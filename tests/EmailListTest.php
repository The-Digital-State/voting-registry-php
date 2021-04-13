<?php

use App\Models\EmailsList;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmailListTest extends TestCase
{
    public function testGetAllEmailLists(): void
    {
        $user = User::factory()
            ->has(EmailsList::factory()->count(5))
            ->create();

        $token = JWTAuth::fromUser($user);
        $this->get("/email-lists", [
            'Authorization' => "Bearer $token"
        ]);

        $this->response
            ->assertStatus(200)
            ->assertJsonStructure([[
                'id',
                'title',
                'emailsCount',
            ]])
            ->assertJsonCount(5);
    }

    public function testGetEmailListsEmpty(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);
        $this->get("/email-lists", [
            'Authorization' => "Bearer $token"
        ]);

        $this->response
            ->assertStatus(200)
            ->assertExactJson([]);
    }

    public function testGetEmailListsWithoutToken(): void
    {
        $this->get("/email-lists");

        $this->response
            ->assertStatus(401)
            ->assertSeeText('Unauthorized.');
    }

    public function testGetEmailList(): void
    {
        $user = User::factory()
            ->has(EmailsList::factory()->count(2))
            ->create();

        $token = JWTAuth::fromUser($user);

        $emailList = $user->emailsLists->first();

        $this->get("/email-list/{$emailList->id}", [
            'Authorization' => "Bearer $token"
        ]);

        $this->response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'emails',
            ]);
    }

    public function testGetEmailListNotFound(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $this->get("/email-list/666666", [
            'Authorization' => "Bearer $token"
        ]);

        $this->response->assertStatus(404);
    }

    public function testCreateEmailList(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $this->post("/email-list", [
            'title' => 'Test Email List',
            'emails' => [
                'test@test.com',
                'test2@test.com',
                'test3@test.com',
            ]
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $this->response
            ->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'emails',
            ]);
    }

    public function testCreateEmailListWithValidationError(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $this->post("/email-list", [
            'emails' => [
                'test@test.com',
                'test2@test.com',
                'test3@test.com',
            ]
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $this->response
            ->assertStatus(422)
            ->assertExactJson([
                'title' => [
                    'The title field is required.'
                ],
            ]);
    }

    public function testUpdateEmailList(): void
    {
        $user = User::factory()
            ->has(EmailsList::factory()->count(2))
            ->create();

        $token = JWTAuth::fromUser($user);

        $emailList = $user->emailsLists->first();

        $this->put("/email-list/{$emailList->id}", [
            'title' => 'Test Email List',
            'emails' => [
                'test@test.com',
                'test2@test.com',
                'test3@test.com',
            ]
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $this->response
            ->assertStatus(201)
            ->assertExactJson([
                'id' => $emailList->id,
                'title' => 'Test Email List',
                'emails' => [
                    'test@test.com',
                    'test2@test.com',
                    'test3@test.com',
                ]
            ]);
    }

    public function testDeleteEmailList(): void
    {
        $user = User::factory()
            ->has(EmailsList::factory()->count(2))
            ->create();

        $token = JWTAuth::fromUser($user);

        $emailList = $user->emailsLists->first();

        $this->get("/email-list/{$emailList->id}", [
            'Authorization' => "Bearer $token"
        ]);
        $this->response->assertStatus(200);

        $this->delete("/email-list/{$emailList->id}", [], [
            'Authorization' => "Bearer $token"
        ]);
        $this->response->assertStatus(200);

        $this->get("/email-list/{$emailList->id}", [
            'Authorization' => "Bearer $token"
        ]);
        $this->response->assertStatus(404);
    }
}
