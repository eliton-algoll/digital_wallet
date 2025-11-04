<?php

namespace App\Domains\User\Services;

use App\Domains\User\DTOs\StoreUserDTO;
use App\Domains\User\Models\User;
use App\Domains\User\Repositories\IUserRepository;
use App\Domains\Wallet\DTOs\WalletStoreDTO;
use App\Domains\Wallet\Services\WalletService;
use RuntimeException;
use Illuminate\Support\Facades\DB;
use Psr\Log\LoggerInterface;
use Throwable;

class UserService
{
    public function __construct(
        private readonly IUserRepository $userRepository,
        private readonly WalletService $walletService,
        private readonly LoggerInterface $logger
    )
    { }

    public function store(StoreUserDTO $userStoreDto): User
    {
        return DB::transaction(function () use ($userStoreDto) {
            try {
                $user = $this->userRepository->store($userStoreDto);

                $walletStoreDto = new WalletStoreDTO($user);
                $this->walletService->store($walletStoreDto);

                return $user;
            } catch (Throwable $th) {
                $this->logger->error(sprintf('[%s] Error storing user', __METHOD__), [
                    'userStoreDto' => $userStoreDto->toArray(),
                    'error' => $th->getMessage(),
                    'exception' => $th,
                ]);

                throw new RuntimeException('Unexpected error storing user');
            }
        });
    }

    public function getByEmail(string $email): User
    {
        return $this->userRepository->findByEmail($email);
    }
}
