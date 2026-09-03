<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmitenEvent extends Model
{
    protected $fillable = ['stock_symbol', 'title', 'description', 'type', 'event_date', 'value'];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function getDateAttribute()
    {
        if ($this->event_date instanceof \DateTime) {
            return $this->event_date->format('Y-m-d');
        }

        return $this->attributes['event_date'] ?? null;
    }
}