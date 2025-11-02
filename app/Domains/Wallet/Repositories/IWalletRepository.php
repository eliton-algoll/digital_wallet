<?php

namespace App\Domains\Wallet\Repositories;

use App\Domains\Wallet\DTOs\WalletStoreDTO;
use App\Domains\Wallet\Models\Wallet;

interface IWalletRepository
{
    public function store(WalletStoreDTO $walletStoreDto): Wallet;
}
