<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;

class Subtotal implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $sql=DB::table('sales')
        ->join('shops','sales.shop_id','=','shops.id')
        ->join('areas','areas.id','=','shops.area_id')
        ->join('companies','companies.id','=','shops.company_id')
        ->join('hinbans','hinbans.id','=','sales.hinban_id')
        ->join('brands','brands.id','=','hinbans.brand_id')
        ->join('units','units.id','=','hinbans.unit_id')
        ->select('sales.id',
        'sales.sales_date',
        'shops.company_id',
        'companies.co_name',
        'companies.pic_id',
        'sales.shop_id',
        'shops.shop_name',
        'shops.area_id',
        'areas.area_name',
         'sales.YM',
         'sales.YW',
         'sales.YMD',
         'sales.Y',
         'sales.hinban_id',
         'hinbans.hinban_name',
         'hinbans.price',
         'hinbans.m_price',
         'hinbans.brand_id',
         'brands.brand_name',
         'sales.pcs',
         'sales.kingaku',
         'sales.arari',
         'hinbans.unit_id',
         'units.unit_code',
         'units.season_id',
         'units.season_name',
         'hinbans.face',
         'hinbans.designer_id',
         'hinbans.vendor_id',
         'sales.created_at')
        ;

        $builder->fromSub($sql,'salesdata_subtotal');
    }


}
