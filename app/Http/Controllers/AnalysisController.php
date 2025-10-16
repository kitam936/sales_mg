<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\OrderSubtotal;
use App\Models\Scopes\Subtotal;
use Illuminate\Support\Facades\DB;
use App\Services\DecileService;


class AnalysisController extends Controller
{
    public function index()
    {
        $start_date = '2025-05-01';
        $end_date = '2025-8-31';
        $subQuery = OrderSubtotal::betweenDate($start_date, $end_date)
            ->groupBy('id')
            ->selectRaw('id,sum(subtotal) as total,
            customer_name,
            item_id,
            prod_code,
            item_name,
            item_price,
            item_pcs,
            work_fee,
            subtotal,
            DATE_FORMAT(pitin_date,"%Y%m%d") as pitin_date,
            created_at');

        $data = DB::table($subQuery)
            ->groupBy('pitin_date')
            ->selectRaw('pitin_date, sum(total) as total')
            ->get();

            // dd($data);


            // dd( $period);
        return Inertia::render('Analysis', [
            'title' => '分析',
            'description' => '分析ページの説明',
            'data' => $data,
        ]);
    }

    public function test(Request $request)
    {
        $startDate = '2018-05-01';
        $endDate = '2025-8-31';

        //1 Idで集計
        $subQuery = OrderSubtotal::betweenDate($startDate, $endDate)
            ->groupBy('id', 'user_id', 'customer_name')
            ->selectRaw('id,
                user_id as customer_id,
                customer_name,
                sum(subtotal) as totalOrder');
        //2　UserIdで集計
        $subQuery = DB::table($subQuery)
            ->groupBy('customer_id','customer_name')
            ->selectRaw('customer_id, customer_name, sum(totalOrder) as total')
            ->orderBy('total', 'desc')
            ;
            // ->get();

        // dd($subQuery);

        //3 購入金額順に連番を振る
        DB::statement('SET @row_num = 0');
        $subQuery = DB::table($subQuery)
            ->selectRaw('customer_id, customer_name, total, (@row_num := @row_num + 1) AS row_num')
            ->orderBy('total', 'desc')
            ;
            // ->get( );

        // dd($subQuery);

        //4 全体の件数を取得し　1／10の値や合計を取得
        $count = DB::table($subQuery)->count();
        $total = DB::table($subQuery)->selectRaw('sum(total) as total')->get();
        $total = $total[0]->total; //構成比用

        $decile = ceil($count / 10); //1/10の件数

        $bindValues = [];
        $tempValue = 0;

        for ($i = 1; $i <= 10; $i++) {
            array_push($bindValues, 1+$tempValue);
            $tempValue += $decile;
            array_push($bindValues, 1+$tempValue);
        }

        // dd($count,$decile,$bindValues);

        //5 10件ごとにグループ化

        DB::statement('SET @row_num = 0;');
        $subQuery = DB::table($subQuery)
            ->selectRaw('row_num,customer_id, customer_name, total,
                CASE
                    WHEN ? <= row_num and row_num < ? THEN "1"
                    WHEN ? <= row_num and row_num < ? THEN "2"
                    WHEN ? <= row_num and row_num < ? THEN "3"
                    WHEN ? <= row_num and row_num < ? THEN "4"
                    WHEN ? <= row_num and row_num < ? THEN "5"
                    WHEN ? <= row_num and row_num < ? THEN "6"
                    WHEN ? <= row_num and row_num < ? THEN "7"
                    WHEN ? <= row_num and row_num < ? THEN "8"
                    WHEN ? <= row_num and row_num < ? THEN "9"
                    WHEN ? <= row_num and row_num < ? THEN "10"
                END AS decile', $bindValues);
                // ->get();

        // dd($subQuery);

        //6 グループ化したデータを集計
        $subQuery = DB::table($subQuery)
            ->groupBy('decile')
            ->selectRaw('decile, sum(total) as totalPerGroup, round(avg(total)) as avg')
            ->orderByRaw('CAST(decile AS UNSIGNED) ASC') // 数値としてソート
            ;
            // ->get();

        // dd($subQuery);

        // 7 構成比を計算
        DB::statement("SET @total = {$total};");
        $data = DB::table($subQuery)
            ->selectRaw('decile, totalPerGroup, avg, round(totalPerGroup / @total * 100, 2) as ratio')
            ->get();
        dd($data);
    }
}
