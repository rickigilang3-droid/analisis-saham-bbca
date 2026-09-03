<?php

namespace App\Models;

use App\Models\UserPortfolio;
use App\Models\Watchlist;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'status', 'is_active', 'balance', 'lots', 'avg_price', 'stock',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'balance'           => 'decimal:2',
        'avg_price'         => 'decimal:2',
        'lots'              => 'integer',
        'is_active'         => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    public function portfolios()
    {
        return $this->hasMany(UserPortfolio::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active' && ($this->is_active ?? true);
    }
}