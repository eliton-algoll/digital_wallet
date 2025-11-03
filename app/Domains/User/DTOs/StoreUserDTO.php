<?php

namespace App\Domains\User\DTOs;

use Ramsey\Uuid\Uuid;

final readonly class StoreUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'uuid' => Uuid::uuid4()->toString(),
            'password' => $this->password,
        ];
    }
}
