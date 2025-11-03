<?php

namespace App\Http\Requests\Users;

use App\Domains\User\DTOs\StoreUserDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'email' => ['required', 'email', 'max:254', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function getValidatedData(): StoreUserDTO
    {
        $data = $this->validated();

        return new StoreUserDTO(
            name: $data['name'],
            email: $data['email'],
            password: $data['password']
        );
    }
}
