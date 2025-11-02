<?php

namespace App\Http\Controllers\Users;

use App\Domains\User\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UserStoreRequest;
use App\Http\Resources\Users\UserStoreResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserStoreAction extends Controller
{
    public function __construct(private readonly UserService $userService) {

    }

    /**
     * @throws \Throwable
     */
    public function __invoke(UserStoreRequest $request): JsonResponse
    {
            $userStoreDto = $request->getValidatedData();

            $user = $this->userService->store($userStoreDto);

            return response()->json(UserStoreResource::make($user), Response::HTTP_OK);
    }
}
