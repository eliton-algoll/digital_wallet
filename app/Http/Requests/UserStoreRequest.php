<?php

namespace App\Http\Requests;

use App\Domains\User\DTOs\UserStoreDTO;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'email' => ['required', 'email', 'max:254', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ];
    }

    public function getValidatedData(): UserStoreDto
    {
        $data = $this->validated();

        return new UserStoreDto(
            name: $data['name'],
            email: $data['email'],
            password: $data['password']
        );
    }
}
