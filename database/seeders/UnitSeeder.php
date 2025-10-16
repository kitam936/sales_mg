<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('units')->insert([
            [
            'id' => 1,
            'unit_code' => '101',
            'season_id' => 10,
            'season_name' => '春',
            ],
            [
            'id' => 2,
            'unit_code' => '102',
            'season_id' => 10,
            'season_name' => '春',
            ],
            [
            'id' => 3,
            'unit_code' => '103',
            'season_id' => 12,
            'season_name' => '夏',
            ],
            [
            'id' => 4,
            'unit_code' => '104',
            'season_id' => 2,
            'season_name' => '夏',
            ],
            [
            'id' => 5,
            'unit_code' => '105',
            'season_id' => 2,
            'season_name' => '夏',
            ],
            [
            'id' => 6,
            'unit_code' => '106',
            'season_id' => 2,
            'season_name' => '夏',
            ],
            [
            'id' => 7,
            'unit_code' => '107',
            'season_id' => 6,
            'season_name' => '秋',
            ],
            [
            'id' => 8,
            'unit_code' => '108',
            'season_id' => 6,
            'season_name' => '秋',
            ],
            [
            'id' => 9,
            'unit_code' => '109',
            'season_id' => 8,
            'season_name' => '冬',
            ],
            [
            'id' => 10,
             'unit_code' => '110',
            'season_id' => 8,
            'season_name' => '冬',
            ],
            [
            'id' => 11,
            'unit_code' => '111',
            'season_id' => 8,
            'season_name' => '冬',
            ],
            [
            'id' => 12,
            'unit_code' => '112',
            'season_id' => 8,
            'season_name' => '冬',
            ],

        ]);
    }
}
