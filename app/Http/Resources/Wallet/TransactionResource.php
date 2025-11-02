<?php

namespace App\Http\Resources\Wallet;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
        ];
    }
}
