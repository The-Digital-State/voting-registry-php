<?php

namespace App\Http\Controllers;

use App\Http\Api\Azure;
use App\Http\Resources\UserResource;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Voter;
use Exception;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(): \Illuminate\Http\JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            abort(401, 'Unauthorized');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get a JWT via given invitation token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginByInvitation(): \Illuminate\Http\JsonResponse
    {

        $invitation = Invitation::whereToken(request('token'))->first();

        if (!$invitation) {
            abort(401, 'Unauthorized');
        }

        $user = User::whereEmail($invitation->email)->firstOrNew(['email' => $invitation->email]);

        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }

        if ($user->doesntExist() || $user->isDirty()) {
            $user->save();
        }

        return $this->respondWithToken(auth()->login($user));
    }

    /**
     * Get a JWT via given azure access token
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginByAzure(): \Illuminate\Http\JsonResponse
    {
        $accessToken = request('accessToken');

        if (!$accessToken) {
            abort(401, 'Unauthorized');
        }

        try {
            $response = Azure::getProfile($accessToken);
            $response->throw();

            $profile = $response->json();

            $user = User::whereEmail($profile['userPrincipalName'])
                ->firstOrNew(['email' => $profile['userPrincipalName']]);

            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
            }

            if ($user->doesntExist() || $user->isDirty()) {
                $user->save();
            }

            return $this->respondWithToken(auth()->login($user));
        } catch (Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return UserResource
     */
    public function me(): UserResource
    {
        return new UserResource(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): \Illuminate\Http\JsonResponse
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(): \Illuminate\Http\JsonResponse
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken(string $token): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'token' => $token,
            'tokenType' => 'bearer',
            'expiresIn' => auth()->factory()->getTTL() * 60
        ]);
    }
}
