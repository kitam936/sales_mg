<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesDigestController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->shoukaType ?? 'hinban';

        /*
        |--------------------------------------------------------------------------
        | 集計軸の設定
        |--------------------------------------------------------------------------
        */
        switch ($type) {
            case 'brand':
                $groupField = 'hinbans.brand_id';
                $nameField  = 'brands.brand_name';
                break;

            case 'season':
                $groupField = 'units.season_id';
                $nameField  = 'units.season_name';
                break;

            case 'unit':
                $groupField = 'hinbans.unit_id';
                $nameField  = 'hinbans.unit_id';
                break;

            case 'face':
                $groupField = 'faces.id';
                $nameField  = 'faces.face_code';
                break;

            case 'designer':
                $groupField = 'hinbans.designer_id';
                $nameField  = 'designers.designer_name';
                break;

            default: // hinban
                $groupField = 'hinbans.id';
                $nameField  = 'hinbans.hinban_name';
        }

        /*
        |--------------------------------------------------------------------------
        | shop 絞り込み
        |--------------------------------------------------------------------------
        */
        $shopIds = null;

        if ($request->filled('shop_id')) {
            $shopIds = [$request->shop_id];
        } elseif ($request->filled('company_id')) {
            $shopIds = DB::table('shops')
                ->where('company_id', $request->company_id)
                ->pluck('id')
                ->toArray();
        }

        /*
        |--------------------------------------------------------------------------
        | sales 集計サブクエリ
        |--------------------------------------------------------------------------
        */
        $salesSub = DB::table('sales')
            ->join('shops', 'sales.shop_id', '=', 'shops.id') //追記
            ->where('shops.company_id', '>', 1000) //GMSのみ
            ->select('hinban_id', DB::raw('SUM(pcs) as sales_total'))
            ->when($shopIds, fn ($q) => $q->whereIn('shop_id', $shopIds))
            ->groupBy('hinban_id');

        /*
        |--------------------------------------------------------------------------
        | stock 集計サブクエリ
        |--------------------------------------------------------------------------
        */
        $stockSub = DB::table('stocks')
            ->select('hinban_id', DB::raw('SUM(pcs) as stock_total'))
            ->when($shopIds, fn ($q) => $q->whereIn('shop_id', $shopIds))
            ->groupBy('hinban_id');

        /*
        |--------------------------------------------------------------------------
        | メインクエリ
        |--------------------------------------------------------------------------
        */
        $query = DB::table('hinbans')
            // units は必須
            ->join('units', 'hinbans.unit_id', '=', 'units.id')

            // 条件付き JOIN
            ->when($type === 'face',
                fn ($q) => $q->leftJoin('faces', 'hinbans.face', '=', 'faces.face_code')
            )
            ->when($type === 'designer',
                fn ($q) => $q->leftJoin('designers', 'hinbans.designer_id', '=', 'designers.id')
            )
            ->when($type === 'brand',
                fn ($q) => $q->leftJoin('brands', 'hinbans.brand_id', '=', 'brands.id')
            )

            // 集計 JOIN
            ->leftJoinSub($salesSub, 'sales_total_sub',
                fn ($join) => $join->on('hinbans.id', '=', 'sales_total_sub.hinban_id')
            )
            ->leftJoinSub($stockSub, 'stock_total_sub',
                fn ($join) => $join->on('hinbans.id', '=', 'stock_total_sub.hinban_id')
            )

            // select
            ->select(
                DB::raw("$groupField as id"),
                DB::raw("$nameField as name"),
                DB::raw('COALESCE(MAX(sales_total_sub.sales_total),0) as sales_total'),
                DB::raw('COALESCE(MAX(stock_total_sub.stock_total),0) as stock_total'),
                DB::raw('CASE
                    WHEN COALESCE(MAX(sales_total_sub.sales_total),0)
                       + COALESCE(MAX(stock_total_sub.stock_total),0) = 0
                    THEN 0
                    ELSE ROUND(
                        COALESCE(MAX(sales_total_sub.sales_total),0) /
                        (COALESCE(MAX(sales_total_sub.sales_total),0)
                        + COALESCE(MAX(stock_total_sub.stock_total),0)) * 100,
                        1
                    )
                END as rate')
            )
            ->groupBy($groupField, $nameField)

            // ->when($type === 'hinban', fn($q) =>
            //     $q->havingRaw('(COALESCE(MAX(sales_total_sub.sales_total),0) + COALESCE(MAX(stock_total_sub.stock_total),0)) > 0')
            // )

            ->when($type === 'hinban', fn($q) =>
                $q->havingRaw('COALESCE(MAX(sales_total_sub.sales_total),0)>0 && COALESCE(MAX(stock_total_sub.stock_total),0)>= 0')
            )

            /*
            |--------------------------------------------------------------------------
            | フィルタ条件
            | ※ 集計軸と同じ項目は除外する
            |--------------------------------------------------------------------------
            */
            ->when($request->filled('year_code'),
                fn ($q) => $q->where('hinbans.year_code', $request->year_code)
            )
            ->when(
                $request->filled('brand_id') && $type !== 'brand',
                fn ($q) => $q->where('hinbans.brand_id', $request->brand_id)
            )
            ->when(
                $request->filled('season_id') && $type !== 'season',
                fn ($q) => $q->where('units.season_id', $request->season_id)
            )
            ->when(
                $request->filled('unit_id') && $type !== 'unit',
                fn ($q) => $q->where('units.id', $request->unit_id)
            )
            ->when(
                $request->filled('face') && $type !== 'face',
                fn ($q) => $q->where('hinbans.face', $request->face)
            )
            ->when(
                $request->filled('designer_id') && $type !== 'designer',
                fn ($q) => $q->where('hinbans.designer_id', $request->designer_id)
            );

        /*
        |--------------------------------------------------------------------------
        | 並び順
        |--------------------------------------------------------------------------
        */
        if ($type === 'hinban') {
            $query
                ->where('hinbans.vendor_id', '<>', 8200)
                ->orderByDesc('rate');
        } else {
            $query->orderBy('id');
        }

        /*
        |--------------------------------------------------------------------------
        | ページネーション
        |--------------------------------------------------------------------------
        */
        $data = $query->paginate(50)->withQueryString();

        return response()->json($data);
    }

}





