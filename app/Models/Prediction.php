<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    protected $fillable = [
        'symbol', 'prediction_date', 'target_date', 'prediction_type',
        'signal', 'confidence', 'predicted_price', 'actual_price',
        'reasoning', 'raw_data',
    ];

    protected $casts = [
        'prediction_date' => 'date',
        'target_date'     => 'date',
        'raw_data'        => 'array',
        'confidence'      => 'decimal:2',
        'predicted_price' => 'decimal:2',
    ];

    public function getSignalBadgeAttribute(): string
    {
        return match ($this->signal) {
            'BUY'  => '<span class="badge-buy">🟢 BELI</span>',
            'SELL' => '<span class="badge-sell">🔴 JUAL</span>',
            'HOLD' => '<span class="badge-hold">🟡 TAHAN</span>',
            default => '-',
        };
    }
}