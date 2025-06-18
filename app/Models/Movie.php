<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use CrudTrait;
    protected $fillable = [
        'tmdb_id',
        'title',
        'original_title',
        'overview',
        'poster_path',
        'backdrop_path',
        'release_date',
        'original_language',
        'adult',
        'video',
        'vote_average',
        'vote_count',
        'popularity',
        'genre_ids',
        'type',
        'embed_url',
        'status',
    ];
    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'actor_movie', 'movie_id', 'actor_id');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'user_favorite_movies');
    }
}