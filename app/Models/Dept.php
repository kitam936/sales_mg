<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Dept extends Model
{
    /** @use HasFactory<\Database\Factories\DeptFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'dept_name',
        'dept_info',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
