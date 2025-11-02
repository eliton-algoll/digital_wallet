<?php

namespace App\Domains\Wallet\Services;

use App\Domains\Wallet\DTOs\WalletStoreDTO;
use App\Domains\Wallet\Models\Wallet;
use App\Domains\Wallet\Repositories\IWalletRepository;
use Psr\Log\LoggerInterface;
use Throwable;

class WalletService
{
    public function __construct(
        private readonly IWalletRepository $walletRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    public function store(WalletStoreDTO $walletStoreDto): Wallet
    {
        try {
            return $this->walletRepository->store($walletStoreDto);
        } catch(Throwable $th) {
            $this->logger->error(sprintf('[%s] Error storing user', __METHOD__), [
                'wallet_store_dto' => $walletStoreDto->toArray(),
                'message' => $th->getMessage(),
                'exception' => $th,
            ]);

            throw $th;
        }
    }
}
