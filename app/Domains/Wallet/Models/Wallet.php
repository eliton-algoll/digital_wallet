<?php

namespace App\Domains\Wallet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'daily_withdrawal_limit',
        'daily_deposit_limit',
    ];

    protected $attributes = [
        'balance' => 0,
        'daily_withdrawal_limit' => 1000,
        'daily_deposit_limit' => 10000,
    ];

    protected $casts = [
        'balance' => 'float',
        'daily_withdrawal_limit' => 'float',
        'daily_deposit_limit' => 'float',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
