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
    ];
    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'actor_movie');
    }
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}