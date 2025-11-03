<?php

namespace App\Infrastructure\Repositories\Users;

use App\Domains\User\DTOs\StoreUserWebhookDTO;
use App\Domains\User\Models\UserWebhook;
use App\Domains\User\Repositories\IUserWebhookRepository;

class UserWebhookRepository implements IUserWebhookRepository
{
    public function store(StoreUserWebhookDTO $storeUserWebhookDTO): UserWebhook
    {
        return UserWebhook::create($storeUserWebhookDTO->toArray());
    }
}
