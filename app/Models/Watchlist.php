<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    protected $fillable = ['user_id', 'symbol', 'buy_price', 'target_price', 'stop_loss', 'notes'];
    protected $casts = ['buy_price' => 'decimal:2', 'target_price' => 'decimal:2', 'stop_loss' => 'decimal:2'];

    public function user() { return $this->belongsTo(User::class); }
}