<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('shops')->insert([
            [
            'id' =>1104,
            'company_id' => 1100,
            'shop_name' => '西友荻窪店',
            'shop_info' => '',
            'area_id' => 4,
            'is_selling' => 1,
            ],
            [
            'id' => 5201,
            'company_id' => 5200,
            'shop_name' => 'IYC船橋店',
            'shop_info' => '',
            'area_id' => 4,
            'is_selling' => 1,
            ],
            [
            'id' => 5301,
            'company_id' => 5300,
            'shop_name' => 'アピタ高崎店',
            'shop_info' => '',
            'area_id' => 4,
            'is_selling' => 1,
            ],
            [
            'id' => 5202,
            'company_id' => 5200,
            'shop_name' => 'IYC小田原店',
            'shop_info' => '',
            'area_id' => 4,
            'is_selling' => 1,
            ],




        ]);
    }
}
