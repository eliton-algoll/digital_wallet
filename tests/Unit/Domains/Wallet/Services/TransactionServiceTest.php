<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Wallet\Services;

use App\Domains\User\Models\User;
use App\Domains\User\Services\UserService;
use App\Domains\Wallet\DTOs\TransactionStoreDTO;
use App\Domains\Wallet\DTOs\TransferDTO;
use App\Domains\Wallet\DTOs\UpdateBalanceDTO;
use App\Domains\Wallet\Enums\TransactionType;
use App\Domains\Wallet\Exceptions\DailyLimitExceedException;
use App\Domains\Wallet\Exceptions\InsufficientBalanceException;
use App\Domains\Wallet\Models\Transaction;
use App\Domains\Wallet\Models\Wallet;
use App\Domains\Wallet\Repositories\ITransactionRepository;
use App\Domains\Wallet\Services\TransactionService;
use App\Domains\Wallet\Services\WalletService;
use App\Events\TransactionCompletedEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;
use Psr\Log\LoggerInterface;

class TransactionServiceTest extends TestCase
{
    private ITransactionRepository|MockInterface $transactionRepository;
    private WalletService|MockInterface $walletService;
    private UserService|MockInterface $userService;
    private TransactionService $transactionService;
    private LoggerInterface|MockInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionRepository = Mockery::mock(ITransactionRepository::class);
        $this->walletService = Mockery::mock(WalletService::class);
        $this->userService = Mockery::mock(UserService::class);
        $this->logger = Mockery::mock(LoggerInterface::class);

        $this->transactionService = new TransactionService(
            $this->transactionRepository,
            $this->walletService,
            $this->userService,
            $this->logger
        );
    }

    public function test_should_be_creates_a_deposit_transaction_successfully()
    {
        $user = new User();
        $user->wallet = new Wallet(['id' => 1, 'balance' => 100]);

        $dto = new TransactionStoreDTO($user, 50, TransactionType::DEPOSIT);

        $transaction = new Transaction([
            'id' => 1,
            'uuid' => "fake-uuid-string",
            'wallet_id' => 1,
            'amount' => 50,
        ]);

        DB::shouldReceive('transaction')
            ->andReturnUsing(fn($cb) => $cb());

        $this->walletService
            ->shouldReceive('checkDailyTransactionLimitByType')
            ->once();

        $this->transactionRepository
            ->shouldReceive('store')
            ->once()
            ->andReturn($transaction);

        $this->walletService->shouldReceive('updateBalance')
            ->once()
            ->with(Mockery::type(UpdateBalanceDTO::class));

        Event::fake([TransactionCompletedEvent::class]);

        $result = $this->transactionService->deposit($dto);

        Event::assertDispatched(TransactionCompletedEvent::class);
        $this->assertSame($transaction, $result);
    }

    public function test_should_throw_exception_when_deposit_daily_limit_exceeded()
    {
        $user = new User();
        $user->wallet = new Wallet(['id' => 1, 'balance' => 100]);

        $dto = new TransactionStoreDTO($user, 100000, TransactionType::DEPOSIT);

        DB::shouldReceive('transaction')
            ->andReturnUsing(fn($cb) => $cb());

        $this->logger->shouldReceive('error')
            ->once();

        $this->walletService
            ->shouldReceive('checkDailyTransactionLimitByType')
            ->once()
            ->andThrow(new DailyLimitExceedException('Daily limit exceeded'));

        $this->expectException(DailyLimitExceedException::class);
        $this->expectExceptionMessage('Daily limit exceeded');

        $this->transactionService->deposit($dto);
    }

    public function test_should_throw_exception_if_transaction_store_fails()
    {
        $user = new User();
        $user->wallet = new Wallet(['id' => 1, 'balance' => 100]);

        $dto = new TransactionStoreDTO($user, 50, TransactionType::DEPOSIT);

        DB::shouldReceive('transaction')
            ->andReturnUsing(fn($cb) => $cb());

        $this->walletService
            ->shouldReceive('checkDailyTransactionLimitByType')
            ->once();

        $this->transactionRepository
            ->shouldReceive('store')
            ->once()
            ->andThrow(new RuntimeException('DB error'));

        $this->logger->shouldReceive('error')
            ->once();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('DB error');

        $this->transactionService->deposit($dto);
    }

    public function test_should_create_a_withdrawal_transaction_successfully()
    {
        $user = new User();
        $user->wallet = new Wallet(['id' => 1, 'balance' => 200]);

        $dto = new TransactionStoreDTO($user, 50, TransactionType::WITHDRAWAL);

        $transaction = new Transaction([
            'id' => 1,
            'uuid' => 'fake-uuid-string',
            'wallet_id' => 1,
            'amount' => 50,
        ]);

        DB::shouldReceive('transaction')
            ->andReturnUsing(fn($cb) => $cb());

        $this->walletService
            ->shouldReceive('checkDailyTransactionLimitByType')
            ->once();

        $this->transactionRepository
            ->shouldReceive('store')
            ->once()
            ->andReturn($transaction);

        $this->walletService
            ->shouldReceive('updateBalance')
            ->once();

        Event::fake([TransactionCompletedEvent::class]);

        $result = $this->transactionService->withdrawal($dto);

        Event::assertDispatched(TransactionCompletedEvent::class);

        $this->assertSame($transaction, $result);
    }

    public function test_should_throw_exception_when_balance_is_insufficient()
    {
        $user = new User();
        $user->wallet = new Wallet(['id' => 1, 'balance' => 30]);

        $dto = new TransactionStoreDTO($user, 50, TransactionType::WITHDRAWAL);

        DB::shouldReceive('transaction')
            ->andReturnUsing(fn($cb) => $cb());

        $this->walletService
            ->shouldReceive('checkDailyTransactionLimitByType')
            ->once();

        $this->expectException(InsufficientBalanceException::class);
        $this->expectExceptionMessage('Insufficient balance.');

        $this->logger->shouldReceive('error')
            ->once();

        $this->transactionService->withdrawal($dto);
    }

    public function test_should_throw_exception_when_withdrawal_daily_limit_exceeded()
    {
        $user = new User();
        $user->wallet = new Wallet(['id' => 1, 'balance' => 200]);

        $dto = new TransactionStoreDTO($user, 100, TransactionType::WITHDRAWAL);

        DB::shouldReceive('transaction')
            ->andReturnUsing(fn($cb) => $cb());

        $this->walletService
            ->shouldReceive('checkDailyTransactionLimitByType')
            ->once()
            ->andThrow(new DailyLimitExceedException('Daily limit exceeded'));

        $this->logger->shouldReceive('error')
            ->once();

        $this->expectException(DailyLimitExceedException::class);
        $this->expectExceptionMessage('Daily limit exceeded');

        $this->transactionService->withdrawal($dto);
    }

    public function test_should_create_a_transfer_transaction_successfully()
    {
        $sender = new User();
        $sender->wallet = new Wallet(['id' => 1, 'balance' => 500]);

        $receiver = new User();
        $receiver->wallet = new Wallet(['id' => 2, 'balance' => 200]);

        $transferDto = new TransferDTO(
            $sender,
            100,
            'teste@teste.com'
        );

        $transaction = new Transaction([
            'id' => 1,
            'uuid' => 'fake-uuid-transfer',
            'wallet_id' => 1,
            'amount' => 100,
            'transferred_wallet_id' => $receiver->wallet->id,
        ]);

        DB::shouldReceive('transaction')
            ->andReturnUsing(fn($cb) => $cb());

        $this->walletService
            ->shouldReceive('checkDailyTransactionLimitByType')
            ->twice();

        $this->userService
            ->shouldReceive('getByEmail')
            ->andReturn($sender, $receiver);

        $this->transactionRepository
            ->shouldReceive('store')
            ->andReturn($transaction);

        $this->walletService
            ->shouldReceive('updateBalance')
            ->twice()
            ->with(Mockery::type(UpdateBalanceDTO::class));

        Event::shouldReceive('dispatch')
            ->twice()
            ->with(Mockery::type(TransactionCompletedEvent::class));

        $result = $this->transactionService->transfer($transferDto);

        $this->assertSame($transaction, $result);
    }
}
