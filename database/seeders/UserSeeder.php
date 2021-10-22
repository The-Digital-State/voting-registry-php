<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'Pogonya';
        $user->email = 'pogonya2020@protonmail.com';
        $user->email_verified_at = now();
        $user->password = bcrypt('pogonya');
        $user->save();
    }
}
