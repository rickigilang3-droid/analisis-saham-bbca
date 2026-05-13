<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id', 'type', 'stock', 'lot', 'price', 'total'];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
        'lot'   => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}