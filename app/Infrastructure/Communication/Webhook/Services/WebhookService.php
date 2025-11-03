<?php

namespace App\Infrastructure\Communication\Webhook\Services;


use App\Infrastructure\Communication\Webhook\DTOs\SendWebhookRequestDTO;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class WebhookService
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {
    }

    public function send(SendWebhookRequestDTO $sendWebhookRequestDTO): void
    {
        $client = new Client([
            'timeout' => 10,
            'verify' => false,
        ]);

        try {
            $response = $client->post($sendWebhookRequestDTO->url, [
                'json' => $sendWebhookRequestDTO->payload,
                'headers' => $this->buildHeaders($sendWebhookRequestDTO),
            ]);

            $this->logger->info(sprintf('[%s] Webhook sent', __METHOD__), [
                'send_webhook_request_dto' => $sendWebhookRequestDTO->toArray(),
                'response' => $response->getBody()->getContents(),
            ]);
        } catch (Throwable $th) {

            $this->logger->error(sprintf('[%s] Error sending webhook', __METHOD__), [
                'send_webhook_request_dto' => $sendWebhookRequestDTO->toArray(),
                'message' => $th->getMessage(),
            ]);

            throw $th;
        }
    }

    private function buildHeaders(SendWebhookRequestDTO $sendWebhookRequestDTO): array
    {
        $authorization = $sendWebhookRequestDTO->secret ? ['Authorization' => 'Bearer ' . $sendWebhookRequestDTO->secret] : [];
        $headers = !empty($sendWebhookRequestDTO->headers) ? $sendWebhookRequestDTO->headers : [];

        return [
            'Content-Type' => 'application/json',
            ...$authorization,
            ...$headers
        ];
    }
}
