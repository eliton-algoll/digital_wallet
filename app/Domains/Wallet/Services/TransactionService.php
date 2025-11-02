<?php

namespace App\Domains\Wallet\Services;

use App\Domains\Wallet\DTOs\TransactionStoreDTO;
use App\Domains\Wallet\DTOs\UpdateBalanceDTO;
use App\Domains\Wallet\Enums\WalletBalanceAction;
use App\Domains\Wallet\Models\Transaction;
use App\Domains\Wallet\Repositories\ITransactionRepository;
use Illuminate\Support\Facades\DB;
use Nette\Schema\ValidationException;
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

    public function deposit(TransactionStoreDTO $depositDTO): Transaction {
        try {
            DB::beginTransaction();

            $wallet = $depositDTO->user->wallet;

            $this->walletService->checkDailyDepositLimit($wallet, $depositDTO->amount);

            $transaction = $this->transactionRepository->store($depositDTO);

            $updateBalanceDTO = new UpdateBalanceDTO(
                wallet: $wallet,
                balanceAction: WalletBalanceAction::CREDIT,
                amount: $depositDTO->amount,
            );

            $this->walletService->updateBalance($updateBalanceDTO);

            DB::commit();

            return $transaction;
        } catch (ValidationException $e) {
            $this->logger->debug(sprintf('[%s] Daily deposit limit reached.', __METHOD__), [
                'wallet_store_dto' => $depositDTO->toArray(),
                'message' => $e->getMessage(),
            ]);

            DB::rollBack();

            throw $e;
        } catch (Throwable $th) {
            $this->logger->error(sprintf('[%s] Error storing user', __METHOD__), [
                'wallet_store_dto' => $depositDTO->toArray(),
                'message' => $th->getMessage(),
                'exception' => $th,
            ]);

            DB::rollBack();

            throw new RuntimeException('Unexpected error while storing deposit' );
        }
    }
}
