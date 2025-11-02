<?php

use App\Http\Controllers\Users\AuthAction;
use App\Http\Controllers\Users\UserStoreAction;
use App\Http\Controllers\Wallet\BalanceAction;
use App\Http\Controllers\Wallet\DepositAction;
use App\Http\Controllers\Wallet\TransferAction;
use App\Http\Controllers\Wallet\WithdrawalAction;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::get('/', function () {
        return response()->json(['api' => config('app.name'), 'env' => config('app.env'), 'time' => now()]);
    });

    Route::post('users', UserStoreAction::class);
    Route::post('login', AuthAction::class);

    Route::middleware(['auth:sanctum', 'auth'])->group(function() {
        Route::prefix('wallet')->group(function() {
            Route::post('deposit', DepositAction::class);
            Route::post('withdrawal', WithdrawalAction::class);
            Route::post('transfer', TransferAction::class);
            Route::get('balance', BalanceAction::class);
        });
    });
});
