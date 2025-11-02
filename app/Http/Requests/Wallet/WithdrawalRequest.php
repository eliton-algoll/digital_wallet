<?php

namespace App\Http\Requests\Wallet;

use App\Domains\Wallet\DTOs\TransactionStoreDTO;
use App\Domains\Wallet\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;

class WithdrawalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function getValidatedData(): TransactionStoreDTO
    {
        $data = $this->validated();

        return new TransactionStoreDTO(
            user: $this->user(),
            amount: $data['amount'],
            type: TransactionType::WITHDRAWAL,
        );
    }
}
