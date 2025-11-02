<?php

namespace App\Http\Controllers\Wallet;

use App\Domains\Wallet\Services\TransactionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\WithdrawalRequest;
use App\Http\Resources\Wallet\TransactionResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WithdrawalAction extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ){
    }

    public function __invoke(WithdrawalRequest $request): JsonResponse
    {
        $depositDto = $request->getValidatedData();

        $transaction = $this->transactionService->withdrawal($depositDto);

        return response()->json(TransactionResource::make($transaction), Response::HTTP_OK);
    }
}
