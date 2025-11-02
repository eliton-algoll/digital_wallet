<?php

namespace App\Domains\Wallet\DTOs;

use App\Domains\User\Models\User;
use App\Domains\Wallet\Enums\TransactionType;

final readonly class TransactionStoreDTO
{
    public function __construct(
        public readonly User $user,
        public readonly float $amount,
        public readonly TransactionType $type,
        public readonly ?int $transferredWalletId = null
    ) {
    }

    public function toArray(): array {
        return [
            'wallet_id' => $this->user->wallet->id,
            'amount' => $this->amount,
            'type' => $this->type,
            'transferred_wallet_id' => $this->transferredWalletId,
        ];
    }
}
