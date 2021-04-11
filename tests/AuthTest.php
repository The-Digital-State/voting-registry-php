<?php

use App\Models\Invitation;

class AuthTest extends TestCase
{
    public function testGetJwtToken(): void
    {
        $invitation = Invitation::factory()->create();

        $this->get("/auth/jwt/{$invitation->token}");

        $this->response
            ->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'token_type',
                'expires_in',
            ]);
    }

    public function testGetJwtTokenUnauthorized(): void
    {
        // make an invitation without saving in database
        $invitation = Invitation::factory()->make();

        $this->get("/auth/jwt/{$invitation->token}");

        $this->response
            ->assertStatus(401)
            ->assertSeeText('Unauthorized');
    }
}
