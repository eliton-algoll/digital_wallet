<?php

namespace App\Http\Requests\Wallet;

use App\Domains\Wallet\DTOs\TransferDTO;
use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'recipient' => ['required', 'email', 'exists:users,email'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function getValidatedData(): TransferDTO
    {
        $data = $this->validated();

        return new TransferDTO(
            user: $this->user(),
            amount: $data['amount'],
            recipient: $data['recipient'],
        );
    }
}
