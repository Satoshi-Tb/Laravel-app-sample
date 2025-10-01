<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $todos = [];

        $user = DB::table('users')->first();

        foreach(range(0, 4) as $_) {

            $todos[] = [
                'title' => fake()->realText(10),
                'memo' => fake()->boolean() ? fake()->realText(30) : null,
                'deadline' => fake()->dateTimeBetween('now', '+10 days'),
                'color' => null,
                'done' => fake()->boolean(),
                'user_id' => $user->id,
            ];
        }

        DB::table('todos')->insert($todos);
    }
}
