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
        $movingAverages = [];        // 売上12ヶ月移動平均
        $movingAveragesProfit = []; // 粗利12ヶ月移動平均

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

        // 日付範囲で絞り込み
        $subQuery = SalesData::betweenDate($request->startDate, $request->endDate);

        // 絞り込み条件
        $filters = ['company_id','shop_id','pic_id','brand_id','season_id','unit_id','face_id','designer_id'];
        foreach ($filters as $f) {
            if (!empty($request->$f)) {
                $subQuery->where($f, $request->$f);
            }
        }

    // 集計処理：タイプ別
    switch($request->type) {
        case 'co_total':
            $subQuery->groupBy('company_id','co_name')
                     ->selectRaw('company_id, co_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                     ->orderBy('total', 'desc');
            $data = DB::table($subQuery)->get();
            $labels = $data->pluck('co_name');
            $totals = $data->pluck('total')->map(fn($v) => (int)$v);
            $profits = $data->pluck('total_profit')->map(fn($v) => (int)$v);
            break;

        case 'sh_total':
            $subQuery->groupBy('shop_id','shop_name')
                     ->selectRaw('shop_id, shop_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                     ->orderBy('total', 'desc');
            $data = DB::table($subQuery)->get();
            $labels = $data->pluck('shop_name');
            $totals = $data->pluck('total')->map(fn($v) => (int)$v);
            $profits = $data->pluck('total_profit')->map(fn($v) => (int)$v);
            break;

        case 'pic_total':
            $subQuery->join('users', 'salesdata_subtotal.pic_id', '=', 'users.id')
                     ->groupBy('pic_id','users.name')
                     ->selectRaw('pic_id, users.name as staff_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                     ->orderBy('total', 'desc');
            $data = DB::table($subQuery)->get();
            $labels = $data->pluck('staff_name');
            $totals = $data->pluck('total')->map(fn($v) => (int)$v);
            $profits = $data->pluck('total_profit')->map(fn($v) => (int)$v);
            break;

        case 'bd_total':
            $subQuery->join('brands','salesdata_subtotal.brand_id','=','brands.id')
                     ->groupBy('brand_id','brands.brand_name')
                     ->selectRaw('brand_id, brands.brand_name as brand_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                     ->orderBy('total','desc');
            $data = DB::table($subQuery)->get();
            $labels = $data->pluck('brand_name');
            $totals = $data->pluck('total')->map(fn($v)=>(int)$v);
            $profits = $data->pluck('total_profit')->map(fn($v)=>(int)$v);
            break;

        case 'ss_total':
            $subQuery->groupBy('season_id','season_name')
                     ->selectRaw('season_id, season_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                     ->orderBy('total','desc');
            $data = DB::table($subQuery)->get();
            $labels = $data->pluck('season_name');
            $totals = $data->pluck('total')->map(fn($v)=>(int)$v);
            $profits = $data->pluck('total_profit')->map(fn($v)=>(int)$v);
            break;

        case 'un_total':
            $subQuery->groupBy('unit_id')
                     ->selectRaw('unit_id, SUM(kingaku) as total, SUM(arari) as total_profit')
                     ->orderBy('total','desc');
            $data = DB::table($subQuery)->get();
            $labels = $data->pluck('unit_id');
            $totals = $data->pluck('total')->map(fn($v)=>(int)$v);
            $profits = $data->pluck('total_profit')->map(fn($v)=>(int)$v);
            break;

        case 'fa_total':
            $subQuery->groupBy('face')
                     ->selectRaw('face, SUM(kingaku) as total, SUM(arari) as total_profit')
                     ->orderBy('total','desc');
            $data = DB::table($subQuery)->get();
            $labels = $data->pluck('face');
            $totals = $data->pluck('total')->map(fn($v)=>(int)$v);
            $profits = $data->pluck('total_profit')->map(fn($v)=>(int)$v);
            break;

        case 'de_total':
            $subQuery->join('designers','salesdata_subtotal.designer_id','=','designers.id')
                     ->groupBy('designer_id','designers.designer_name')
                     ->selectRaw('designer_id, designers.designer_name as designer_name, SUM(kingaku) as total, SUM(arari) as total_profit')
                     ->orderBy('total','desc');
            $data = DB::table($subQuery)->get();
            $labels = $data->pluck('designer_name');
            $totals = $data->pluck('total')->map(fn($v)=>(int)$v);
            $profits = $data->pluck('total_profit')->map(fn($v)=>(int)$v);
            break;

        case 'pm': // 月別
        case 'py': // 年別
        case 'pw': // 週別
            $dateFormat = match($request->type) {
                'pm' => '%Y%m',
                'py' => '%Y',
                'pw' => '%x%v', // YEARWEEK
            };
            $subQuery->groupBy('sales_date')
                     ->selectRaw("DATE_FORMAT(sales_date, '$dateFormat') as date, SUM(kingaku) as total, SUM(arari) as total_profit");
            $data = DB::table($subQuery)
                    ->select('date', DB::raw('SUM(total) as total'), DB::raw('SUM(total_profit) as total_profit'))
                    ->groupBy('date')
                    ->get();
            $labels = $data->pluck('date');
            $totals = $data->pluck('total')->map(fn($v)=>(int)$v);
            $profits = $data->pluck('total_profit')->map(fn($v)=>(int)$v);

            // 移動平均は月別のみ
            if ($request->type === 'pm') {
                $totalsArr = $totals->toArray();
                $profitsArr = $profits->toArray();
                for ($i = 0; $i < count($totalsArr); $i++) {
                    $movingAverages[] = ($i < 11) ? null : floor(array_sum(array_slice($totalsArr, $i-11,12))/12);
                    $movingAveragesProfit[] = ($i < 11) ? null : floor(array_sum(array_slice($profitsArr, $i-11,12))/12);
                }
            }
            break;

        default:
            // デフォルトは空配列
            $labels = collect();
            $totals = collect();
            $profits = collect();
            break;
    }

    return response()->json([
        'data' => $data,
        'type' => $request->type,
        'labels' => $labels,
        'totals' => $totals,
        'profits' => $profits,               // ← ここが必ずセットされる
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
