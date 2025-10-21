<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\Area;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Report;

class Shop extends Model
{
    /** @use HasFactory<\Database\Factories\ShopFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'shop_name',
        'company_id',
        'shop_info',
        'area_id',
        'is_selling',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function sale()
    {
        return $this->hasMany(Sale::class);
    }

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

    public function report()
    {
        return $this->hasMany(Report::class);
    }

    public function scopeSearchShops($query, $search)
    {
        if ($search) {
            return $query->where('shop_name', 'like', '%' . $search . '%')
                         ->orWhere('shop_info', 'like', '%' . $search . '%');
        }
        return $query;
    }
}
