<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreUserWebhookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'url' => $this->url,
            'headers' => $this->headers,
            'secret' => $this->secret,
            'user'=> [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]
        ];
    }
}
