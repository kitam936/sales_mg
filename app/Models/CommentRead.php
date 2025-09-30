<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Comment;
use App\Models\User;

class CommentRead extends Model
{
    protected $fillable = [
        'id',
        'comment_id',
        'user_id',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
