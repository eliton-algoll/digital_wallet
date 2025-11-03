<?php

namespace App\Domains\User\DTOs;

use App\Domains\User\Models\User;
use Ramsey\Uuid\Uuid;

final readonly class StoreUserWebhookDTO
{
    public function __construct(
        public readonly User $user,
        public readonly string $url,
        public readonly ?string $secret = null,
        public readonly ?string $headers = null,
    ) {
    }

    public function toArray(): array {
        return [
            'user_id' => $this->user->id,
            'uuid' => Uuid::uuid4()->toString(),
            'url' => $this->url,
            'secret' => $this->secret,
            'headers' => $this->headers,
        ];
    }
}
