<?php

use App\Http\Middleware\AuthenticateApi;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth' => AuthenticateApi::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return response()->json($e->errors(), 422);
                }

                if ($e instanceof HttpException) {
                    return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
                }

                if (method_exists($e, 'getStatusCode')) {
                    return response()->json(['message' => $e->getMessage() ?: 'Error'], $e->getStatusCode());
                }

                if ($e instanceof AuthenticationException) {
                    return response()->json(['message' => 'Unauthenticated.'], 401);
                }

                return response()->json(['message' => $e->getMessage() ?: 'Internal Server Error'], 500);
            }

            return null;
        });
    })->create();
