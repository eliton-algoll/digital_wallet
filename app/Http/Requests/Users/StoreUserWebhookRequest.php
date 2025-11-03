<?php

namespace App\Http\Requests\Users;

use App\Domains\User\DTOs\StoreUserWebhookDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserWebhookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'url' => ['required', 'string', 'max:255'],
            'headers' => ['sometimes', 'string'],
            'secret' => ['sometimes', 'string', 'max:255'],
        ];
    }

    public function getValidatedData(): StoreUserWebhookDTO
    {
        $data = $this->validated();

        $user = $this->user();

        return new StoreUserWebhookDTO(
            user: $user,
            url: $data['url'],
            secret: $data['secret'] ?? null,
            headers: $data['headers'] ?? null
        );
    }
}
