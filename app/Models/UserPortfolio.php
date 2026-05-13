<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPortfolio extends Model
{
    protected $fillable = ['user_id', 'symbol', 'lot', 'avg_price'];
    protected $casts = ['avg_price' => 'decimal:2'];

    public function user() { return $this->belongsTo(User::class); }

    public function getCurrentValueAttribute($currentPrice): float
    {
        return $this->lot * 100 * $currentPrice;
    }

    public function getProfitLossAttribute($currentPrice): float
    {
        return ($currentPrice - $this->avg_price) * $this->lot * 100;
    }
}