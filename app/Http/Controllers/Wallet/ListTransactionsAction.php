<?php

namespace App\Http\Controllers\Wallet;

use App\Domains\Wallet\Services\TransactionService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Wallet\ListTransactionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListTransactionsAction extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ){
    }

    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $perPage = $request->get('per_page', 20);

        $filters = $request->all();
        $sortBy = [
            'sort_by' =>  $request->input('sort_by'),
            'direction' =>  $request->input('direction', 'desc'),
        ];

        $transactions = $this->transactionService->list($user, $filters, $sortBy, $perPage);

        return response()->json([
            'data' => ListTransactionResource::collection($transactions),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'last_page' => $transactions->lastPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
            ]
        ]
        );
    }
}
