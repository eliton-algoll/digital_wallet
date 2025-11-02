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
            $wallet->balance += $updateBalanceDTO->amount;

            $wallet->save();

            return;
        }

        $wallet->balance -= $updateBalanceDTO->amount;

        $wallet->save();
    }

    public function getDailyDepositTotal(Wallet $wallet, Carbon $date): float
    {
        return $wallet->transactions()
            ->where('type', TransactionType::DEPOSIT)
            ->whereDate('created_at', $date)
            ->sum('amount');
    }
}
