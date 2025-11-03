<?php

namespace App\Infrastructure\Communication\Webhook\DTOs;

final readonly class SendWebhookRequestDTO
{
    public function __construct(
        public readonly string $url,
        public readonly array $payload,
        public readonly ?array $headers = null,
        public readonly ?string $secret = null,
    ) {
    }

    public function toArray(): array {
        return [
            'url' => $this->url,
            'payload' => $this->payload,
            'headers' => $this->headers,
            'secret' => $this->secret,
        ];
    }
}
