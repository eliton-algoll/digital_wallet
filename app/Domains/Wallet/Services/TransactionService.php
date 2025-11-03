<?php

namespace App\Domains\Wallet\Services;

use App\Domains\User\Models\User;
use App\Domains\User\Services\UserService;
use App\Domains\Wallet\DTOs\TransactionStoreDTO;
use App\Domains\Wallet\DTOs\TransferDTO;
use App\Domains\Wallet\DTOs\UpdateBalanceDTO;
use App\Domains\Wallet\Enums\TransactionType;
use App\Domains\Wallet\Enums\WalletBalanceAction;
use App\Domains\Wallet\Exceptions\InsufficientBalanceException;
use App\Domains\Wallet\Models\Transaction;
use App\Domains\Wallet\Repositories\ITransactionRepository;
use App\Events\TransactionCompletedEvent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class TransactionService
{
    public function __construct(
        private readonly ITransactionRepository $transactionRepository,
        private readonly WalletService $walletService,
        private readonly UserService $userService,
        private readonly LoggerInterface $logger
    ) {

    }

    public function deposit(TransactionStoreDTO $transactionStoreDTO): Transaction
    {
        return DB::transaction(function () use ($transactionStoreDTO) {
            try {
                $transaction = $this->createTransaction($transactionStoreDTO);

                event(new TransactionCompletedEvent($transaction));

                return $transaction;
            } catch (Throwable $th) {
                $this->logger->error(sprintf('[%s] Error storing deposit', __METHOD__), [
                    'transaction_store_dto' => $transactionStoreDTO->toArray(),
                    'message' => $th->getMessage(),
                    'exception' => $th,
                ]);

                throw $th;
            }
        });
    }

    private function createTransaction(TransactionStoreDTO $transactionStoreDTO): transaction {
        $wallet = $transactionStoreDTO->user->wallet;

        $this->walletService->checkDailyTransactionLimitByType($wallet, $transactionStoreDTO->amount, $transactionStoreDTO->type);

        if ($transactionStoreDTO->type === TransactionType::WITHDRAWAL || $transactionStoreDTO->type === TransactionType::TRANSFER_OUT) {
            if ($wallet->balance < $transactionStoreDTO->amount) {
                throw new InsufficientBalanceException();
            }
        }

        $transaction = $this->transactionRepository->store($transactionStoreDTO);

        $updateBalanceType = match ($transactionStoreDTO->type) {
            TransactionType::DEPOSIT, TransactionType::TRANSFER_IN => WalletBalanceAction::CREDIT,
            TransactionType::WITHDRAWAL, TransactionType::TRANSFER_OUT => WalletBalanceAction::DEBIT,
        };

        $updateBalanceDTO = new UpdateBalanceDTO(
            wallet: $wallet,
            balanceAction: $updateBalanceType,
            amount: $transactionStoreDTO->amount,
        );

        $this->walletService->updateBalance($updateBalanceDTO);

        return $transaction;
    }

    public function withdrawal(TransactionStoreDTO $transactionStoreDTO): Transaction
    {
        return DB::transaction(function () use ($transactionStoreDTO) {
            try {
                $transaction = $this->createTransaction($transactionStoreDTO);

                event(new TransactionCompletedEvent($transaction));

                return $transaction;
            } catch (Throwable $th) {
                $this->logger->error(sprintf('[%s] Error storing withdrawal', __METHOD__), [
                    'transaction_store_dto' => $transactionStoreDTO->toArray(),
                    'message' => $th->getMessage(),
                    'exception' => $th,
                ]);

                throw $th;
            }
        });
    }

    public function transfer(TransferDTO $transferDTO): Transaction
    {
        return DB::transaction(function () use ($transferDTO) {
            try {
                $recipientUser = $this->userService->getByEmail($transferDTO->recipient);

                $transferOutTransactionStoreDTO = new TransactionStoreDTO(
                    user: $transferDTO->user,
                    amount: $transferDTO->amount,
                    type: TransactionType::TRANSFER_OUT,
                    transferredWalletId: $recipientUser->wallet->id
                );

                $transferOutTransaction = $this->createTransaction($transferOutTransactionStoreDTO);

                $transferInTransactionStoreDTO = new TransactionStoreDTO(
                    user: $recipientUser,
                    amount: $transferDTO->amount,
                    type: TransactionType::TRANSFER_IN,
                    transferredWalletId: $transferDTO->user->wallet->id
                );

                $transferInTransaction = $this->createTransaction($transferInTransactionStoreDTO);

                event(new TransactionCompletedEvent($transferOutTransaction));
                event(new TransactionCompletedEvent($transferInTransaction));

                return $transferOutTransaction;
            } catch (Throwable $th) {
                $this->logger->error(sprintf('[%s] Error while storing transfer', __METHOD__), [
                    'transfer_dto' => $transferDTO->toArray(),
                    'message' => $th->getMessage(),
                    'exception' => $th,
                ]);

                throw $th;
            }
        });
    }

    public function list(User $user, array $filters, array $sortBy, int $perPage): LengthAwarePaginator
    {
        return $this->transactionRepository->list($user->wallet->id, $filters, $sortBy, $perPage);
    }
}
