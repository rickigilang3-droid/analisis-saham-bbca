<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmitenEvent extends Model
{
    protected $fillable = ['stock_symbol', 'title', 'description', 'type', 'event_date', 'value'];

    protected $casts = [
        'event_date' => 'date',
    ];
}