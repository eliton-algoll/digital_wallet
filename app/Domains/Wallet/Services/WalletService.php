<?php

namespace App\Domains\Wallet\Services;

use App\Domains\User\Models\User;
use App\Domains\Wallet\DTOs\UpdateBalanceDTO;
use App\Domains\Wallet\DTOs\WalletStoreDTO;
use App\Domains\Wallet\Enums\TransactionType;
use App\Domains\Wallet\Exceptions\DailyLimitExceedException;
use App\Domains\Wallet\Models\Wallet;
use App\Domains\Wallet\Repositories\IWalletRepository;
use Psr\Log\LoggerInterface;
use Throwable;

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

    public function checkDailyTransactionLimitByType(Wallet $wallet, float $amount, TransactionType $type): void
    {
        if (!in_array($type->value, [TransactionType::DEPOSIT->value, TransactionType::WITHDRAWAL->value])) {
            return;
        }

        $totalToday = $this->walletRepository->getDailyTransactionTotalByType($wallet, $type);

        if (($totalToday + $amount) > $wallet->daily_deposit_limit) {
            throw new DailyLimitExceedException(sprintf('Daily %s limit exceeded.', $type->value));
        }
    }

    public function getWallet(User $user): wallet
    {
        return $user->wallet;
    }
}
