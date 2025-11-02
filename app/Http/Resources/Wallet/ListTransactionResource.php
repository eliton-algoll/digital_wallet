<?php

namespace App\Http\Resources\Wallet;

use Illuminate\Http\Resources\Json\JsonResource;

class ListTransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'amount' => $this->amount,
            'type' => $this->type->value,
            'created_at' => $this->created_at,
            'recipient' => $this->transferredWallet ? [
                'name' => $this->transferredWallet->user->name,
                'email' => $this->transferredWallet->user->email
            ] : null,
        ];
    }
}
