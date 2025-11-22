<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Shop;
use App\Models\SalesData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesComparisonController extends Controller
{
    public function index(Request $request)
    {
        $compareType = $request->get('compareType', 'monthly'); // monthly / weekly
        $company_id = $request->get('company_id');
        $shop_id = $request->get('shop_id');

        $today = Carbon::today();

        if ($compareType === 'monthly') {
            $startDate = $today->copy()->subMonths(11)->startOfMonth();
            $endDate = $today->copy()->endOfMonth();
            $prevStart = $startDate->copy()->subYear();
            $prevEnd = $endDate->copy()->subYear();
        } else { // weekly
            $startDate = $today->copy()->subWeeks(51)->startOfWeek();
            $endDate = $today->copy()->endOfWeek();
            $prevStart = $startDate->copy()->subYear();
            $prevEnd = $endDate->copy()->subYear();
        }

        $companies = Company::all();
        $shops = $company_id ? Shop::where('company_id', $company_id)->get() : collect();

        // DB側で現年/前年をまとめて取得
        $query = SalesData::query()
            ->selectRaw(
                match($compareType) {
                    'monthly' => "
                        YEAR(sales_date) as year,
                        MONTH(sales_date) as period,
                        CASE WHEN sales_date BETWEEN ? AND ? THEN 1
                             WHEN sales_date BETWEEN ? AND ? THEN 0
                             ELSE NULL END as is_current,
                        SUM(kingaku) as total_sales,
                        SUM(arari) as total_profit
                    ",
                    'weekly' => "
                        YEAR(sales_date) as year,
                        WEEK(sales_date,1) as period,
                        CASE WHEN sales_date BETWEEN ? AND ? THEN 1
                             WHEN sales_date BETWEEN ? AND ? THEN 0
                             ELSE NULL END as is_current,
                        SUM(kingaku) as total_sales,
                        SUM(arari) as total_profit,
                        MAX(sales_date) as max_date
                    "
                }
            , [$startDate, $endDate, $prevStart, $prevEnd])
            ->when($company_id, fn($q) => $q->where('company_id', $company_id))
            ->when($shop_id, fn($q) => $q->where('shop_id', $shop_id))
            ->groupBy('year','period','is_current')
            ->orderBy('year')
            ->orderBy('period')
            ->get();

        // 現年・前年データをDB側で振り分け
        $currentData = $query->where('is_current',1)->keyBy(fn($item) => $item->period);
        $prevData = $query->where('is_current',0)->keyBy(fn($item) => $item->period);

        // 表示行作成
        $rows = [];
        if ($compareType === 'monthly') {
            for ($i = 0; $i < 12; $i++) {
                $date = $startDate->copy()->addMonths($i);
                $m = intval($date->format('n'));

                $cur = $currentData->get($m);
                $pre = $prevData->get($m);

                $sales = $cur->total_sales ?? 0;
                $profit = $cur->total_profit ?? 0;
                $sales_prev = $pre->total_sales ?? 0;
                $profit_prev = $pre->total_profit ?? 0;
                $sales_rate = $sales_prev ? round(($sales/$sales_prev)*100,1) : null;
                $profit_rate = $profit_prev ? round(($profit/$profit_prev)*100,1) : null;

                $rows[] = [
                    'period' => $date->format('y/n'),
                    'sales' => $sales,
                    'profit' => $profit,
                    'sales_prev' => $sales_prev,
                    'profit_prev' => $profit_prev,
                    'sales_rate' => $sales_rate,
                    'profit_rate' => $profit_rate,
                ];
            }
        } else { // weekly
            for ($i = 0; $i < 52; $i++) {
                $weekStart = $startDate->copy()->addWeeks($i);
                $weekEnd = $weekStart->copy()->endOfWeek();
                $w = intval($weekStart->format('W'));

                $cur = $currentData->get($w);
                $pre = $prevData->get($w);

                $sales = $cur->total_sales ?? 0;
                $profit = $cur->total_profit ?? 0;
                $sales_prev = $pre->total_sales ?? 0;
                $profit_prev = $pre->total_profit ?? 0;
                $sales_rate = $sales_prev ? round(($sales/$sales_prev)*100,1) : null;
                $profit_rate = $profit_prev ? round(($profit/$profit_prev)*100,1) : null;

                $rows[] = [
                    'period' => $weekEnd->format('y/m/d'),
                    'sales' => $sales,
                    'profit' => $profit,
                    'sales_prev' => $sales_prev,
                    'profit_prev' => $profit_prev,
                    'sales_rate' => $sales_rate,
                    'profit_rate' => $profit_rate,
                ];
            }
        }

        return response()->json([
            'companies' => $companies,
            'shops' => $shops,
            'rows' => $rows,
        ]);
    }
}


