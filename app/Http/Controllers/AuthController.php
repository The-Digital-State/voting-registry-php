<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Invitation;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function getJwt(string $invitationToken): JsonResponse
    {
        $invitation = Invitation::where('token', $invitationToken)->first();
        if (!$invitation) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $invitation->email)->firstOr(function () use ($invitation) {
            return User::create([
                'email' => $invitation->email,
                'active' => true,
            ]);
        });

        $jwtAccessToken = Auth::claims(['available_poll_id' => $invitation->poll_id])->login($user);

        return response()->json([
            'token' => $jwtAccessToken,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    public function invalidateJwt()
    {
        Auth::invalidate();

        return response('');
    }

    public function loginByAzure(Request $request): JsonResponse
    {
        $this->validate($request, [
            'access_token' => 'required',
        ]);

        try {
            $response = Http::withToken($request->input('access_token'))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->get('https://graph.microsoft.com/v1.0/me');

            $response->throw();

            $profile = $response->json();

            $user = User::where('email', $profile['userPrincipalName'])
                ->firstOr(function () use ($profile) {
                    return User::create([
                        'email' => $profile['userPrincipalName'],
                        'active' => true,
                    ]);
                });

            $jwtAccessToken = Auth::login($user);

            return response()->json([
                'token' => $jwtAccessToken,
                'token_type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 60
            ]);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }
    }
}
