<?php

namespace App\Http\Controllers\Wallet;

use App\Domains\Wallet\Services\TransactionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\DepositRequest;
use App\Http\Resources\Wallet\TransactionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class DepositAction extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ){
    }

    /**
     * @throws ValidationException
     */
    public function __invoke(DepositRequest $request): JsonResponse
    {
        $depositDto = $request->getValidatedData();

        $transaction = $this->transactionService->deposit($depositDto);

        return response()->json(TransactionResource::make($transaction), Response::HTTP_OK);
    }
}
