<?php

namespace App\Domains\User\Services;

use App\Domains\User\DTOs\LoginDTO;
use App\Domains\User\Repositories\IUserRepository;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;
use RuntimeException;
use Psr\Log\LoggerInterface;
use Throwable;
use Illuminate\Support\Facades\Hash;

readonly class AuthService
{
    public function __construct(
        private readonly IUserRepository $userRepository,
        private readonly LoggerInterface $logger
    ) {

    }

    /**
     * @throws ValidationException
     */
    public function login(LoginDTO $loginDTO): NewAccessToken {
        try {
            $user = $this->userRepository->findByEmail($loginDTO->email);

            if (!Hash::check($loginDTO->password, $user->password)) {
                throw ValidationException::withMessages(['email' => ['Invalid credentials.']]);
            }

            $user->tokens()->delete();

            $token = $user->createToken('auth_token');
            $accessToken = $token->accessToken;

            $accessToken->expires_at = Carbon::now()->addHours(2);
            $accessToken->save();

            return $token;
        } catch (ValidationException $th) {
            throw $th;
        } catch (Throwable $th) {
            $this->logger->error(sprintf('[%s] Error logging in user', __METHOD__), [
                'login_dto' => $loginDTO->toArray(),
                'message' => $th->getMessage(),
                'exception' => $th,
            ]);

            throw new RuntimeException('Unexpected error when authenticate user');
        }
    }
}
