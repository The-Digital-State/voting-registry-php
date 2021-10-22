<?php


namespace App\Http\Api;


use Illuminate\Support\Facades\Http;

class Azure
{
    public static function getProfile($accessToken)
    {
        return Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->get('https://graph.microsoft.com/v1.0/me');
    }
}
