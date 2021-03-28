<?php

namespace Database\Factories;

use App\Models\EmailsList;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailsListFactory extends Factory
{
    protected $model = Emailslist::class;

    public function definition(): array
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
