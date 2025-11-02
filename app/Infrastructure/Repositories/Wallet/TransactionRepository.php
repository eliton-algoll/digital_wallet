<?php

namespace App\Infrastructure\Repositories\Wallet;

use App\Domains\Wallet\DTOs\TransactionStoreDTO;
use App\Domains\Wallet\Models\Transaction;
use App\Domains\Wallet\Repositories\ITransactionRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public function list(int $walletId, array $filters, array $sortBy, int $perPage): LengthAwarePaginator
    {
        $queryBuilder = Transaction::query()
            ->where('wallet_id', $walletId);

        $this->applyFilters($queryBuilder, $filters);

        $this->applyOrder($queryBuilder, $sortBy);

        return $queryBuilder->paginate($perPage);
    }

    private function applyFilters(Builder $builder, array $filters): void
    {
        $builder
            ->when(!empty($filters['created_at']), fn(Builder $queryBuilder) => $queryBuilder->whereDate('created_at', '>=', $filters['created_at']))
            ->when(!empty($filters['type']), fn(Builder $queryBuilder) => $queryBuilder->where('type', $filters['type']));
    }

    private function applyOrder(Builder $builder, array $sortBy): void
    {
        $column = $sortBy['sort_by'] ?? 'created_at';
        $direction = $sortBy['direction'] ?? 'desc';

        $builder->orderBy($column, $direction);
    }
}
