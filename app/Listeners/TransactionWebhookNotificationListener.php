<?php

namespace App\Listeners;

use App\Events\TransactionCompletedEvent;
use App\Jobs\SendTransactionWebhookJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TransactionWebhookNotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle(TransactionCompletedEvent $transactionCompletedEvent): void
    {
        $transaction = $transactionCompletedEvent->getTransaction();
        $user = $transaction->wallet->user;

        $webhooks = $user->userWebhooks;

        foreach ($webhooks as $webhook) {

            SendTransactionWebhookJob::dispatch($webhook, [
                'event' => 'transaction.completed',
                'data' => [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'transaction' => [
                        'id' => $transaction->uuid,
                        'type' => $transaction->type->value,
                        'amount' => $transaction->amount,
                        'recipient' => $transaction->transferredWallet ? [
                            'name' => $transaction->transferredWallet->user->name,
                            'email' => $transaction->transferredWallet->user->email,
                        ] : [],
                        'created_at' => $transaction->created_at,
                    ]
                ],
            ]);
        }
    }
}
