<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Face extends Model
{
    /** @use HasFactory<\Database\Factories\FaceFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'face_code',
        'face_item',

    ];
}
