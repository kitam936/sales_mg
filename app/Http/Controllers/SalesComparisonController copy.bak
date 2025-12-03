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
            // 現年期間：直近12か月
            $startDate = $today->copy()->subMonths(11)->startOfMonth();
            $endDate = $today->copy()->endOfMonth();
            // 前年期間
            $prevStart = $startDate->copy()->subYear();
            $prevEnd = $endDate->copy()->subYear();
        } else { // weekly
            // 現年期間：直近52週
            $startDate = $today->copy()->subWeeks(51)->startOfWeek();
            $endDate = $today->copy()->endOfWeek();
            // 前年期間
            $prevStart = $startDate->copy()->subYear();
            $prevEnd = $endDate->copy()->subYear();
        }

        $companies = Company::all();
        $shops = $company_id ? Shop::where('company_id', $company_id)->get() : collect();

        // 集計列
        $selectRaw = match($compareType) {
            'monthly' => "YEAR(sales_date) as year, MONTH(sales_date) as period, SUM(kingaku) as total_sales, SUM(arari) as total_profit",
            'weekly'  => "YEAR(sales_date) as year, WEEK(sales_date,1) as period, SUM(kingaku) as total_sales, SUM(arari) as total_profit, MAX(sales_date) as max_date",
        };

        $groupBy = match($compareType) {
            'monthly' => ['year','period'],
            'weekly' => ['year','period'],
        };

        // データ取得（現年＋前年の両方）
        $query = SalesData::query()
            ->selectRaw($selectRaw)
            ->when($company_id, fn($q) => $q->where('company_id', $company_id))
            ->when($shop_id, fn($q) => $q->where('shop_id', $shop_id))
            ->whereBetween('sales_date', [$prevStart, $endDate])
            ->groupBy($groupBy)
            ->orderBy('year')
            ->orderBy('period')
            ->get();

        // 現年・前年データを振り分け
        if ($compareType === 'monthly') {
            $currentData = $query->filter(fn($item) =>
                Carbon::create($item->year, $item->period, 1)->between($startDate, $endDate)
            )->keyBy(fn($item) => intval($item->period));

            $prevData = $query->filter(fn($item) =>
                Carbon::create($item->year, $item->period, 1)->between($prevStart, $prevEnd)
            )->keyBy(fn($item) => intval($item->period));
        } else { // weekly
            $currentData = $query->filter(fn($item) =>
                Carbon::parse($item->max_date)->between($startDate, $endDate)
            )->keyBy(fn($item) => intval($item->period));

            $prevData = $query->filter(fn($item) =>
                Carbon::parse($item->max_date)->between($prevStart, $prevEnd)
            )->keyBy(fn($item) => intval($item->period));
        }

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
                $sales_rate = $sales_prev ? round(($sales / $sales_prev) * 100,1) : null;
                $profit_rate = $profit_prev ? round(($profit / $profit_prev) * 100,1) : null;

                $periodLabel = $date->format('y/n');

                $rows[] = [
                    'period' => $periodLabel,
                    'sales' => $sales,
                    'profit' => $profit,
                    'sales_prev' => $sales_prev,
                    'profit_prev' => $profit_prev,
                    'sales_rate' => $sales_rate,
                    'profit_rate' => $profit_rate,
                ];
            }
        } else { // weekly 直近52週
            for ($i = 0; $i < 52; $i++) {
                $weekStart = $startDate->copy()->addWeeks($i);
                $weekEnd = $weekStart->copy()->endOfWeek();
                $w = intval($weekStart->format('W'));

                // 現年・前年のキーに週番号を使う
                $cur = $currentData->get($w);
                $pre = $prevData->get($w);

                $sales = $cur->total_sales ?? 0;
                $profit = $cur->total_profit ?? 0;
                $sales_prev = $pre->total_sales ?? 0;
                $profit_prev = $pre->total_profit ?? 0;
                $sales_rate = $sales_prev ? round(($sales / $sales_prev) * 100,1) : null;
                $profit_rate = $profit_prev ? round(($profit / $profit_prev) * 100,1) : null;

                // 週ラベルはその週の最大日付をYYMMDD
                $periodLabel = $weekEnd->format('y/m/d');

                $rows[] = [
                    'period' => $periodLabel,
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

