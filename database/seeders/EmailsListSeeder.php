<?php

namespace Database\Seeders;

use App\Models\EmailsList;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmailsListSeeder extends Seeder
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
            EmailsList::factory(5)->for($user, 'owner')->create();
        }
    }
}
