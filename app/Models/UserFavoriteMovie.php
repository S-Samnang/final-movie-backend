<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFavoriteMovie extends Model
{
    protected $table = 'user_favorite_movies';

    protected $fillable = ['user_id', 'movie_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}