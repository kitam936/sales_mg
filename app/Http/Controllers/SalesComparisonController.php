<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Shop;
use App\Models\SalesData;
use Carbon\Carbon;

class SalesComparisonController extends Controller
{
    public function index(Request $request)
    {
        $compareType = $request->get('compareType', 'monthly');
        $company_id  = $request->get('company_id');
        $shop_id     = $request->get('shop_id');

        // 追加フィルター
        $filters = ['pic_id','brand_id','season_id','unit_id','face','designer_id','year_code'];

        // 現年の最大売上日
        $maxSalesDateQuery = SalesData::query()
            ->when($company_id, fn($q) => $q->where('company_id', $company_id))
            ->when($shop_id, fn($q) => $q->where('shop_id', $shop_id));

        foreach ($filters as $f) {
            if (!empty($request->$f)) {
                $maxSalesDateQuery->where($f, $request->$f);
            }
        }

        $maxSalesDate = $maxSalesDateQuery->max('sales_date');

        $today = Carbon::today();
        $maxDate = $maxSalesDate ? Carbon::parse($maxSalesDate) : $today;

        // 期間設定
        if ($compareType === 'monthly') {
            $endDate   = $maxDate->copy()->endOfMonth();
            $startDate = $endDate->copy()->subMonths(11)->startOfMonth();

            $prevStart = $startDate->copy()->subYear()->startOfMonth();
            $prevEnd   = $endDate->copy()->subYear()->endOfMonth();

        } else {
            $maxSunday = $maxDate->copy()->endOfWeek(Carbon::SUNDAY);
            $endDate   = $maxSunday;
            $startDate = $endDate->copy()->subWeeks(51)->startOfWeek(Carbon::MONDAY);

            $prevStart = $startDate->copy()->subYear()->startOfWeek(Carbon::MONDAY);
            $prevEnd   = $endDate->copy()->subYear()->startOfWeek(Carbon::MONDAY);
        }

        // マスタ取得
        $companies = Company::all();
        $shops     = $company_id ? Shop::where('company_id', $company_id)->get() : collect();

        // DB 一括取得
        $baseQuery = SalesData::query()
            ->when($company_id, fn($q) => $q->where('company_id', $company_id))
            ->when($shop_id, fn($q) => $q->where('shop_id', $shop_id));

        // すべての追加フィルター適用
        foreach ($filters as $f) {
            if (!empty($request->$f)) {
                $baseQuery->where($f, $request->$f);
            }
        }

        if ($compareType === 'monthly') {

            $query = $baseQuery
                ->selectRaw("
                    DATE_FORMAT(sales_date, '%Y-%m-01') as period_key,
                    CASE WHEN sales_date BETWEEN ? AND ? THEN 1
                         WHEN sales_date BETWEEN ? AND ? THEN 0 END as is_current,
                    SUM(kingaku) as total_sales,
                    SUM(arari) as total_profit
                ", [$startDate, $endDate, $prevStart, $prevEnd])
                ->groupBy('period_key', 'is_current')
                ->orderBy('period_key')
                ->get();

        } else {

            $query = $baseQuery
                ->selectRaw("
                    DATE_FORMAT(
                        DATE_SUB(sales_date, INTERVAL WEEKDAY(sales_date) DAY),
                        '%Y-%m-%d'
                    ) as period_key,
                    CASE WHEN sales_date BETWEEN ? AND ? THEN 1
                         WHEN sales_date BETWEEN ? AND ? THEN 0 END as is_current,
                    SUM(kingaku) as total_sales,
                    SUM(arari) as total_profit
                ", [$startDate, $endDate, $prevStart, $prevEnd])
                ->groupBy('period_key', 'is_current')
                ->orderBy('period_key')
                ->get();
        }

        // 整形
        $currentData = $query->where('is_current', 1)->keyBy('period_key');
        $prevData    = $query->where('is_current', 0)->keyBy('period_key');

        $rows = [];

        // 月別処理
        if ($compareType === 'monthly') {

            for ($i = 0; $i < 12; $i++) {
                $monthStart = $startDate->copy()->addMonths($i)->startOfMonth();
                $key        = $monthStart->format('Y-m-01');

                $cur = $currentData->get($key);
                $pre = $prevData->get($monthStart->copy()->subYear()->format('Y-m-01'));

                $rows[] = [
                    'period'      => $monthStart->format('y/m'),
                    'sales'       => $cur->total_sales ?? 0,
                    'profit'      => $cur->total_profit ?? 0,
                    'sales_prev'  => $pre->total_sales ?? 0,
                    'profit_prev' => $pre->total_profit ?? 0,
                    'sales_rate'  => ($pre && $pre->total_sales > 0)
                        ? round(($cur->total_sales ?? 0) / $pre->total_sales * 100, 1)
                        : null,
                    'profit_rate' => ($pre && $pre->total_profit > 0)
                        ? round(($cur->total_profit ?? 0) / $pre->total_profit * 100, 1)
                        : null,
                ];
            }

        } else {

            $maxSunday = $maxSalesDate ? Carbon::parse($maxSalesDate)->copy()->endOfWeek(Carbon::SUNDAY) : $endDate->copy();
            $totalWeeks = $startDate->diffInWeeks($endDate) + 1;

            for ($i = 0; $i < $totalWeeks; $i++) {

                $weekStart = $startDate->copy()->addWeeks($i)->startOfWeek(Carbon::MONDAY);
                $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

                if ($weekEnd->greaterThan($maxSunday)) {
                    break;
                }

                $key     = $weekStart->format('Y-m-d');
                $prevKey = $weekStart->copy()->subYear()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');

                $cur = $currentData->get($key);
                $pre = $prevData->get($prevKey);

                $rows[] = [
                    'period'      => $weekEnd->format('y/m/d'),
                    'sales'       => $cur->total_sales ?? 0,
                    'profit'      => $cur->total_profit ?? 0,
                    'sales_prev'  => $pre->total_sales ?? 0,
                    'profit_prev' => $pre->total_profit ?? 0,
                    'sales_rate'  => ($pre && $pre->total_sales > 0)
                        ? round(($cur->total_sales ?? 0) / $pre->total_sales * 100, 1)
                        : null,
                    'profit_rate' => ($pre && $pre->total_profit > 0)
                        ? round(($cur->total_profit ?? 0) / $pre->total_profit * 100, 1)
                        : null,
                ];
            }
        }

        return response()->json([
            'companies' => $companies,
            'shops'     => $shops,
            'rows'      => $rows,
        ]);
    }
}
