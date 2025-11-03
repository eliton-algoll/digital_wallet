<?php

namespace App\Http\Requests\Wallet;

use App\Domains\Wallet\DTOs\TransferDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class TransferRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'recipient' => ['required', 'email', 'exists:users,email'],
            'amount' => ['required', 'numeric', 'gt:0'],
        ];
    }

    /**
     * @throws ValidationException
     */
    public function getValidatedData(): TransferDTO
    {
        $data = $this->validated();

        $user = $this->user();

        if ($user->email === $data['recipient']) {
            $validator = $this->getValidatorInstance();

            $validator->errors()->add(
                'recipient',
                'You cannot transfer funds to yourself.'
            );

            throw new ValidationException($validator);
        }

        return new TransferDTO(
            user: $this->user(),
            amount: $data['amount'],
            recipient: $data['recipient'],
        );
    }
}
