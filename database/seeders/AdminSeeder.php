<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = [
            'first_name' => 'Alexander',
            'last_name' => 'SuperTrump',
            'gender' => 'm',
            'email' => 'portfolioAdmin@gmail.com',
            'password' => bcrypt('Civediamo22'),
            'role' => 1,
            'email_verified_at' => now()
        ];
        User::create($user);
    }
}
