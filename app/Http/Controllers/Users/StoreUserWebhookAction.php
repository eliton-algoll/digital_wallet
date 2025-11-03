<?php

namespace App\Http\Controllers\Users;

use App\Domains\User\Services\UserWebhookService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserWebhookRequest;
use App\Http\Resources\Users\StoreUserWebhookResource;
use Illuminate\Http\JsonResponse;
use Throwable;

class StoreUserWebhookAction extends Controller
{
    public function __construct(private readonly UserWebhookService $userWebhookService) {

    }

    /**
     * @throws Throwable
     */
    public function __invoke(StoreUserWebhookRequest $request): JsonResponse
    {
            $storeUserWebhookDto = $request->getValidatedData();

            $user = $this->userWebhookService->store($storeUserWebhookDto);

            return response()->json(StoreUserWebhookResource::make($user));
    }
}
