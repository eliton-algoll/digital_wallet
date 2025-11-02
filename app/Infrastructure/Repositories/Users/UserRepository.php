<?php

namespace App\Infrastructure\Repositories\Users;

use App\Domains\User\DTOs\UserStoreDTO;
use App\Domains\User\Models\User;
use App\Domains\User\Repositories\IUserRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository implements IUserRepository
{
    public function store(UserStoreDTO $data): User
    {
        return User::create($data->toArray());
    }

    public function findByEmail(string $email): User
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new ModelNotFoundException('User not found');
        }

       return $user;
    }
}
