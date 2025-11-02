<?php

namespace App\Infrastructure\Repositories\Users;

use App\Domains\User\DTOs\UserStoreDTO;
use App\Domains\User\Models\User;
use App\Domains\User\Repositories\IUserRepository;

class UserRepository implements IUserRepository
{
    public function show(int $id): User
    {
        return User::findOrFail($id);
    }

    public function store(UserStoreDTO $data): User
    {
        return User::create($data->toArray());
    }

    public function update(array $data, int $id): User
    {
        $user = $this->show($id);

        $user->update($data);

        return $user;
    }

    public function delete(int $id): void
    {
        $user = $this->show($id);

        $user->delete();
    }

}
