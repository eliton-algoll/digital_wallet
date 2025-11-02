<?php

namespace App\Http\Resources\Wallet;

use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'amount' => $this->amount,
            'type' => $this->type->value,
            'wallet' => [
                'balance' => $this->wallet->balance
            ],
            'recipient' => [
                'name' => $this->transferredWallet->user->name,
                'email' => $this->transferredWallet->user->email
            ]
        ];
    }
}
