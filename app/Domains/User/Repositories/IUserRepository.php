<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\DTOs\StoreUserDTO;
use App\Domains\User\Models\User;

interface IUserRepository
{
    public function store(StoreUserDTO $data): User;

    public function findByEmail(string $email): User;
}
