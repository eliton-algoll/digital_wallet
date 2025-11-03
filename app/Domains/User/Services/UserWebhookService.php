<?php

namespace App\Domains\User\Services;

use App\Domains\User\DTOs\StoreUserWebhookDTO;
use App\Domains\User\Models\UserWebhook;
use App\Domains\User\Repositories\IUserWebhookRepository;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class UserWebhookService
{
    public function __construct(
        private readonly IUserWebhookRepository $userWebhookRepository,
        private readonly LoggerInterface $logger
    )
    { }

    public function store(StoreUserWebhookDTO $storeUserWebhookDto): UserWebhook {
        return DB::transaction(function () use ($storeUserWebhookDto) {
            try {
                return $this->userWebhookRepository->store($storeUserWebhookDto);
            } catch (Throwable $th) {
                $this->logger->error(sprintf('[%s] Error storing user webhook', __METHOD__), [
                    'store_user_webhook_dto' => $storeUserWebhookDto->toArray(),
                    'error' => $th->getMessage(),
                    'exception' => $th,
                ]);

                throw new RuntimeException('Unexpected error storing user webhook' );
            }
        });
    }
}
