<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserStoreResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'balance' => $this->wallet->balance,
            'daily_withdrawal_limit' => $this->wallet->daily_withdrawal_limit,
            'daily_deposit_limit' => $this->wallet->daily_deposit_limit,
            'created_at' => $this->created_at,
        ];
    }
}
