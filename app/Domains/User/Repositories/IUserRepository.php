<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\DTOs\UserStoreDTO;
use App\Domains\User\Models\User;

interface IUserRepository
{
    public function show(int $id): User;

    public function store(UserStoreDTO $data): User;

    public function update(array $data, int $id): User;
}
