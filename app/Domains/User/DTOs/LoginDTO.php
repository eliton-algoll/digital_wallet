<?php

namespace App\Domains\User\DTOs;

final readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }

    public function toArray(): array {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
