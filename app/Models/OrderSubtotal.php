<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\Subtotal;
use Carbon\Carbon;

class OrderSubtotal extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new Subtotal);
    }

    public function scopeBetweenDate($query, $startDate=null, $endDate=null)
    {
        if (is_null($startDate) && is_null($endDate)) {
            return $query;
        }

        if( !is_null($startDate) && is_null($endDate)) {
            return $query->where('pitin_date', '>=', $startDate);
        }

        if( is_null($startDate) && !is_null($endDate)) {
            $endDate1 = Carbon::parse($endDate)->addDay(1);
            return $query->where('pitin_date', '<=', $endDate1);
        }

        if( !is_null($startDate) && !is_null($endDate)) {
            $endDate1 = Carbon::parse($endDate)->addDay(1);
            return $query->where('pitin_date','>=', $startDate)
                         ->where('pitin_date', '<=', $endDate1);
        }

    }
}
