<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = [
           'name' => 'test_user',
           'email' => env('TEST_USER_MAIL'),
           'password' => env('TEST_USER_PASSWORD'),
        ];

        DB::table('users')->insert($user);
    }
}
