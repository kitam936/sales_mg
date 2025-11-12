<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\Subtotal;
use Carbon\Carbon;

class SalesData extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new Subtotal);
    }

    protected $casts = [
        'kingaku' => 'integer',
        'arari' => 'integer',
    ];

    public function scopeBetweenDate($query, $startDate=null, $endDate=null)
    {

        if (is_null($startDate) && is_null($endDate)) {
            return $query;
        }

        if( !is_null($startDate) && is_null($endDate)) {
            return $query->where('sales_date', '>=', $startDate);
        }

        if( is_null($startDate) && !is_null($endDate)) {
            $endDate1 = Carbon::parse($endDate)->addDay(1);
            return $query->where('sales_date', '<=', $endDate1);
        }

        if( !is_null($startDate) && !is_null($endDate)) {
            $endDate1 = Carbon::parse($endDate)->addDay(1);
            return $query->where('sales_date','>=', $startDate)
                         ->where('sales_date', '<=', $endDate1);
        }

    }
}
