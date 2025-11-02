<?php

namespace App\Infrastructure\Repositories\Wallet;

use App\Domains\Wallet\DTOs\TransactionStoreDTO;
use App\Domains\Wallet\Models\Transaction;
use App\Domains\Wallet\Repositories\ITransactionRepository;
use Ramsey\Uuid\Uuid;

class TransactionRepository implements ITransactionRepository
{
    public function store(TransactionStoreDTO $depositDTO): Transaction
    {
        return Transaction::create([
            ...$depositDTO->toArray(),
            'uuid' => Uuid::uuid4()->toString(),
        ]);
    }
}
