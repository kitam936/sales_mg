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
        $compareType = $request->get('compareType', 'monthly');
        $company_id = $request->get('company_id');
        $shop_id = $request->get('shop_id');

        /**
         * ============================
         *  ★ 現年の売上データ最大日付を取得
         * ============================
         */
        $maxSalesDate = SalesData::when($company_id, fn($q) => $q->where('company_id', $company_id))
            ->when($shop_id, fn($q) => $q->where('shop_id', $shop_id))
            ->max('sales_date');

        // データがなければ今日とする
        $maxDate = $maxSalesDate ? Carbon::parse($maxSalesDate) : Carbon::today();


        /**
         * ============================
         *   期間設定（月：12ヶ月 / 週：maxDate の週まで）
         * ============================
         */
        if ($compareType === 'monthly') {

            // 12ヶ月固定
            $endDate = $maxDate->copy()->endOfMonth();
            $startDate = $endDate->copy()->subMonths(11)->startOfMonth();

        } else {

            // ★週別 → maxDate の週で止める
            $endDate = $maxDate->copy()->endOfWeek();  // 現年の最終週
            $startDate = $endDate->copy()->subWeeks(51)->startOfWeek();
        }

        // 前年期間
        $prevStart = $startDate->copy()->subYear();
        $prevEnd   = $endDate->copy()->subYear();

        $companies = Company::all();
        $shops = $company_id ? Shop::where('company_id', $company_id)->get() : collect();


        /**
         * ============================
         *  DB 一括取得（現年＋前年）
         * ============================
         */
        $query = SalesData::query()
            ->selectRaw(
                $compareType === 'monthly'
                    ? "
                        MONTH(sales_date) as period,
                        CASE WHEN sales_date BETWEEN ? AND ? THEN 1
                             WHEN sales_date BETWEEN ? AND ? THEN 0 END as is_current,
                        SUM(kingaku) as total_sales,
                        SUM(arari) as total_profit
                    "
                    : "
                        WEEK(sales_date,1) as period,
                        CASE WHEN sales_date BETWEEN ? AND ? THEN 1
                             WHEN sales_date BETWEEN ? AND ? THEN 0 END as is_current,
                        SUM(kingaku) as total_sales,
                        SUM(arari) as total_profit,
                        MAX(sales_date) as max_date
                    ",
                [$startDate, $endDate, $prevStart, $prevEnd]
            )
            ->when($company_id, fn($q) => $q->where('company_id', $company_id))
            ->when($shop_id, fn($q) => $q->where('shop_id', $shop_id))
            ->groupBy('period', 'is_current')
            ->orderBy('period')
            ->get();

        $currentData = $query->where('is_current', 1)->keyBy('period');
        $prevData = $query->where('is_current', 0)->keyBy('period');

        $rows = [];


        /**
         * ============================
         *  月別 12ヶ月
         * ============================
         */
        if ($compareType === 'monthly') {

            for ($i = 0; $i < 12; $i++) {

                $date = $startDate->copy()->addMonths($i);
                $m = intval($date->format('n'));

                $cur = $currentData->get($m);
                $pre = $prevData->get($m);

                $sales       = $cur->total_sales ?? 0;
                $profit      = $cur->total_profit ?? 0;
                $sales_prev  = $pre->total_sales ?? 0;
                $profit_prev = $pre->total_profit ?? 0;

                $rows[] = [
                    'period'      => $date->format('y/m'),
                    'sales'       => $sales,
                    'profit'      => $profit,
                    'sales_prev'  => $sales_prev,
                    'profit_prev' => $profit_prev,
                    'sales_rate'  => $sales_prev ? round($sales / $sales_prev * 100, 1) : null,
                    'profit_rate' => $profit_prev ? round($profit / $profit_prev * 100, 1) : null,
                ];
            }

        /**
         * ============================
         *  週別（★maxDate の週まで）
         * ============================
         */
        } else {

            /**
             * ★ maxDate の週番号（ISO-8601）
             */
            $maxWeek = intval($maxDate->format('W'));

            /**
             * ★ startDate の週番号
             * （年をまたぐので単純に W では範囲のズレが出る）
             * → 秒数で週数差分を算出する
             */
            $weekStartNum = intval($startDate->format('W'));

            // ループ回数 = startDate 〜 maxDate の週数
            $totalWeeks = $startDate->diffInWeeks($maxDate) + 1;

            for ($i = 0; $i < $totalWeeks; $i++) {

                $weekStart = $startDate->copy()->addWeeks($i);
                $weekEnd   = $weekStart->copy()->endOfWeek();

                $w = intval($weekStart->format('W'));

                $cur = $currentData->get($w);
                $pre = $prevData->get($w);

                $sales       = $cur->total_sales ?? 0;
                $profit      = $cur->total_profit ?? 0;
                $sales_prev  = $pre->total_sales ?? 0;
                $profit_prev = $pre->total_profit ?? 0;

                /** 週ラベル */
                $periodLabel = $cur && $cur->max_date
                    ? Carbon::parse($cur->max_date)->format('y/m/d')
                    : $weekEnd->format('y/m/d');

                $rows[] = [
                    'period'      => $periodLabel,
                    'sales'       => $sales,
                    'profit'      => $profit,
                    'sales_prev'  => $sales_prev,
                    'profit_prev' => $profit_prev,
                    'sales_rate'  => $sales_prev ? round($sales / $sales_prev * 100, 1) : null,
                    'profit_rate' => $profit_prev ? round($profit / $profit_prev * 100, 1) : null,
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
