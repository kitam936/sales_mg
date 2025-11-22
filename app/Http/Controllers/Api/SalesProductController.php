<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SalesData;
use Illuminate\Support\Facades\DB;

class SalesProductController extends Controller
{
    public function index(Request $request)
    {
        // ランキング用
        if ($request->filled('type') && $request->type === 'ranking') {

            // 1. sales を品番ごとに集計（社・店舗で絞込み可能）
            $salesSub = DB::table('sales')
                ->select('hinban_id', DB::raw('SUM(pcs) as pcs_total'))
                ->when($request->filled('startDate') && $request->filled('endDate'), function($q) use ($request){
                    $q->whereBetween('sales_date', [$request->startDate, $request->endDate]);
                })
                ->when($request->filled('company_id'), function($q) use ($request){
                    $q->whereIn('shop_id', function($sub){
                        $sub->select('id')
                            ->from('shops')
                            ->where('company_id', request('company_id'));
                    });
                })
                ->when($request->filled('shop_id'), function($q) use ($request){
                    $q->where('shop_id', $request->shop_id);
                })
                ->groupBy('hinban_id');

            // 2. images から品番ごとに最新1件取得
            $imageSub = DB::table('images as i1')
                ->select('i1.hinban_id','i1.filename')
                ->whereIn('i1.created_at', function($query){
                    $query->select(DB::raw('MAX(i2.created_at)'))
                          ->from('images as i2')
                          ->whereColumn('i2.hinban_id','i1.hinban_id');
                });

            // 3. hinbans に JOIN
            $query = DB::table('hinbans')
                ->joinSub($salesSub,'sales_sum', function($join){
                    $join->on('hinbans.id','=','sales_sum.hinban_id');
                })
                ->leftJoinSub($imageSub,'img', function($join){
                    $join->on('hinbans.id','=','img.hinban_id');
                })
                ->leftJoin('units','hinbans.unit_id','=','units.id')
                ->leftJoin('brands','hinbans.brand_id','=','brands.id')
                ->leftJoin('faces','hinbans.face','=','faces.id')
                ->where('hinbans.vendor_id','<>',8200)
                ->select(
                    'hinbans.id as hinban_id',
                    'hinbans.hinban_name',
                    'hinbans.m_price',
                    'img.filename',
                    'units.season_id',
                    'units.season_name',
                    'units.unit_code',
                    'hinbans.designer_id',
                    'hinbans.face as face_name',
                    'sales_sum.pcs_total',
                    'brands.brand_name'
                );

            // 4. マスタ側フィルター
            foreach (['brand_id','season_id','unit_id','face','designer_id'] as $field) {
                if ($request->filled($field)) {
                    switch ($field) {
                        case 'brand_id':
                            $query->where('brands.id', $request->$field);
                            break;
                        case 'season_id':
                            $query->where('units.season_id', $request->$field);
                            break;
                        case 'unit_id':
                            $query->where('units.id', $request->$field);
                            break;
                        case 'face':
                            $query->where('hinbans.face', $request->$field);
                            break;
                        case 'designer_id':
                            $query->where('hinbans.designer_id', $request->$field);
                            break;
                    }
                }
            }

            $query->where('sales_sum.pcs_total','>',0)
                  ->orderByDesc('sales_sum.pcs_total');

           // 5. ページネーション
            $perPage = 50;
            $page = $request->page ?? 1;

            $items = $query->distinct()->paginate($perPage, ['*'], 'page', $page);

            // ⭐ このまままるごと返せば、links が Laravel 形式で返る
            return response()->json($items);


            // 6. ページネーションリンクをVue向けに整形
            // $items->getCollection()->transform(function($item){
            //     return $item;
            // });

            return response()->json([
                'data' => $items->items(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'links' => [
                    'next' => $items->nextPageUrl(),
                    'prev' => $items->previousPageUrl(),
                ]
            ]);
        }

        // 通常一覧
        $query = DB::table('sales')->orderBy('id','desc')->paginate(50);

        return response()->json($query);
    }
}
