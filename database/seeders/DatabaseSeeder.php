<?php

namespace Database\Seeders;

use App\Models\EmailsList;
use App\Models\Poll;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->count(10)
            ->has(EmailsList::factory()->count(5))
            ->has(Poll::factory()->count(2))
            ->create();
    }
}
