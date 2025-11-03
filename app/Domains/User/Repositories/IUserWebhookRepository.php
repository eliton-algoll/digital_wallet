<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\DTOs\StoreUserWebhookDTO;
use App\Domains\User\Models\UserWebhook;

interface IUserWebhookRepository
{
    public function store(StoreUserWebhookDTO $storeUserWebhookDTO): UserWebhook;
}
