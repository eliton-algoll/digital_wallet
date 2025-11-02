<?php

namespace App\Domains\Wallet\Repositories;

use App\Domains\Wallet\DTOs\TransactionStoreDTO;
use App\Domains\Wallet\Models\Transaction;

interface ITransactionRepository
{
    public function store(TransactionStoreDTO $depositDTO): Transaction;
}
