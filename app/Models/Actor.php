<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    use CrudTrait;
    protected $fillable = [
        'tmdb_id',
        'name',
        'original_name',
        'profile_path',
        'character',
        'adult',
        'gender',
        'known_for_department',
        'popularity',
        'cast_id',
        'credit_id',
        'order',
        'status',
        'also_known_as',
        'biography',
        'birthday',
        'deathday',
        'homepage',
        'place_of_birth',
        'imdb_id',
    ];

    protected $casts = [
        'also_known_as' => 'array',
    ];
    
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'actor_movie', 'actor_id', 'movie_id');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
