<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('designers')->insert([[
            'id' => 1,
            'designer_name' => 'A',
            'designer_info' => 'A',
        ],
        [
            'id' => 2,
            'designer_name' => 'B',
            'designer_info' => 'B',
        ],
        [
            'id' => 3,
            'designer_name' => 'C',
            'designer_info' => 'C',
        ],
        [
            'id' => 4,
            'designer_name' => 'D',
            'designer_info' => 'D',
        ],
        [
            'id' => 5,
            'designer_name' => 'E',
            'designer_info' => 'E',
        ],
        [
            'id' => 6,
            'designer_name' => 'F',
            'designer_info' => 'F',
        ],
        [
            'id' => 7,
            'designer_name' => 'G',
            'designer_info' => 'G',
        ],
        [
            'id' => 8,
            'designer_name' => 'H',
            'designer_info' => 'H',
        ],
        [
            'id' => 9,
            'designer_name' => 'I',
            'designer_info' => 'I',
        ],
        [
            'id' => 10,
            'designer_name' => 'J',
            'designer_info' => 'J',
        ],

        [
            'id' => 99,
            'designer_name' => 'その他',
            'designer_info' => 'その他',
        ],

    ]);
    }
}
