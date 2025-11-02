<?php

namespace App\Http\Controllers\Wallet;

use App\Domains\Wallet\Services\TransactionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Wallet\TransferRequest;
use App\Http\Resources\Wallet\TransferResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class TransferAction extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ){
    }

    /**
     * @throws ValidationException
     */
    public function __invoke(TransferRequest $request): JsonResponse
    {
        $depositDto = $request->getValidatedData();

        $transaction = $this->transactionService->transfer($depositDto);

        return response()->json(TransferResource::make($transaction), Response::HTTP_OK);
    }
}
