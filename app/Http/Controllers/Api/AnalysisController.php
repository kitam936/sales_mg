<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        $movingAverages = [];
        $movingAveragesProfit = [];

        // 会社一覧
        $companies = DB::table('shops')
            ->join('companies', 'shops.company_id', '=', 'companies.id')
            ->whereBetween('shops.company_id', [1, 7999])
            ->select('companies.id as co_id', 'companies.co_name as co_name')
            ->groupBy('co_id', 'co_name')
            ->orderBy('co_id')
            ->get();

        // Shop一覧
        $shops = DB::table('shops')
            ->join('companies','shops.company_id','=','companies.id')
            ->whereBetween('shops.company_id',[1001,7999])
            ->select('shops.id as shop_id','shops.shop_name','companies.co_name')
            ->groupBy('shop_id','shops.shop_name','companies.co_name')
            ->orderBy('shop_id')
            ->get();

        // Brand一覧
        $brands = DB::table('brands')
            ->select('id as brand_id','brand_name')
            ->orderBy('id')
            ->get();

        // Season一覧
        $seasons = DB::table('units')
            ->select('season_id','season_name')
            ->groupBy('season_id','season_name')
            ->orderBy('season_id')
            ->get();

        // Unit一覧
        $units = DB::table('units')
            ->select('id as unit_id','unit_code')
            ->groupBy('id','unit_code')
            ->orderBy('unit_code')
            ->get();

        // Face一覧
        $faces = DB::table('faces')
            ->select('id as face_id','face_code','face_item')
            ->orderBy('id')
            ->get();

        // Designer一覧
        $designers = DB::table('designers')
            ->select('id as designer_id','designer_name')
            ->orderBy('id')
            ->get();

        // PIC一覧
        $pics = DB::table('users')
            ->where('dept_id',2)
            ->select('id as pic_id','name as pic_name')
            ->orderBy('id')
            ->get();

        //年度一覧
        $years = DB::table('hinbans')
            ->select('year_code')
            ->groupBy('year_code')
            ->orderBy('year_code','desc')
            // ->distinct()
            ->get();


        /**
         * ----------------------------------------------------
         * 基本データクエリ
         * ----------------------------------------------------
         */
        $query = DB::table('sales')
            ->join('shops','sales.shop_id','=','shops.id')
            ->join('companies','companies.id','=','shops.company_id')
            ->join('hinbans','hinbans.id','=','sales.hinban_id')
            ->join('brands','brands.id','=','hinbans.brand_id')
            ->join('units','units.id','=','hinbans.unit_id');

        // 期間絞り込み
        if ($request->type !== 'py') {
            $start = $request->startDate;
            $end = isset($request->endDate) ? date('Y-m-d', strtotime($request->endDate.' +1 day')) : null;

            if ($start && $end) $query->whereBetween('sales_date', [$start, $end]);
            elseif ($start) $query->where('sales_date', '>=', $start);
            elseif ($end) $query->where('sales_date', '<=', $end);
        }

        // その他フィルター
        $filters = ['company_id','shop_id','pic_id','brand_id','season_id','unit_id','face','designer_id'];
        foreach ($filters as $f) {
            if (!empty($request->$f)) {
                $query->where($f, $request->$f);
            }
        }

        /**
         * ----------------------------------------------------
         * type別クエリ
         * ----------------------------------------------------
         */
        $data = collect();

        switch ($request->type) {
            case 'py': // 年度（4月始まり）
                $data = $query
                    ->selectRaw("
                        CASE
                            WHEN MONTH(sales_date) >= 4 THEN YEAR(sales_date)
                            ELSE YEAR(sales_date) - 1
                        END as date,
                        SUM(kingaku) as total,
                        SUM(arari) as total_profit
                    ")
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                break;

            case 'pm': // 月別
                $data = $query
                    ->selectRaw("DATE_FORMAT(sales_date,'%Y/%m') as date, SUM(kingaku) as total, SUM(arari) as total_profit")
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                // 移動平均（12ヶ月）
                $totalsArr = $data->pluck('total')->toArray();
                $profitsArr = $data->pluck('total_profit')->toArray();
                for ($i = 0; $i < count($totalsArr); $i++) {
                    $movingAverages[] = ($i < 11) ? null : floor(array_sum(array_slice($totalsArr,$i-11,12))/12);
                    $movingAveragesProfit[] = ($i < 11) ? null : floor(array_sum(array_slice($profitsArr,$i-11,12))/12);
                }
                break;

            case 'pw': // 週別
                $data = $query
                    ->selectRaw("DATE_FORMAT(sales_date,'%x/%v') as date, SUM(kingaku) as total, SUM(arari) as total_profit")
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                break;

            // 個別集計
            case 'co_total':
                $data = $query
                    ->selectRaw('companies.co_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->groupBy('companies.co_name')
                    ->orderByDesc('total')
                    ->get();
                break;

            case 'sh_total':
                $data = $query
                    ->selectRaw('shops.shop_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->groupBy('shops.shop_name')
                    ->orderByDesc('total')
                    ->get();
                break;

            case 'pic_total':
                $data = $query
                    ->join('users','companies.pic_id','=','users.id')
                    ->selectRaw('users.name as pic_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->groupBy('pic_name')
                    ->orderByDesc('total')
                    ->get();
                break;

            case 'bd_total':
                $data = $query
                    ->selectRaw('brands.brand_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->groupBy('brands.brand_name')
                    ->orderByDesc('total')
                    ->get();
                break;

            case 'ss_total':
                $data = $query
                    ->selectRaw('units.season_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->groupBy('units.season_name')
                    ->orderBy('units.season_name')
                    ->get();
                break;

            case 'un_total':
                $data = $query
                    ->selectRaw('RIGHT(units.unit_code, 2) as unit_code, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->groupBy('units.unit_code')
                    ->orderBy('units.unit_code')
                    ->get();
                break;

            case 'fa_total':
                $data = $query
                    ->selectRaw('face, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->groupBy('face')
                    ->orderByDesc('total')
                    ->get();
                break;

            case 'de_total':
                // $data = $query
                //     ->join('designers','hinbans.designer_id','=','designers.id')
                //     ->where('designers.id','<>',99)
                //     ->selectRaw('designers.designer_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                //     ->groupBy('designers.designer_name')
                //     ->orderByDesc('total')
                //     ->get();

                $data = DB::table('sales')
                    ->join('hinbans','sales.hinban_id','=','hinbans.id')
                    ->join('designers','hinbans.designer_id','=','designers.id')
                    ->where('designers.id','<>',99)
                    ->select('designers.designer_name', DB::raw('SUM(kingaku) as total'), DB::raw('SUM(arari) as total_profit'))
                    ->groupBy('designers.designer_name')
                    ->orderByDesc('total')
                    ->get();
                break;
        }


        /**
         * ----------------------------------------------------
         * ★ ラベル列を安全に選ぶ
         * ----------------------------------------------------
         */
        $labelColumns = [
            'date',
            'co_name',
            'shop_name',
            'pic_name',
            'brand_name',
            'season_name',
            'unit_code',
            'face',
            'designer_name',
        ];

        $labels = collect();

        foreach ($labelColumns as $col) {
            $values = $data->pluck($col)->filter();
            if ($values->isNotEmpty()) {
                $labels = $values;
                break;
            }
        }

        // 配列として返す
        $labelsArray = $labels->values();

        // 合計値
        $totals = $data->pluck('total')->map(fn($v) => (int)$v)->values();
        $profits = $data->pluck('total_profit')->map(fn($v) => (int)$v)->values();


        return response()->json([
            'data' => $data,
            'type' => $request->type,
            'labels' => $labelsArray,
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
            'year_code' => $years,
        ]);
    }
}


