<?php

namespace Database\Factories;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollFactory extends Factory
{
    protected $model = Poll::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph(8, true),
            'short_description' => $this->faker->paragraph(3, true),
            'question' => [
                'question' => $this->faker->sentence . '?',
                'options' => $this->faker->sentences(rand(2, 6)),
            ],
        ];
    }
}
