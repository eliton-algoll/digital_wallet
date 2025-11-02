<?php

namespace App\Domains\Wallet\DTOs;

use App\Domains\User\Models\User;

final readonly class TransferDTO
{
    public function __construct(
        public readonly User $user,
        public readonly float $amount,
        public readonly string $recipient
    ) {
    }

    public function toArray(): array {
        return [
            'wallet_id' => $this->user->wallet->id,
            'amount' => $this->amount,
            'recipient' => $this->recipient,
        ];
    }
}
