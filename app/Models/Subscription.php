<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_name',
        'amount',
        'currency',
        'reference',
        'status',
        'paid_at',
    ];

    // Relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}