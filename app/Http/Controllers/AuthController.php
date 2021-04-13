<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Invitation;
use Laravel\Lumen\Routing\Controller;

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
}
