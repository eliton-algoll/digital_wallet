<?php

namespace App\Domains\Wallet\Services;

use App\Domains\Wallet\DTOs\TransactionStoreDTO;
use App\Domains\Wallet\DTOs\UpdateBalanceDTO;
use App\Domains\Wallet\Enums\TransactionType;
use App\Domains\Wallet\Enums\WalletBalanceAction;
use App\Domains\Wallet\Models\Transaction;
use App\Domains\Wallet\Repositories\ITransactionRepository;
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
        private readonly LoggerInterface $logger
    ) {

    }

    public function deposit(TransactionStoreDTO $transactionStoreDTO): Transaction {
        try {
            DB::beginTransaction();

            $transaction = $this->createTransaction($transactionStoreDTO, TransactionType::DEPOSIT);

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
    private function createTransaction(TransactionStoreDTO $transactionStoreDTO, TransactionType $transactionType): transaction {
        $wallet = $transactionStoreDTO->user->wallet;

        $this->walletService->checkDailyTransactionLimitByType($wallet, $transactionStoreDTO->amount, $transactionType);

        if ($transactionType === TransactionType::WITHDRAWAL) {
            if ($wallet->balance < $transactionStoreDTO->amount) {
                throw ValidationException::withMessages(['amount' => ['Insufficient balance.']]);
            }
        }

        $transaction = $this->transactionRepository->store($transactionStoreDTO);

        $updateBalanceDTO = new UpdateBalanceDTO(
            wallet: $wallet,
            balanceAction: WalletBalanceAction::DEBIT,
            amount: $transactionStoreDTO->amount,
        );

        $this->walletService->updateBalance($updateBalanceDTO);

        return $transaction;
    }

    public function withdrawal(TransactionStoreDTO $transactionStoreDTO): Transaction {
        try {
            DB::beginTransaction();

            $transaction = $this->createTransaction($transactionStoreDTO, TransactionType::WITHDRAWAL);

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
}
