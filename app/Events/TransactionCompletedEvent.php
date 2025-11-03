<?php

namespace App\Events;

use App\Domains\Wallet\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        private readonly Transaction $transaction,
    ) {
    }

    public function getTransaction(): Transaction {
        return $this->transaction;
    }
}
