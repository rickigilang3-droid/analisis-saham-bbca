<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIndicator extends Model
{
    protected $fillable = [
        'stock_data_id', 'ma5', 'ma10', 'ma20', 'ma50', 'rsi',
        'macd', 'macd_signal', 'macd_histogram',
        'bollinger_upper', 'bollinger_middle', 'bollinger_lower',
    ];

    public function stockData()
    {
        return $this->belongsTo(StockData::class);
    }
}