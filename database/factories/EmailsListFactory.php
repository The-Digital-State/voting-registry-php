<?php

namespace Database\Factories;

use App\Models\EmailsList;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailsListFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailsList::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $emails = [];
        $emailsCount = $this->faker->numberBetween(5, 50);

        for ($i = 0; $i < $emailsCount; $i++) {
            $emails[] = $this->faker->unique()->safeEmail;
        }

        return [
            'title' => $this->faker->sentence,
            'emails' => $emails,
        ];
    }
}
