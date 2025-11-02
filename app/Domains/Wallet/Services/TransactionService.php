<?php

namespace App\Domains\Wallet\Services;

use App\Domains\User\Models\User;
use App\Domains\User\Services\UserService;
use App\Domains\Wallet\DTOs\TransactionStoreDTO;
use App\Domains\Wallet\DTOs\TransferDTO;
use App\Domains\Wallet\DTOs\UpdateBalanceDTO;
use App\Domains\Wallet\Enums\TransactionType;
use App\Domains\Wallet\Enums\WalletBalanceAction;
use App\Domains\Wallet\Models\Transaction;
use App\Domains\Wallet\Repositories\ITransactionRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Psr\Log\LoggerInterface;
use RuntimeException;
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

    public function deposit(TransactionStoreDTO $transactionStoreDTO): Transaction {
        try {
            DB::beginTransaction();

            $transaction = $this->createTransaction($transactionStoreDTO);

            DB::commit();

            return $transaction;
        } catch (ValidationException $e) {
            $this->logger->debug(sprintf('[%s] Daily deposit limit reached.', __METHOD__), [
                'wallet_store_dto' => $transactionStoreDTO->toArray(),
                'message' => $e->getMessage(),
            ]);

            DB::rollBack();

            throw $e;
        } catch (Throwable $th) {
            $this->logger->error(sprintf('[%s] Error storing user', __METHOD__), [
                'wallet_store_dto' => $transactionStoreDTO->toArray(),
                'message' => $th->getMessage(),
                'exception' => $th,
            ]);

            DB::rollBack();

            throw new RuntimeException('Unexpected error while storing deposit' );
        }
    }

    /**
     * @throws ValidationException
     */
    private function createTransaction(TransactionStoreDTO $transactionStoreDTO): transaction {
        $wallet = $transactionStoreDTO->user->wallet;

        $this->walletService->checkDailyTransactionLimitByType($wallet, $transactionStoreDTO->amount, $transactionStoreDTO->type);

        if ($transactionStoreDTO->type === TransactionType::WITHDRAWAL || $transactionStoreDTO->type === TransactionType::TRANSFER_OUT) {
            if ($wallet->balance < $transactionStoreDTO->amount) {
                throw ValidationException::withMessages(['amount' => ['Insufficient balance.']]);
            }
        }

        $transaction = $this->transactionRepository->store($transactionStoreDTO);

        $updateBalanceType = in_array($transactionStoreDTO->type->value, [TransactionType::DEPOSIT->value, TransactionType::TRANSFER_IN->value]) ? WalletBalanceAction::CREDIT : WalletBalanceAction::DEBIT;

        $updateBalanceDTO = new UpdateBalanceDTO(
            wallet: $wallet,
            balanceAction: $updateBalanceType,
            amount: $transactionStoreDTO->amount,
        );

        $this->walletService->updateBalance($updateBalanceDTO);

        return $transaction;
    }

    public function withdrawal(TransactionStoreDTO $transactionStoreDTO): Transaction {
        try {
            DB::beginTransaction();

            $transaction = $this->createTransaction($transactionStoreDTO);

            DB::commit();

            return $transaction;
        } catch (ValidationException $e) {
            $this->logger->debug(sprintf('[%s] Daily withdrawal limit reached.', __METHOD__), [
                'wallet_store_dto' => $transactionStoreDTO->toArray(),
                'message' => $e->getMessage(),
            ]);

            DB::rollBack();

            throw $e;
        } catch (Throwable $th) {
            $this->logger->error(sprintf('[%s] Error storing user', __METHOD__), [
                'wallet_store_dto' => $transactionStoreDTO->toArray(),
                'message' => $th->getMessage(),
                'exception' => $th,
            ]);

            DB::rollBack();

            throw new RuntimeException('Unexpected error while storing deposit' );
        }
    }

    public function transfer(TransferDTO $transferDTO): Transaction {
        try {
            DB::beginTransaction();

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
                transferredWalletId:  $transferDTO->user->wallet->id
            );

            $this->createTransaction($transferInTransactionStoreDTO);

            DB::commit();

            return $transferOutTransaction;
        } catch (ValidationException $e) {
            $this->logger->debug(sprintf('[%s] insufficient balance.', __METHOD__), [
                'transfer_dto' => $transferDTO->toArray(),
                'message' => $e->getMessage(),
            ]);

            DB::rollBack();

            throw $e;
        } catch (Throwable $th) {
            $this->logger->error(sprintf('[%s] Error while storing transfer', __METHOD__), [
                'transfer_dto' => $transferDTO->toArray(),
                'message' => $th->getMessage(),
                'exception' => $th,
            ]);

            DB::rollBack();

            throw new RuntimeException('Unexpected error while storing transfer' );
        }
    }

    public function list(User $user, array $filters, array $sortBy, int $perPage): LengthAwarePaginator
    {
        return $this->transactionRepository->list($user->wallet->id, $filters, $sortBy, $perPage);
    }
}
