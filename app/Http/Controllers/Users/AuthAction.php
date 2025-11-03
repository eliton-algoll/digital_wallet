<?php

namespace App\Http\Controllers\Users;

use App\Domains\User\Services\AuthService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Resources\Users\PersonalAccessTokenResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthAction extends Controller
{
    public function __construct(private readonly AuthService $authService) {

    }

    /**
     * @throws ValidationException
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $token = $this->authService->login($request->getValidatedData());

        return response()->json(PersonalAccessTokenResource::make($token));
    }
}
