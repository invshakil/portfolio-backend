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
            'name' => 'SuperTrump',
            'email' => 'portfolioAdmin@gmail.com',
            'password' => bcrypt('Civediamo22'),
            'role' => 1,
        ];
        User::create($user);
    }
}
