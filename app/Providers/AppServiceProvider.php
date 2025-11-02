<?php

namespace App\Providers;

use App\Domains\User\Repositories\IUserRepository;
use App\Domains\Wallet\Repositories\IWalletRepository;
use App\Infrastructure\Repositories\Users\UserRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
