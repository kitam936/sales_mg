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

        // 集計軸
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
                $nameField  = 'hinbans.id';
        }

        // サブクエリ：売上
        $salesSub = DB::table('sales')
            ->select('hinban_id', DB::raw('SUM(pcs) as sales_total'))
            ->when($request->company_id, fn($q) => $q->whereIn('shop_id', function($q2) use ($request) {
                $q2->select('id')->from('shops')->where('company_id', $request->company_id);
            }))
            ->when($request->shop_id, fn($q) => $q->where('shop_id', $request->shop_id))
            ->groupBy('hinban_id');

        // サブクエリ：在庫
        $stockSub = DB::table('stocks')
            ->select('hinban_id', DB::raw('SUM(pcs) as stock_total'))
            ->groupBy('hinban_id');

        // メインクエリ
        $query = DB::table('hinbans')
            ->leftJoin('units', 'hinbans.unit_id', '=', 'units.id')
            ->leftJoin('faces', 'hinbans.face', '=', 'faces.face_code')
            ->leftJoin('designers', 'hinbans.designer_id', '=', 'designers.id')
            ->leftJoin('brands', 'hinbans.brand_id', '=', 'brands.id')
            ->leftJoinSub($salesSub, 'sales_total_sub', function($join) {
                $join->on('hinbans.id', '=', 'sales_total_sub.hinban_id');
            })
            ->leftJoinSub($stockSub, 'stock_total_sub', function($join) {
                $join->on('hinbans.id', '=', 'stock_total_sub.hinban_id');
            })
            ->select(
                DB::raw("$groupField as id"),
                DB::raw("$nameField as name"), // MAXは不要
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
            ->havingRaw('COALESCE(MAX(stock_total_sub.stock_total),0) > 0');

        // その他の絞り込み（外側でJOINされているテーブルに対して）
        if ($request->filled('brand_id')) $query->where('hinbans.brand_id', $request->brand_id);
        if ($request->filled('season_id')) $query->where('units.season_id', $request->season_id);
        if ($request->filled('unit_id')) $query->where('units.id', $request->unit_id);
        if ($request->filled('face')) $query->where('faces.id', $request->face);
        if ($request->filled('designer_id')) $query->where('designers.id', $request->designer_id);

        // ★ 年度コードによる絞り込み追加
        if ($request->filled('year_code')) {
            $query->where('hinbans.year_code', $request->year_code);
        }

        // 並び替え
       // 並び替え
if ($type === 'hinban') {
    $query->orderBy('rate', 'desc'); // rate 列を使う
        } else {
            // それ以外：名前順
            $query->orderBy('id');
        }

        $data = $query->paginate(50)->withQueryString();

        return response()->json($data);
    }
}

