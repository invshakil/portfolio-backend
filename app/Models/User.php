<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    public const ADMIN = 1;
    public const STAFF = 2;
    public const USER = 3;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * @return HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function getUserTypeAttribute(): string
    {
        switch (auth()->user()->role) {
            case User::ADMIN:
                $type = 'admin';
                break;
            case User::STAFF:
                $type = 'staff';
                break;
            default:
                $type = 'user';
        }
        return $type;
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword(): string
    {
        return $this->attributes['password'];
    }

    protected $appends = ['name'];


    public function getNameAttribute(): string
    {
        return  $this->attributes['name'];
    }

    public function getTitleAttribute(): string
    {
        return $this->attributes['role'] == 'admin' ? 'admin' : 'user';
    }
}
