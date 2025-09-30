<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        DB::table('users')->insert([
        [
            'id' => 1,
            'name' => '北村',
            'email' => 'kitamura@dijon.co.jp',
            'password' => Hash::make('tk9521dj'),
            'role_id' => 1,
            'dept_id' => 1,
            'mailService' => 1
        ],
        [
            'id' => 6,
            'name' => '上坂',
            'email' => 'uesaka@dijon.co.jp',
            'password' => Hash::make('ku5783dj'),
            'role_id' => 2,
            'dept_id' => 2,
            'mailService' => 0
        ],
        [
            'id' => 7,
            'name' => '山口',
            'email' => 'yamaguchi@dijon.co.jp',
            'password' => Hash::make('ym3172dj'),
            'role_id' => 7,
            'dept_id' => 2,
            'mailService' => 0
        ],
        [
            'id' => 9,
            'name' => '村山',
            'email' => 'murayama@dijon.co.jp',
            'password' => Hash::make('im4278dj'),
            'role_id' => 9,
            'dept_id' => 3,
            'mailService' => 0
        ],

        [
            'id' => 12,
            'name' => '発知',
            'email' => 'hocchi@dijon.co.jp',
            'password' => Hash::make('dh9409dj'),
            'role_id' => 5,
            'dept_id' => 2,
            'mailService' => 0
        ],
        [
            'id' => 13,
            'name' => '門田',
            'email' => 'kasahara@dijon.co.jp',
            'password' => Hash::make('mk8778dj'),
            'role_id' => 2,
            'dept_id' => 2,
            'mailService' => 0
        ],
        [
            'id' => 99,
            'name' => 'その他',
            'email' => 'info@dijon.co.jp',
            'password' => Hash::make('me2988dj'),
            'role_id' => 9,
            'dept_id' => 9,
            'mailService' => 0
        ],


    ]);
    }
}
