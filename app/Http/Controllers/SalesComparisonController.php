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

        // 期間設定（月：12ヶ月 / 週：52週）
        if ($compareType === 'monthly') {
            $startDate = $today->copy()->subMonths(11)->startOfMonth(); // 直近12ヶ月
            $endDate = $today->copy()->endOfMonth();

        } else { // weekly
            $startDate = $today->copy()->subWeeks(51)->startOfWeek(); // 月曜始まり
            $endDate = $today->copy()->endOfWeek(); // 日曜終わり
        }

        // 前年期間
        $prevStart = $startDate->copy()->subYear();
        $prevEnd   = $endDate->copy()->subYear();

        $companies = Company::all();
        $shops = $company_id ? Shop::where('company_id', $company_id)->get() : collect();

        /**
         * ============================
         *  DB一括取得（現年＋前年）
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

        // 現年 / 前年に振り分け
        $currentData = $query->where('is_current', 1)->keyBy('period');
        $prevData = $query->where('is_current', 0)->keyBy('period');

        /**
         * ============================
         *  表示行作成
         * ============================
         */
        $rows = [];

        /**
         * ----------------------------
         *  月別 12ヶ月
         * ----------------------------
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
         * ----------------------------
         *  週別 52週
         * ----------------------------
         * 週は必ず「月曜始まり」
         * 週ラベルは
         *   現年→ max_date があればそれを使用（その週の最大日付）
         *   無ければ → Carbonでその週の日曜を計算
         */
        } else {

            for ($i = 0; $i < 52; $i++) {

                $weekStart = $startDate->copy()->addWeeks($i);
                $weekEnd   = $weekStart->copy()->endOfWeek(); // 日曜

                $w = intval($weekStart->format('W')); // ISO-8601（月曜始まり）

                $cur = $currentData->get($w);
                $pre = $prevData->get($w);

                $sales       = $cur->total_sales ?? 0;
                $profit      = $cur->total_profit ?? 0;
                $sales_prev  = $pre->total_sales ?? 0;
                $profit_prev = $pre->total_profit ?? 0;

                // ★週ラベル：DBの max_date（その週の最大日付）→ 無ければ週末の日曜
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
