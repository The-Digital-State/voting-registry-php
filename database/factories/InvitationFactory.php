<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvitationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invitation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $pollId = $this->faker->numberBetween(1, Poll::count());

        return [
            'token' => bin2hex(random_bytes(16)),
            'email' => $this->faker->unique()->safeEmail,
            'voted_at' => $this->faker->boolean ? new \DateTime() : null,
            'poll_id' => $pollId
        ];
    }
}
