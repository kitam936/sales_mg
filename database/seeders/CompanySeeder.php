<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('companies')->insert([
            [
            'id' => 1100,
            'co_name' => 'Seiyu',
            'co_info' => '西友',
            'pic_id' => 7
            ],
            [
            'id' => 3200,
            'co_name' => 'IY',
            'co_info' => 'イトーヨーカドー',
            'pic_id' => 2
            ],
            [
            'id' => 5200,
            'co_name' => 'IYコンセ',
            'co_info' => 'IYコンセ',
            'pic_id' => 13
            ],
            [
            'id' => 1400,
            'co_name' => 'UNY',
            'co_info' => 'UNY',
            'pic_id' => 12
            ],
            [
            'id' => 5300,
            'co_name' => 'UNYコンセ',
            'co_info' => 'UNYコンセ',
            'pic_id' => 13
            ],



        ]);
    }
}
