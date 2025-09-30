<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'co_name',
        'co_info',
        'pic_id',
    ];


    public function shop()
    {
        return $this->hasMany(Shop::class);
    }
}
