<?php

namespace App\Http\Controllers\Wallet;

use App\Domains\Wallet\Services\WalletService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Wallet\WalletResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BalanceAction extends Controller
{
    public function __construct(
        private readonly WalletService $walletService,
    ){
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $wallet = $this->walletService->getWallet($user);

        return response()->json(WalletResource::make($wallet), Response::HTTP_OK);
    }
}
