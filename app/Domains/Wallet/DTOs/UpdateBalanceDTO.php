<?php

namespace App\Domains\Wallet\DTOs;

use App\Domains\Wallet\Enums\WalletBalanceAction;
use App\Domains\Wallet\Models\Wallet;

final readonly class UpdateBalanceDTO
{
    public function __construct(
        public readonly Wallet $wallet,
        public readonly WalletBalanceAction $balanceAction,
        public readonly float $amount,
    ) {
    }

    public function toArray(): array
    {
        return [
            'wallet' => $this->wallet,
            'balance_action' => $this->balanceAction,
            'amount' => $this->amount,
        ];
    }
}
