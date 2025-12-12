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

        // 集計軸の設定
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
            default:
                $groupField = 'hinbans.id';
                $nameField  = 'hinbans.hinban_name';
        }

        // shop_id 絞り込み（← 修正済み）
        $shopIds = null;

        // ① 店舗が選ばれていれば最優先
        if ($request->filled('shop_id')) {
            $shopIds = [$request->shop_id];

        }
        // ② 店舗が未指定で会社が指定されている場合
        elseif ($request->filled('company_id')) {
            $shopIds = DB::table('shops')
                        ->where('company_id', $request->company_id)
                        ->pluck('id')
                        ->toArray();
        }

        // sales 集計サブクエリ
        $salesSub = DB::table('sales')
            ->select('hinban_id', DB::raw('SUM(pcs) as sales_total'))
            ->when($shopIds, fn($q) => $q->whereIn('shop_id', $shopIds))
            ->groupBy('hinban_id');

        // stocks 集計サブクエリ
        $stockSub = DB::table('stocks')
            ->select('hinban_id', DB::raw('SUM(pcs) as stock_total'))
            ->when($shopIds, fn($q) => $q->whereIn('shop_id', $shopIds))
            ->groupBy('hinban_id');

        // メインクエリ
        $query = DB::table('hinbans')
            // ->where('hinbans.vendor_id', '<>', 8200)
            ->when(in_array($type, ['brand', 'season', 'unit']), fn($q) => $q->leftJoin('units', 'hinbans.unit_id', '=', 'units.id'))
            ->when($type === 'face', fn($q) => $q->leftJoin('faces', 'hinbans.face', '=', 'faces.face_code'))
            ->when($type === 'designer', fn($q) => $q->leftJoin('designers', 'hinbans.designer_id', '=', 'designers.id'))
            ->when($type === 'brand', fn($q) => $q->leftJoin('brands', 'hinbans.brand_id', '=', 'brands.id'))
            ->leftJoinSub($salesSub, 'sales_total_sub', fn($join) => $join->on('hinbans.id', '=', 'sales_total_sub.hinban_id'))
            ->leftJoinSub($stockSub, 'stock_total_sub', fn($join) => $join->on('hinbans.id', '=', 'stock_total_sub.hinban_id'))
            ->select(
                DB::raw("$groupField as id"),
                DB::raw("$nameField as name"),
                DB::raw("COALESCE(MAX(sales_total_sub.sales_total),0) as sales_total"),
                DB::raw("COALESCE(MAX(stock_total_sub.stock_total),0) as stock_total"),
                DB::raw('CASE
                    WHEN COALESCE(MAX(sales_total_sub.sales_total),0) + COALESCE(MAX(stock_total_sub.stock_total),0) = 0 THEN 0
                    ELSE ROUND(
                        COALESCE(MAX(sales_total_sub.sales_total),0) /
                        (COALESCE(MAX(sales_total_sub.sales_total),0) + COALESCE(MAX(stock_total_sub.stock_total),0)) * 100,
                        1
                    )
                END as rate')
            )
            ->groupBy($groupField, $nameField)
            ->havingRaw('COALESCE(MAX(stock_total_sub.stock_total),0) >= 0')
            // フィルタ
            ->when($request->filled('year_code'), fn($q) => $q->where('hinbans.year_code', $request->year_code))
            ->when($request->filled('brand_id'), fn($q) => $q->where('hinbans.brand_id', $request->brand_id))
            ->when($request->filled('season_id'), fn($q) => $q->where('units.season_id', $request->season_id))
            ->when($request->filled('unit_id'), fn($q) => $q->where('units.id', $request->unit_id))
            ->when($request->filled('face'), fn($q) => $q->where('hinbans.face', $request->face))
            ->when($request->filled('designer_id'), fn($q) => $q->where('hinbans.designer_id', $request->designer_id));

        // 並び順
        if ($type === 'hinban') {
            // $query->orderByDesc('rate');
            $query->where('hinbans.vendor_id', '<>', 8200)->orderByDesc('rate');
        } else {
            $query->orderBy('id');
        }

        $data = $query->paginate(50)->withQueryString();

        return response()->json($data);
    }
}





