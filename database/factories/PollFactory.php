<?php

namespace Database\Factories;

use App\Models\Poll;
use Illuminate\Database\Eloquent\Factories\Factory;

class PollFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Poll::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws \Exception
     */
    public function definition(): array
    {
        $options = [];
        $optionsCount = random_int(3, 6);
        for ($i = 1; $i < $optionsCount; $i++) {
            $options[] = $this->faker->sentence;
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

    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => $this->faker->dateTime(),
                'start' => $this->faker->dateTimeBetween('now', '+5 days'),
                'end' => $this->faker->dateTimeBetween('+5 days', '+10 days'),
            ];
        });
    }
}
