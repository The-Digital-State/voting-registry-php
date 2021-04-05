<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models;

class AuthController extends Controller
{
    public function getJwt(string $invitationToken): JsonResponse
    {
        $invitation = Models\Invitation::where('token', $invitationToken)->first();
        if (!$invitation) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Models\User::where('email', $invitation->email)->firstOr(function () use ($invitation) {
            return Models\User::create([
                'email' => $invitation->email,
                'active' => true,
            ]);
        });

        $jwtAccessToken = Auth::login($user);

        return response()->json([
            'token' => $jwtAccessToken,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    public function logoutJwt()
    {
        auth()->invalidate();

        return response('');
    }
}
