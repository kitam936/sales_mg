<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('depts')->insert([[
            'id' => 1,
            'dept_name' => '管理',
            'dept_info' => '管理者',
        ],
        [
            'id' => 2,
            'dept_name' => '営業',
            'dept_info' => '営業部',
        ],
        [
            'id' => 3,
            'dept_name' => '商品',
            'dept_info' => '商品部',
        ],
        [
            'id' => 4,
            'dept_name' => '経理・総務',
            'dept_info' => '経理部・総務部',
        ],
        [
            'id' => 9,
            'dept_name' => 'その他',
            'dept_info' => 'その他',
        ],

    ]);
    }
}
