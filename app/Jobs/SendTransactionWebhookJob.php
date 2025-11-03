<?php

namespace App\Jobs;

use App\Domains\User\Models\UserWebhook;
use App\Infrastructure\Communication\Webhook\DTOs\SendWebhookRequestDTO;
use App\Infrastructure\Communication\Webhook\Services\WebhookService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Psr\Log\LoggerInterface;
use Throwable;

class SendTransactionWebhookJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected UserWebhook $userWebhook,
        protected array $payload

    ) {
    }

    /**
     * @throws Throwable
     */
    public function handle(
        WebhookService  $webhookService,
        LoggerInterface $logger
    ): void
    {
        $logger->info(sprintf('[%s] Sending webhook', __METHOD__), [
            'payload' => $this->payload,
        ]);

        $headers = $this->userWebhook->headers ? json_decode($this->userWebhook->headers) : null;

        $sendWebhookRequestDto = new SendWebhookRequestDTO(
            url: $this->userWebhook->url,
            payload: $this->payload,
            headers: $headers,
            secret: $this->userWebhook->secret,
        );

        try {
            $webhookService->send($sendWebhookRequestDto);
        } catch (Throwable $th) {
            $logger->error(sprintf('[%s] Error sending webhook', __METHOD__), [
                'send_webhook_request_dto' => $sendWebhookRequestDto->toArray(),
                'message' => $th->getMessage(),
                'exception' => $th,
            ]);

            throw $th;
        }
    }
}
