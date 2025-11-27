<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class StockController extends Controller
{
    /**
     * 在庫集計取得
     */
    public function index(Request $request)
    {
        $type = $request->zaikoType ?? 'hinban';
        $query = DB::table('stocks')
            ->join('hinbans', 'stocks.hinban_id', 'hinbans.id')
            ->join('units', 'hinbans.unit_id', 'units.id')
            ->join('shops', 'stocks.shop_id', 'shops.id')
            ->join('brands', 'hinbans.brand_id', 'brands.id')
            ->join('companies', 'shops.company_id', 'companies.id');


        // 条件絞り込み
        if($request->company_id) $query->where('company_id', $request->company_id);
        if($request->shop_id) $query->where('shop_id', $request->shop_id);
        if($request->brand_id) $query->where('brand_id', $request->brand_id);
        if($request->season_id) $query->where('season_id', $request->season_id);
        if($request->unit_id) $query->where('unit_id', $request->unit_id);
        if($request->face) $query->where('face', $request->face);
        if($request->designer_id) $query->where('designer_id', $request->designer_id);
        if($request->year_code) $query->where('year_code', $request->year_code);

        // 集計
        switch($type) {
            case 'co':
                $query->select('company_id as id','co_name as name',
                    DB::raw('SUM(pcs) as stock_total'),
                    DB::raw('SUM(zaikogaku) as stock_zaikogaku_total'),
                    DB::raw('SUM(cost*pcs) as stock_cost_total')
                    )
                    ->groupBy('id','name');
                break;
            case 'shop':
                $query->select('shop_id as id','shop_name as name',
                    DB::raw('SUM(pcs) as stock_total'),
                    DB::raw('SUM(zaikogaku) as stock_zaikogaku_total'),
                    DB::raw('SUM(cost*pcs) as stock_cost_total')
                    )
                    ->groupBy('id','name');
                break;
            case 'brand':
                $query->select('brand_id as id','brand_name as name',
                    DB::raw('SUM(pcs) as stock_total'),
                    DB::raw('SUM(zaikogaku) as stock_zaikogaku_total'),
                    DB::raw('SUM(cost*pcs) as stock_cost_total')
                    )
                    ->groupBy('id','name');
                break;
            case 'season':
                $query->select('season_id as id','season_name as name',
                    DB::raw('SUM(pcs) as stock_total'),
                    DB::raw('SUM(zaikogaku) as stock_zaikogaku_total'),
                    DB::raw('SUM(cost*pcs) as stock_cost_total')
                    )
                    ->groupBy('id','name');
                break;
            case 'unit':
                $query->select('unit_id as id','unit_id as name',
                    DB::raw('SUM(pcs) as stock_total'),
                    DB::raw('SUM(zaikogaku) as stock_zaikogaku_total'),
                    DB::raw('SUM(cost*pcs) as stock_cost_total')
                    )
                    ->groupBy('id','name');
                break;
            case 'face':
                $query->select('face as id','face as name',
                    DB::raw('SUM(pcs) as stock_total'),
                    DB::raw('SUM(zaikogaku) as stock_zaikogaku_total'),
                    DB::raw('SUM(cost*pcs) as stock_cost_total')
                    )
                    ->groupBy('id','name');
                break;
            case 'hinban':
            default:
                $query->select('hinban_id as id','hinban_name as name',
                    DB::raw('SUM(pcs) as stock_total'),
                    DB::raw('SUM(zaikogaku) as stock_zaikogaku_total'),
                    DB::raw('SUM(cost*pcs) as stock_cost_total')
                    )
                    ->groupBy('id','name');
                break;
        }

        // 在庫0以下を除外
        // $query->havingRaw('SUM(stock_total) > 0');

        $result = $query->get();

        return response()->json([
            'data' => $result
        ]);
    }
}
