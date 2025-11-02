<?php

namespace App\Domains\Wallet\Repositories;

use App\Domains\Wallet\DTOs\UpdateBalanceDTO;
use App\Domains\Wallet\DTOs\WalletStoreDTO;
use App\Domains\Wallet\Enums\TransactionType;
use App\Domains\Wallet\Models\Wallet;
use Carbon\Carbon;

interface IWalletRepository
{
    public function store(WalletStoreDTO $walletStoreDto): Wallet;

    public function updateBalance(UpdateBalanceDTO $updateBalanceDTO): void;

    public function getDailyTransactionTotalByType(Wallet $wallet,  TransactionType $transactionType): float;
}
