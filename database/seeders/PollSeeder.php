<?php

namespace Database\Seeders;

use App\Models\EmailsList;
use App\Models\Poll;
use App\Models\User;
use Illuminate\Database\Seeder;

class PollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            Poll::factory(5)
                ->published()
                ->for($user, 'owner')
                ->for(EmailsList::factory()->for($user, 'owner'))
                ->create();

            Poll::factory(5)
                ->for($user, 'owner')
                ->for(EmailsList::factory()->for($user, 'owner'))
                ->create();
        }
    }
}
