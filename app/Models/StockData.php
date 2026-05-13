<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockData extends Model
{
    protected $table = 'stock_data';

    protected $fillable = [
        'symbol', 'trading_date', 'open_price', 'high_price',
        'low_price', 'close_price', 'volume', 'adj_close',
    ];

    protected $casts = [
        'trading_date' => 'date',
        'open_price'   => 'decimal:2',
        'high_price'   => 'decimal:2',
        'low_price'    => 'decimal:2',
        'close_price'  => 'decimal:2',
        'adj_close'    => 'decimal:2',
    ];

    public function indicator()
    {
        return $this->hasOne(StockIndicator::class);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('trading_date', '>=', now()->subDays($days))->orderBy('trading_date');
    }
}