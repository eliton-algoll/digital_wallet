<?php

namespace App\Infrastructure\Repositories\Wallet;

use App\Domains\Wallet\DTOs\WalletStoreDTO;
use App\Domains\Wallet\Models\Wallet;
use App\Domains\Wallet\Repositories\IWalletRepository;

class WalletRepository implements IWalletRepository
{

    public function store(WalletStoreDTO $walletStoreDto): Wallet
    {
       return Wallet::create($walletStoreDto->toArray());
    }
}
