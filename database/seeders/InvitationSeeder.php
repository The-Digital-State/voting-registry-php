<?php

namespace Database\Seeders;

use App\Models\Invitation;
use App\Models\Poll;
use Illuminate\Database\Seeder;

class InvitationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $polls = Poll::whereNotNull('published_at')->get();

        foreach ($polls as $poll){
            foreach ($poll->emailsList->emails as $email){
                Invitation::factory()->for($poll)->create([
                    'email' => $email,
                ]);
            }
        }
    }
}
