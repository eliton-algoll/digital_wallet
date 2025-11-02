<?php

namespace App\Domains\Wallet\Repositories;

use App\Domains\Wallet\DTOs\TransactionStoreDTO;
use App\Domains\Wallet\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;

interface ITransactionRepository
{
    public function store(TransactionStoreDTO $depositDTO): Transaction;

    public function list(int $walletId, array $filters, array $sortBy, int $perPage): LengthAwarePaginator;
}
