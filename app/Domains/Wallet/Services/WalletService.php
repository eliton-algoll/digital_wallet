<?php

namespace App\Domains\Wallet\Services;

use App\Domains\Wallet\DTOs\UpdateBalanceDTO;
use App\Domains\Wallet\DTOs\WalletStoreDTO;
use App\Domains\Wallet\Enums\TransactionType;
use App\Domains\Wallet\Models\Wallet;
use App\Domains\Wallet\Repositories\IWalletRepository;
use Psr\Log\LoggerInterface;
use Throwable;
use Illuminate\Validation\ValidationException;

readonly class WalletService
{
    public function __construct(
        private IWalletRepository $walletRepository,
        private LoggerInterface   $logger
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

    public function updateBalance(UpdateBalanceDTO $updateBalanceDTO): void
    {
        $this->walletRepository->updateBalance($updateBalanceDTO);
    }

    /**
     * @throws ValidationException
     */
    public function checkDailyTransactionLimitByType(Wallet $wallet, float $amount, TransactionType $type): void
    {
        $totalToday = $this->walletRepository->getDailyTransactionTotalByType($wallet, $type);

        if (($totalToday + $amount) > $wallet->daily_deposit_limit) {
            throw ValidationException::withMessages([
                'amount' => [sprintf('Daily %s limit exceeded.', $type->value)]
            ]);
        }
    }
}
