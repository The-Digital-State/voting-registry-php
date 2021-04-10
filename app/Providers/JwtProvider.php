<?php

namespace App\Providers;

use App\Models;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class JwtProvider implements UserProvider
{
    public function retrieveById($identifier)
    {
        return Models\User::find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        // TODO: Implement retrieveByToken() method.
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // TODO: Implement updateRememberToken() method.
    }

    public function retrieveByCredentials(array $credentials)
    {
        // TODO: Implement retrieveByCredentials() method.
    }

    public function validateCredentials($user, array $credentials): bool
    {
        // TODO: Implement validateCredentials() method.
    }
}