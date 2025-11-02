<?php

namespace App\Domains\Wallet\Models;

use App\Domains\Wallet\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'uuid',
        'amount',
        'type',
        'wallet_id',
        'transferred_wallet_id',
    ];

    protected $casts = [
        'amount' => 'float',
        'type' => TransactionType::class,
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
