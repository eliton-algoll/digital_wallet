<?php

use App\Http\Controllers\Users\AuthAction;
use App\Http\Controllers\Users\UserStoreAction;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])->group(function () {
    Route::get('/', function () {
        return response()->json(['api' => config('app.name'), 'env' => config('app.env'), 'time' => now()]);
    });

    Route::post('users', UserStoreAction::class);
    Route::post('login', AuthAction::class);
});

