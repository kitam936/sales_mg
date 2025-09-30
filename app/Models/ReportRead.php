<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Report;
use App\Models\User;

class ReportRead extends Model
{
    protected $fillable = [
        'id',
        'repot_id',
        'user_id',
    ];

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
