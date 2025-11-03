<?php

namespace App\Infrastructure\Repositories\Wallet;

use App\Domains\Wallet\DTOs\UpdateBalanceDTO;
use App\Domains\Wallet\DTOs\WalletStoreDTO;
use App\Domains\Wallet\Enums\TransactionType;
use App\Domains\Wallet\Enums\WalletBalanceAction;
use App\Domains\Wallet\Models\Wallet;
use App\Domains\Wallet\Repositories\IWalletRepository;
use Carbon\Carbon;

class WalletRepository implements IWalletRepository
{
    public function store(WalletStoreDTO $walletStoreDto): Wallet
    {
       return Wallet::create($walletStoreDto->toArray());
    }

    public function updateBalance(UpdateBalanceDTO $updateBalanceDTO): void
    {
        $wallet = $updateBalanceDTO->wallet;

        if ($updateBalanceDTO->balanceAction === WalletBalanceAction::CREDIT) {
            $wallet->increment('balance', $updateBalanceDTO->amount);

            return;
        }

        $wallet->decrement('balance', $updateBalanceDTO->amount);
    }

    public function getDailyTransactionTotalByType(Wallet $wallet, TransactionType $transactionType): float
    {
        return $wallet->transactions()
            ->where('type', $transactionType)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');
    }
}
