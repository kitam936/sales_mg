<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HinbanAnalysisController extends Controller
{
    /**
     * 全データ取得（基本情報 + 週次 + 月次）
     */
    public function show($id)
    {
        // --- 基本情報 ---
        $hinban = DB::table('hinbans')
            ->leftJoin('brands', 'hinbans.brand_id', '=', 'brands.id')
            ->leftJoin('units', 'hinbans.unit_id', '=', 'units.id')
            ->leftJoin('faces', 'hinbans.face', '=', 'faces.face_code')
            ->leftJoin('images', 'hinbans.id', '=', 'images.hinban_id')
            ->select(
                'hinbans.id as hinban_id',
                'hinbans.hinban_name as name',
                'hinbans.m_price as price',
                'hinbans.unit_id',
                'images.filename as thumbnail_url',
                'brands.brand_name',
                'units.season_name',
                'faces.face_code'
            )
            ->where('hinbans.id', $id)
            ->first();

        $skus = DB::table('skus')
            ->leftjoin('sku_images','skus.id','sku_images.sku_id')
            ->join('sizes','skus.size_id','sizes.id')
            ->join('hinbans','hinbans.id','skus.hinban_id')
            ->where('skus.hinban_id',($id))
            ->where('skus.col_id','<>',99)
            ->select('skus.id as id','sku_images.sku_id','skus.col_id','sku_images.filename','sizes.size_name')
            ->get();

        // --- 週次 ---
        // $weekly = DB::table('sales')
        //     ->selectRaw("YEARWEEK(sales_date, 1) AS yw, SUM(pcs) AS total")
        //     ->where('hinban_id', $id)
        //     ->groupBy(DB::raw("YEARWEEK(sales_date, 1)"))
        //     ->orderBy('yw')
        //     ->limit(26)
        //     ->get();
        $weekly = DB::table('sales')
            ->selectRaw("
                DATE_FORMAT(MAX(sales_date),'%y/%m/%d') AS yw,
                SUM(pcs) AS total
            ")
            ->where('hinban_id', $id)
            ->groupBy(DB::raw("YEARWEEK(sales_date, 1)"))
            ->orderBy('yw')
            ->limit(26)
            ->get();

        // --- 月次 ---
        $monthly = DB::table('sales')
            ->selectRaw("DATE_FORMAT(sales_date, '%Y/%m') AS ym, SUM(pcs) AS total")
            ->where('hinban_id', $id)
            ->groupBy(DB::raw("DATE_FORMAT(sales_date, '%Y/%m')"))
            ->orderBy('ym')
            ->limit(12)
            ->get();

        return response()->json([
            'hinban'  => $hinban,
            'skus'    => $skus,
            'weekly'  => $weekly,
            'monthly' => $monthly,
        ]);
    }

    public function getSalesTrend($hinban_id)
    {
        $weekly = DB::table('sales')
        ->selectRaw("
            DATE_FORMAT(MAX(sales_date),'%y/%m/%d') AS week_end,
            SUM(pcs) AS total
        ")
        ->where('hinban_id', $hinban_id)
        ->groupBy(DB::raw("YEARWEEK(sales_date, 1)"))
        ->orderBy('yw')
        ->limit(26)
        ->get();

        $monthly = DB::table('sales')
            ->selectRaw("DATE_FORMAT(sales_date, '%y/%m') AS ym, SUM(pcs) AS total")
            ->where('hinban_id', $hinban_id)
            ->groupBy(DB::raw("DATE_FORMAT(sales_date, '%y/%m')"))
            ->orderBy('ym')
            ->limit(12)
            ->get();

        return response()->json([
            'weekly' => $weekly,
            'monthly' => $monthly,
        ]);
    }

}

