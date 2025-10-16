<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\Subtotal;

class SalesData extends Model
{
    use HasFactory;

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
            return $query->where('date', '>=', $startDate);
        }

        if( is_null($startDate) && !is_null($endDate)) {
            $endDate1 = Carbon::parse($endDate)->addDay(1);
            return $query->where('date', '<=', $endDate1);
        }

        if( !is_null($startDate) && !is_null($endDate)) {
            $endDate1 = Carbon::parse($endDate)->addDay(1);
            return $query->where('date','>=', $startDate)
                         ->where('date', '<=', $endDate1);
        }

    }
}
