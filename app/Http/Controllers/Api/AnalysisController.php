<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SalesData;
use Illuminate\Support\Facades\DB;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        $movingAverages = [];
        $movingAveragesProfit = [];

        // 初期化
        $data = collect();
        $labels = collect();
        $totals = collect();
        $profits = collect();

        // 会社一覧
        $companies = DB::table('shops')
            ->join('companies', 'shops.company_id', '=', 'companies.id')
            ->whereBetween('shops.company_id', [1001, 7999])
            ->select('companies.id as co_id', 'companies.co_name as co_name')
            ->groupBy('co_id', 'co_name')
            ->orderBy('co_id', 'asc')
            ->get();

        // Shop一覧
        $shops = DB::table('shops')
            ->join('companies', 'shops.company_id', '=', 'companies.id')
            ->whereBetween('shops.company_id', [1001, 7999])
            ->select('shops.id as shop_id', 'companies.co_name as co_name','shops.shop_name as shop_name')
            ->groupBy('shop_id','co_name','shop_name')
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

        // Face一覧
        $faces = DB::table('faces')
            ->select('faces.id as face_id', 'faces.face_code as face_code','faces.face_item as face_item')
            ->groupBy('face_id','face_code','face_item')
            ->orderBy('face_id', 'asc')
            ->get();

        // Designer一覧
        $designers = DB::table('designers')
            ->select('designers.id as designer_id', 'designers.designer_name as designer_name')
            ->groupBy('designer_id','designer_name')
            ->orderBy('designer_id', 'asc')
            ->get();

        // PIC一覧
        $pics = DB::table('users')
            ->where('users.dept_id', 2)
            ->select('users.id as pic_id', 'users.name as pic_name')
            ->groupBy('pic_id','pic_name')
            ->orderBy('pic_id', 'asc')
            ->get();


        /**
         * -------------------------
         * ■ データ抽出のサブクエリ
         * -------------------------
         */
        if ($request->type === 'py') {
            // 年別は全期間
            $subQuery = SalesData::query();
        } else {
            $subQuery = SalesData::betweenDate($request->startDate, $request->endDate);
        }


        /**
         * -------------------------
         * ■ タイプ別集計
         * -------------------------
         */
        switch ($request->type) {

            /**
             * -------------------------
             * 年・月・週 集計
             * -------------------------
             */
            case 'pm': // 月別
            case 'py': // 年別（4月始まり対応）
            case 'pw': // 週別
                // type === 'py' は年度（4月始まり）
                if ($request->type === 'py') {
                    // 年度（Fiscal Year）
                    $dateFormat = "
                        CASE
                            WHEN MONTH(sales_date) >= 4 THEN YEAR(sales_date)
                            ELSE YEAR(sales_date) - 1
                        END
                    ";
                } else {
                    $dateFormat = match($request->type) {
                        'pm' => "DATE_FORMAT(sales_date, '%Y/%m')",
                        'pw' => "DATE_FORMAT(sales_date, '%x/%v')",
                    };
                }

                // すべての絞り込み条件
                $filters = ['company_id','shop_id','pic_id','brand_id','season_id','unit_id','face','designer_id'];
                foreach ($filters as $f) {
                    if (!empty($request->$f)) {
                        $subQuery->where($f, $request->$f);
                    }
                }

                // 日別→年度/月/週へ集計
                $subQuery->groupBy(DB::raw($dateFormat))
                    ->selectRaw("$dateFormat as date, SUM(kingaku) as total, SUM(arari) as total_profit");

                $data = DB::table($subQuery)
                    ->select('date', DB::raw('SUM(total) as total'), DB::raw('SUM(total_profit) as total_profit'))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                $labels = $data->pluck('date');
                $totals = $data->pluck('total')->map(fn($v) => (int)$v);
                $profits = $data->pluck('total_profit')->map(fn($v) => (int)$v);

                // 移動平均（月別のみ）
                if ($request->type === 'pm') {
                    $totalsArr = $totals->toArray();
                    $profitsArr = $profits->toArray();
                    for ($i = 0; $i < count($totalsArr); $i++) {
                        $movingAverages[] =
                            ($i < 11) ? null : floor(array_sum(array_slice($totalsArr, $i - 11, 12)) / 12);

                        $movingAveragesProfit[] =
                            ($i < 11) ? null : floor(array_sum(array_slice($profitsArr, $i - 11, 12)) / 12);
                    }
                }
                break;


            /**
             * -------------------------
             * その他のグルーピング
             * -------------------------
             */

            case 'co_total':
            case 'sh_total':
            case 'pic_total':
            case 'bd_total':
            case 'ss_total':
            case 'un_total':
            case 'fa_total':
            case 'de_total':

                $filters = ['company_id','shop_id','pic_id','brand_id','season_id','unit_id','face','designer_id'];
                foreach ($filters as $f) {
                    if (!empty($request->$f)) {
                        $subQuery->where($f, $request->$f);
                    }
                }

                // 個別処理
                $data = $this->handleTotalTypes($request->type, $subQuery);

                // ラベル・集計値
                $labels = $data['labels'];
                $totals = $data['totals'];
                $profits = $data['profits'];
                break;

            default:
                // 空
                break;
        }


        /**
         * -------------------------
         * ■ API レスポンス
         * -------------------------
         */
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


    /**
     * ------------------------------------------------------
     * 個別 Total タイプの集計（共通処理を分離）
     * ------------------------------------------------------
     */
    private function handleTotalTypes($type, $subQuery)
    {
        switch ($type) {
            case 'co_total':
                $subQuery->groupBy('company_id','co_name')
                    ->selectRaw('company_id, co_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->orderBy('total','desc');
                $data = DB::table($subQuery)->get();
                return [
                    'labels' => $data->pluck('co_name'),
                    'totals' => $data->pluck('total')->map(fn($v)=>(int)$v),
                    'profits' => $data->pluck('total_profit')->map(fn($v)=>(int)$v)
                ];

            case 'sh_total':
                $subQuery->groupBy('shop_id','shop_name')
                    ->selectRaw('shop_id, shop_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->orderBy('total','desc');
                $data = DB::table($subQuery)->get();
                return [
                    'labels' => $data->pluck('shop_name'),
                    'totals' => $data->pluck('total')->map(fn($v)=>(int)$v),
                    'profits' => $data->pluck('total_profit')->map(fn($v)=>(int)$v)
                ];

            case 'pic_total':
                $subQuery->join('users','salesdata_subtotal.pic_id','=','users.id')
                    ->groupBy('pic_id','users.name')
                    ->selectRaw('pic_id, users.name as staff_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->orderBy('total','desc');
                $data = DB::table($subQuery)->get();
                return [
                    'labels' => $data->pluck('staff_name'),
                    'totals' => $data->pluck('total')->map(fn($v)=>(int)$v),
                    'profits' => $data->pluck('total_profit')->map(fn($v)=>(int)$v)
                ];

            case 'bd_total':
                $subQuery->join('brands','salesdata_subtotal.brand_id','=','brands.id')
                    ->groupBy('brand_id','brands.brand_name')
                    ->selectRaw('brand_id, brands.brand_name as brand_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->orderBy('total','desc');
                $data = DB::table($subQuery)->get();
                return [
                    'labels' => $data->pluck('brand_name'),
                    'totals' => $data->pluck('total')->map(fn($v)=>(int)$v),
                    'profits' => $data->pluck('total_profit')->map(fn($v)=>(int)$v)
                ];

            case 'ss_total':
                $subQuery->groupBy('season_id','season_name')
                    ->selectRaw('season_id, season_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->orderBy('season_id','asc');
                $data = DB::table($subQuery)->get();
                return [
                    'labels' => $data->pluck('season_name'),
                    'totals' => $data->pluck('total')->map(fn($v)=>(int)$v),
                    'profits' => $data->pluck('total_profit')->map(fn($v)=>(int)$v)
                ];

            case 'un_total':
                $subQuery->groupBy('unit_id','unit_code')
                    ->selectRaw('unit_id, unit_code, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->orderBy('unit_code','asc');
                $data = DB::table($subQuery)->get();
                return [
                    'labels' => $data->pluck('unit_code')->map(fn($c) => substr($c, -2)),
                    'totals' => $data->pluck('total')->map(fn($v)=>(int)$v),
                    'profits' => $data->pluck('total_profit')->map(fn($v)=>(int)$v)
                ];

            case 'fa_total':
                $subQuery->groupBy('face')
                    ->selectRaw('face, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->orderBy('total','desc');
                $data = DB::table($subQuery)->get();
                return [
                    'labels' => $data->pluck('face'),
                    'totals' => $data->pluck('total')->map(fn($v)=>(int)$v),
                    'profits' => $data->pluck('total_profit')->map(fn($v)=>(int)$v)
                ];

            case 'de_total':
                $subQuery->join('designers','salesdata_subtotal.designer_id','=','designers.id')
                    ->where('designer_id','<>',99)
                    ->groupBy('designer_id','designers.designer_name')
                    ->selectRaw('designer_id, designers.designer_name as designer_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                    ->orderBy('total','desc');
                $data = DB::table($subQuery)->get();
                return [
                    'labels' => $data->pluck('designer_name'),
                    'totals' => $data->pluck('total')->map(fn($v)=>(int)$v),
                    'profits' => $data->pluck('total_profit')->map(fn($v)=>(int)$v)
                ];
        }

        return ['labels'=>collect(),'totals'=>collect(),'profits'=>collect()];
    }
}
