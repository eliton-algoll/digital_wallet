<?php

namespace App\Http\Requests;

use App\Domains\User\DTOs\LoginDTO;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function getValidatedData(): LoginDTO
    {
        $data = $this->validated();

        return new LoginDTO(
            email: $data['email'],
            password: $data['password']
        );
    }
}
