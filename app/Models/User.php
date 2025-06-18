<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class User extends Authenticatable implements JWTSubject
{
    use CrudTrait, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ✅ JWT Required Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // ✅ Roles (Many-to-Many)
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }
    public function favoriteMovies()
    {
        return $this->belongsToMany(Movie::class, 'user_favorite_movies');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}