<?php

namespace Database\Factories;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollFactory extends Factory
{
    protected $model = Poll::class;

    public function definition(): array
    {
        $options = [];
        $optionsCount = random_int(3, 6);
        for($i = 1; $i < $optionsCount; $i++){
            $options[] = [
                'option_index' => $i,
                'option' => $this->faker->word(),
            ];
        }

        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph(8, true),
            'short_description' => $this->faker->paragraph(3, true),
            'question' => [
                'title' => $this->faker->sentence . '?',
                'options' => $options
            ],
        ];
    }
}
