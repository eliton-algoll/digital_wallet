<?php

namespace App\Providers;

use App\Domains\User\Repositories\IUserRepository;
use App\Domains\User\Repositories\IUserWebhookRepository;
use App\Domains\Wallet\Repositories\ITransactionRepository;
use App\Domains\Wallet\Repositories\IWalletRepository;
use App\Infrastructure\Repositories\Users\UserRepository;
use App\Infrastructure\Repositories\Users\UserWebhookRepository;
use App\Infrastructure\Repositories\Wallet\TransactionRepository;
use App\Infrastructure\Repositories\Wallet\WalletRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
        $this->app->bind(IWalletRepository::class, WalletRepository::class);
        $this->app->bind(ITransactionRepository::class, TransactionRepository::class);
        $this->app->bind(IUserWebhookRepository::class, UserWebhookRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
