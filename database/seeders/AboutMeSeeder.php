<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AboutMeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('about_mes')->insert([

                'key' => 'name',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'designation',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'about_me',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'd_o_b',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'nationality',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'address',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'phone',
        ]);
        DB::table('about_mes')->insert([

                'key' => ' blood_group',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'email',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'website',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'facebook',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'twitter',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'linkedin',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'youtube',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'github',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'skype',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'user_image',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'resume_link',
        ]);
        DB::table('about_mes')->insert([

                'key' => 'contact_me',
        ]);
    }
}
