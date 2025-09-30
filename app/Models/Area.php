<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Shop;

class Area extends Model
{
    /** @use HasFactory<\Database\Factories\AreaFactory> */
    use HasFactory;
    protected $fillable = [
        'id',
        'area_name',
        'area_info',
    ];

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
