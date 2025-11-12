<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesData;
use App\Models\Scopes\Subtotal;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Services\DecileService;


class AnalysisController extends Controller
{

    public function index(Request $request)
    {
        $movingAverages = []; // ★ 12ヶ月移動平均用配列
        $movingAveragesProfit = []; // ★ 粗利用12ヶ月移動平均

        // 初期化：必ず定義しておく
        $data = collect();
        $labels = collect();
        $totals = collect();
        $profits = collect();
        // 会社一覧
        $companies = DB::table('shops')
        ->join('companies', 'shops.company_id', '=', 'companies.id')
        ->join('users', 'companies.pic_id', '=', 'users.id')
        ->where('shops.company_id','>', 1000)
        ->where('shops.company_id','<', 8000)
        ->select('companies.id as co_id', 'companies.co_name as co_name')
        ->groupBy('co_id', 'co_name')
        ->orderBy('co_id', 'asc')
        ->get();

        // Shop一覧
        $shops = DB::table('shops')
            ->join('companies', 'shops.company_id', '=', 'companies.id')
            ->where('shops.company_id','>', 1000)
            ->where('shops.company_id','<', 8000)
            ->select('shops.id as shop_id', 'companies.co_name as co_name','shops.shop_name as shop_name')
            ->groupBy('shop_id', 'co_name','shop_name')
            ->orderBy('shop_id', 'asc')
            ->get();

        // Brand一覧
        $brands = DB::table('brands')
            ->select('brands.id as brand_id', 'brands.brand_name as brand_name')
            ->groupBy('brand_id', 'brand_name')
            ->orderBy('brand_id', 'asc')
            ->get();

        // Season一覧
        $seasons = DB::table('units')
            ->select('units.season_id as season_id', 'units.season_name as season_name')
            ->groupBy('season_id', 'season_name')
            ->orderBy('season_id', 'asc')
            ->get();

        // Unit一覧
        $units = DB::table('units')
            ->select('units.unit_code','units.id as unit_id')
            ->groupBy('unit_id', 'unit_code')
            ->orderBy('unit_code', 'asc')
            ->get();

        // face一覧
        $faces = DB::table('faces')
            ->select('faces.id as face_id', 'faces.face_code as face_code','faces.face_item as face_item')
            ->groupBy('face_id', 'face_code','face_item')
            ->orderBy('face_id', 'asc')
            ->get();

        // Designer一覧
        $designers = DB::table('designers')
            ->select('designers.id as designer_id', 'designers.designer_name as designer_name')
            ->groupBy('designer_id', 'designer_name')
            ->orderBy('designer_id', 'asc')
            ->get();

        // 担当者一覧
        $pics = DB::table('users')
            ->where('users.dept_id', 2)
            ->select('users.id as pic_id', 'users.name as pic_name')
            ->groupBy('pic_id', 'pic_name')
            ->orderBy('pic_id', 'asc')
            ->get();

        // 日付範囲指定
        $subQuery = SalesData::betweenDate($request->startDate, $request->endDate);

        if ($request->type === 'co_total') {

            $subQuery ->groupBy('company_id','co_name')
            ->selectRaw('company_id,co_name, sum(kingaku) as total, sum(arari) as total_profit')
            ->orderBy('total', 'desc');

            $data = DB::table($subQuery)->get();

            $labels = $data->pluck('co_name');
            $totals = $data->pluck('total');
            $profits = $data->pluck('total_profit');

        }

        if ($request->type === 'sh_total') {

            $subQuery->groupBy('shop_id','shop_name')
                ->selectRaw('shop_id,shop_name, sum(kingaku) as total, sum(arari) as total_profit');

            // $data = DB::table($subQuery)
            //     ->groupBy('shop_id','shop_name')
            //     ->select('shop_id','shop_name', DB::raw('SUM(total) as total'), DB::raw('SUM(total_profit) as total_profit'))
            //     ->groupBy('shop_id','shop_name')
            //     ->orderBy('total', 'desc')
            //     ->get();

            $data = DB::table($subQuery)
            ->orderBy('total', 'desc')->get();

            $labels = $data->pluck('shop_name');
            $totals = $data->pluck('total');
            $profits = $data->pluck('total_profit');
        }

        if ($request->type === 'pic_total') {

            $subQuery->join('users', 'salesdata_subtotal.pic_id', '=', 'users.id')
                ->groupBy('pic_id','users.name')
                ->selectRaw('pic_id,users.name as staff_name, sum(kingaku) as total, sum(arari) as total_profit');

            // $data = DB::table($subQuery)
            //     ->groupBy('pic_id','staff_name')
            //     ->select('pic_id','staff_name', DB::raw('SUM(total) as total'), DB::raw('SUM(total_profit) as total_profit'))
            //     ->groupBy('pic_id','staff_name')
            //     ->orderBy('total', 'desc')
            //     ->get();

            $data = DB::table($subQuery)
            ->orderBy('total', 'desc')->get();

            $labels = $data->pluck('staff_name');
            $totals = $data->pluck('total');
            $profits = $data->pluck('total_profit');
        }

        if ($request->type === 'bd_total') {
            $subQuery->join('brands', 'salesdata_subtotal.brand_id', '=', 'brands.id')
                ->groupBy('brand_id','brands.brand_name')
                ->selectRaw('brand_id,brands.brand_name as brand_name, sum(kingaku) as total, sum(arari) as total_profit');
            $data = DB::table($subQuery)
                ->groupBy('brand_id','brand_name')
                ->select('brand_id','brand_name', DB::raw('SUM(total) as total'), DB::raw('SUM(total_profit) as total_profit'))
                ->groupBy('brand_id','brand_name')
                ->orderBy('total', 'desc')
                ->get();

            $labels = $data->pluck('brand_name');
            $totals = $data->pluck('total');
            $profits = $data->pluck('total_profit');
        }

        if ($request->type === 'ss_total') {

            $subQuery->groupBy('season_id','season_name')
                ->selectRaw('season_id,season_name, sum(kingaku) as total, sum(arari) as total_profit');

            $data = DB::table($subQuery)
                ->groupBy('season_id','season_name')
                ->select('season_id','season_name', DB::raw('SUM(total) as total'), DB::raw('SUM(total_profit) as total_profit'))
                ->groupBy('season_id','season_name')
                ->orderBy('total', 'desc')
                ->get();

            $labels = $data->pluck('season_name');
            $totals = $data->pluck('total');
            $profits = $data->pluck('total_profit');
        }

        if( $request->type === 'un_total') {

            $subQuery->groupBy('unit_id')
                ->selectRaw('unit_id,sum(kingaku) as total, sum(arari) as total_profit');

            $data = DB::table($subQuery)
                ->groupBy('unit_id')
                ->select('unit_id', DB::raw('SUM(total) as total'), DB::raw('SUM(total_profit) as total_profit'))
                ->groupBy('unit_id')
                ->orderBy('total', 'desc')
                ->get();

            $labels = $data->pluck('unit_id');
            $totals = $data->pluck('total');
            $profits = $data->pluck('total_profit');
        }

        if( $request->type === 'fa_total') {
            $subQuery->groupBy('face')
                ->selectRaw('face, sum(kingaku) as total, sum(arari) as total_profit');

            $data = DB::table($subQuery)
                ->groupBy('face')
                ->select('face', DB::raw('SUM(total) as total'), DB::raw('SUM(total_profit) as total_profit'))
                ->groupBy('face')
                ->orderBy('total', 'desc')
                ->get();

            $labels = $data->pluck('face');
            $totals = $data->pluck('total');
            $profits = $data->pluck('total_profit');
        }

        if( $request->type === 'de_total') {

            $subQuery->join('designers', 'salesdata_subtotal.designer_id', '=', 'designers.id')
                ->groupBy('designer_id','designers.designer_name')
                ->selectRaw('designer_id,designers.designer_name as designer_name, sum(kingaku) as total, sum(arari) as total_profit');

            $data = DB::table($subQuery)
                ->groupBy('designer_id','designer_name')
                ->select('designer_id','designer_name', DB::raw('SUM(total) as total'), DB::raw('SUM(total_profit) as total_profit'))
                ->groupBy('designer_id','designer_name')
                ->orderBy('total', 'desc')
                ->get();

            $labels = $data->pluck('designer_name');
            $totals = $data->pluck('total');
            $profits = $data->pluck('total_profit');
        }


        // ★ Company絞り込み
        if (!empty($request->company_id)) {
            $subQuery->where('company_id', $request->company_id);
        }

        // ★Shop絞り込み
        if (!empty($request->shop_id)) {
            $subQuery->where('shop_id', $request->shop_id);
        }

        // ★ 担当者絞り込み
        if (!empty($request->pic_id)) {
            $subQuery->where('pic_id', $request->pic_id);
        }

        // ★ Brand絞り込み
        if (!empty($request->brand_id)) {
            $subQuery->where('brand_id', $request->brand_id);
        }

        // ★ Season絞り込み
        if (!empty($request->season_id)) {
            $subQuery->where('season_id', $request->season_id);
        }

        // ★ Unit絞り込み
        if (!empty($request->unit_id)) {
            $subQuery->where('unit_id', $request->unit_id);
        }

        // ★ Face絞り込み
        if (!empty($request->face_id)) {
            $subQuery->where('face_id', $request->face_id);
        }

        // ★ Designer絞り込み
        if (!empty($request->designer_id)) {
            $subQuery->where('designer_id', $request->designer_id);
        }

        // 集計処理

        if ($request->type === 'pm') {

            $subQuery->groupBy('sales_date')
            ->selectRaw('DATE_FORMAT(sales_date, "%Y%m") as date, SUM(kingaku) as total, SUM(arari) as total_profit');

            $data = DB::table($subQuery)
                ->select('date', DB::raw('SUM(total) as total'), DB::raw('SUM(total_profit) as total_profit'))
                ->groupBy('date')
                ->get();

            // ★ 売上の12ヶ月移動平均
            $totalsArr = $data->pluck('total')->toArray();
            for ($i = 0; $i < count($totalsArr); $i++) {
                $movingAverages[] = ($i < 11)
                    ? null
                    : floor(array_sum(array_slice($totalsArr, $i - 11, 12)) / 12);
            }

            // ★ 粗利の12ヶ月移動平均
            $profitsArr = $data->pluck('total_profit')->toArray();
            for ($i = 0; $i < count($profitsArr); $i++) {
                $movingAveragesProfit[] = ($i < 11)
                    ? null
                    : floor(array_sum(array_slice($profitsArr, $i - 11, 12)) / 12);
            }
            $labels = $data->pluck('date');
            $totals = $data->pluck('total')->map(fn($v) => (int)$v);
            $profits = $data->pluck('total_profit')->map(fn($v) => (int)$v); // ← 追加


        }

        if ($request->type === 'py') {

            $subQuery->groupBy('sales_date')
            ->selectRaw('DATE_FORMAT(sales_date, "%Y") as date, SUM(kingaku) as total, SUM(arari) as total_profit');

            $data = DB::table($subQuery)
                ->select('date', DB::raw('SUM(total) as total'), DB::raw('SUM(total_profit) as total_profit'))
                ->groupBy('date')
                ->get();

            $labels = $data->pluck('date');
            $totals = $data->pluck('total');
            $profits = $data->pluck('total_profit');

            }

        if ($request->type === 'pw') {

            $subQuery->groupBy('sales_date')
            ->selectRaw('YEARWEEK(sales_date, 1) as date, SUM(kingaku) as total, SUM(arari) as total_profit');

            $data = DB::table($subQuery)
                ->select('date', DB::raw('SUM(total) as total'), DB::raw('SUM(total_profit) as total_profit'))
                ->groupBy('date')
                ->get();

            $labels = $data->pluck('date');
            $totals = $data->pluck('total');
            $profits = $data->pluck('total_profit');
        }

        return response()->json([
            'data' => $data,
            'type' => $request->type,
            'labels' => $labels,
            'totals' => $totals,
            'profits' => $profits,
            'movingAverages' => $movingAverages,
            'movingAveragesProfit' => $movingAveragesProfit,
            'companies' => $companies,
            'shops' => $shops,
            'pics' => $pics,
            'brands' => $brands,
            'seasons' => $seasons,
            'units' => $units,
            'faces' => $faces,
            'designers' => $designers,
        ]);
    }
}
