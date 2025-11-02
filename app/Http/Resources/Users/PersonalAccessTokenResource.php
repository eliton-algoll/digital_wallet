<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonalAccessTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $accessToken = $this->accessToken;

        return [
            'user' => [
                'id' => $accessToken->tokenable->uuid,
                'name' => $accessToken->tokenable->name,
                'email' => $accessToken->tokenable->email,
            ],
            'token' => $this->plainTextToken,
            'expires_at' => $accessToken->expires_at
        ];
    }
}
