<?php

namespace App\Domains\Wallet\DTOs;

use App\Domains\User\Models\User;

final readonly class WalletStoreDTO
{
    public function __construct(
        public readonly User $user,
    ) {
    }

    public function toArray(): array {
        return [
            'user_id' => $this->user->id,
        ];
    }
}
