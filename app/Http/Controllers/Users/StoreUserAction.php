<?php

namespace App\Http\Controllers\Users;

use App\Domains\User\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Resources\Users\StoreUserResource;
use Illuminate\Http\JsonResponse;
use Throwable;

class StoreUserAction extends Controller
{
    public function __construct(private readonly UserService $userService) {

    }

    /**
     * @throws Throwable
     */
    public function __invoke(StoreUserRequest $request): JsonResponse
    {
            $userStoreDto = $request->getValidatedData();

            $user = $this->userService->store($userStoreDto);

            return response()->json(StoreUserResource::make($user));
    }
}
