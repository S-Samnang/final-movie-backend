<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use CrudTrait;
    protected $fillable = [
        'user_id',
        'movie_id',
        'rating',
        'comment',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}