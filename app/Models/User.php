<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Shop;
use App\Models\Dept;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id' ,
        'name' ,
        'email' ,
        'password' ,
        'user_info' ,
        'role_id' ,
        'dept_id' ,
        'mailService' ,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeSearchUsers($query, $input = null)
        {
            if(!empty($input)){
                if(User::where('name', 'like', '%'.$input . '%' )
                ->orWhere('user_info', 'like', '%'.$input . '%')->exists())
                {
                return $query->where('name', 'like', '%'.$input . '%' )
                ->orWhere('user_info', 'like', '%'.$input . '%');
                }
            }
        }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function dept()
    {
        return $this->belongsTo(Dept::class, 'dept_id');
    }

}
