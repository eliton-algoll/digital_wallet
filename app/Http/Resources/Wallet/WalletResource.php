<?php

namespace App\Http\Resources\Wallet;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    public function toArray($request): array
    {
        $user = $this->user;

        return [
            'name' => $user->name,
            'email' => $user->email,
            'balance' => $this->balance,
        ];
    }
}
