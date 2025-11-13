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

        $endDate = Carbon::parse($request->get('endDate', now()->format('Y-m-d')));

        // 月別・週別の期間設定
        if ($compareType === 'weekly') {
            // 今週を終点として過去52週分
            $endDate = $endDate->copy()->endOfWeek();
            $startDate = $endDate->copy()->subWeeks(51)->startOfWeek();
        } else { // monthly
            // 今月を終点として過去12か月分
            $endDate = $endDate->copy()->endOfMonth();
            $startDate = $endDate->copy()->subMonths(11)->startOfMonth();
        }

        // 前年同期間
        $prevStart = $startDate->copy()->subYear();
        $prevEnd = $endDate->copy()->subYear();

        $companies = Company::all();
        $shops = $company_id ? Shop::where('company_id', $company_id)->get() : collect();

        // 集計列
        $selectRaw = match($compareType) {
            'monthly' => "YEAR(sales_date) as year, MONTH(sales_date) as period, SUM(kingaku) as total_sales, SUM(arari) as total_profit",
            'weekly'  => "YEAR(sales_date) as year, WEEK(sales_date,1) as period, SUM(kingaku) as total_sales, SUM(arari) as total_profit, MAX(sales_date) as max_date",
            default => "YEAR(sales_date) as year, MONTH(sales_date) as period, SUM(kingaku) as total_sales, SUM(arari) as total_profit",
        };

        $groupBy = match($compareType) {
            'monthly' => ['year', 'period'],
            'weekly' => ['year', 'period'],
            default => ['year', 'period'],
        };

        // データ取得
        $query = SalesData::query()
            ->selectRaw($selectRaw)
            ->when($company_id, fn($q) => $q->where('company_id', $company_id))
            ->when($shop_id, fn($q) => $q->where('shop_id', $shop_id))
            ->whereBetween('sales_date', [$prevStart, $endDate])
            ->groupBy($groupBy)
            ->orderBy('year')
            ->orderBy('period')
            ->get();

        // 現年・前年データ
        $currentData = $query->filter(fn($item) => $item->year == $startDate->copy()->addYear()->year || $item->year == $endDate->year)
                             ->keyBy(fn($item) => intval($item->period));
        $prevData = $query->filter(fn($item) => $item->year == $prevStart->year)->keyBy(fn($item) => intval($item->period));

        $rows = [];

        if ($compareType === 'monthly') {
            for ($m = 0; $m < 12; $m++) {
                $monthDate = $endDate->copy()->subMonths(11 - $m);
                $periodNumber = $monthDate->month;

                $cur = $currentData->get($periodNumber);
                $pre = $prevData->get($periodNumber);

                $sales = $cur->total_sales ?? 0;
                $profit = $cur->total_profit ?? 0;
                $sales_prev = $pre->total_sales ?? 0;
                $profit_prev = $pre->total_profit ?? 0;
                $sales_rate = $sales_prev ? round(($sales / $sales_prev) * 100, 1) : null;
                $profit_rate = $profit_prev ? round(($profit / $profit_prev) * 100, 1) : null;

                $periodLabel = $monthDate->format('y年n月');

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
        } else { // weekly
            for ($w = 0; $w < 52; $w++) {
                $weekEnd = $endDate->copy()->subWeeks(51 - $w)->endOfWeek();
                $periodNumber = intval($weekEnd->weekOfYear);

                $cur = $currentData->get($periodNumber);
                $pre = $prevData->get($periodNumber);

                $sales = $cur->total_sales ?? 0;
                $profit = $cur->total_profit ?? 0;
                $sales_prev = $pre->total_sales ?? 0;
                $profit_prev = $pre->total_profit ?? 0;
                $sales_rate = $sales_prev ? round(($sales / $sales_prev) * 100, 1) : null;
                $profit_rate = $profit_prev ? round(($profit / $profit_prev) * 100, 1) : null;

                // 週ラベルは週の最終日を YYMMDD
                $periodLabel = $weekEnd->format('ymd');

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

