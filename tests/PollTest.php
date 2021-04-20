<?php

use App\Models\EmailsList;
use App\Models\Poll;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class PollTest extends TestCase
{
    public function testGetAllPolls(): void
    {
        $user = User::factory()
            ->has(Poll::factory()
                ->has(EmailsList::factory()->count(5))
                ->count(3))
            ->create();

        $token = JWTAuth::fromUser($user);
        $this->get("/polls", [
            'Authorization' => "Bearer $token"
        ]);

        $this->response
            ->assertStatus(200)
            ->assertJsonStructure([[
                'id',
                'title',
                'startDate',
                'endDate',
                'emailListTitle',
                'status',
            ]])
            ->assertJsonCount(3);
    }

    public function testGetPollsEmpty(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);
        $this->get("/polls", [
            'Authorization' => "Bearer $token"
        ]);

        $this->response
            ->assertStatus(200)
            ->assertExactJson([]);
    }

    public function testGetPollData(): void
    {
        $user = User::factory()
            ->has(Poll::factory()
                ->has(EmailsList::factory()->count(5))
                ->count(1))
            ->create();

        $token = JWTAuth::fromUser($user);

        $poll = $user->polls->first();

        $this->get("/poll/{$poll->id}", [
            'Authorization' => "Bearer $token"
        ]);

        $this->response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'shortDescription',
                'startDate',
                'endDate',
                'question',
                'emailListId',
                'status',
            ]);
    }

    public function testGetPollNotFound(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $this->get("/poll/666666", [
            'Authorization' => "Bearer $token"
        ]);

        $this->response->assertStatus(404);
    }

    public function testCreatePollDraft(): void
    {
        $user = User::factory()
            ->has(EmailsList::factory()->count(2))
            ->create();

        $token = JWTAuth::fromUser($user);

        $emailList = $user->emailsLists->first();

        $this->post("/poll/draft", [
            'title' => 'Test Title',
            'description' => 'Test Description',
            'shortDescription' => 'Test Short Description',
            'startDate' => null,
            'endDate' => null,
            'emailListId' => $emailList->id,
            'question' => [
                'title' => 'Test Question',
                'options' => [
                    [
                        'option_index' => 1,
                        'option' => 'Test Answer 1'
                    ],
                    [
                        'option_index' => 2,
                        'option' => 'Test Answer 2'
                    ]
                ]
            ]
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $this->response
            ->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'shortDescription',
                'startDate',
                'endDate',
                'question',
                'emailListId',
                'status',
            ]);
    }

    public function testCreateEmailListWithValidationError(): void
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $this->post("/poll/draft", [
            'description' => 'Test Description',
            'shortDescription' => 'Test Short Description',
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

    public function testUpdatePoll(): void
    {
        $user = User::factory()
            ->has(Poll::factory()->count(3))
            ->has(EmailsList::factory()->count(2))
            ->create();

        $token = JWTAuth::fromUser($user);

        $emailList = $user->emailsLists->first();
        $poll = $user->polls->first();

        $this->put("/poll/draft/{$poll->id}", [
            'title' => 'Test Title 2',
            'description' => 'Test Description',
            'shortDescription' => 'Test Short Description',
            'startDate' => null,
            'endDate' => null,
            'emailListId' => $emailList->id,
            'question' => [
                'title' => 'Test Question',
                'options' => [
                    [
                        'option_index' => 1,
                        'option' => 'Test Answer 1'
                    ],
                    [
                        'option_index' => 2,
                        'option' => 'Test Answer 2'
                    ]
                ]
            ]
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $this->response
            ->assertStatus(201)
            ->assertExactJson([
                'id' => $poll->id,
                'title' => 'Test Title 2',
                'description' => 'Test Description',
                'shortDescription' => 'Test Short Description',
                'startDate' => null,
                'endDate' => null,
                'emailListId' => $emailList->id,
                'question' => [
                    'title' => 'Test Question',
                    'options' => [
                        [
                            'option_index' => 1,
                            'option' => 'Test Answer 1'
                        ],
                        [
                            'option_index' => 2,
                            'option' => 'Test Answer 2'
                        ]
                    ]
                ],
                'status' => 'draft'
            ]);
    }

    public function testDeletePoll(): void
    {
        $user = User::factory()
            ->has(Poll::factory()->count(3))
            ->create();

        $token = JWTAuth::fromUser($user);

        $poll = $user->polls->first();

        $this->delete("/poll/draft/{$poll->id}", [], [
            'Authorization' => "Bearer $token"
        ]);
        $this->response->assertStatus(200);

        $this->get("/poll/{$poll->id}", [
            'Authorization' => "Bearer $token"
        ]);
        $this->response->assertStatus(404);
    }
}
