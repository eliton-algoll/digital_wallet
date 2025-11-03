<?php

use App\Http\Middleware\AuthenticateApi;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use App\Domains\Shared\Exceptions\DomainException;

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
                return match (true) {
                    $e instanceof ValidationException => response()->json([
                        'message' => $e->getMessage(),
                        'errors'  => $e->errors(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY),

                    $e instanceof DomainException => response()->json([
                        'message' => $e->getMessage(),
                    ], $e->getStatusCode(), $e->getHeaders()),

                    $e instanceof ModelNotFoundException => response()->json([
                        'message' => 'Resource not found.',
                    ], Response::HTTP_NOT_FOUND),

                    $e instanceof AuthenticationException => response()->json([
                        'message' => 'Unauthenticated.',
                    ], Response::HTTP_UNAUTHORIZED),

                    $e instanceof AuthorizationException => response()->json([
                        'message' => 'Forbidden.',
                    ], Response::HTTP_FORBIDDEN),

                    $e instanceof HttpExceptionInterface => response()->json([
                        'message' => $e->getMessage(),
                    ], $e->getStatusCode(), method_exists($e, 'getHeaders') ? $e->getHeaders() : []),

                    default => response()->json([
                        'message' => config('app.debug') ? $e->getMessage() : 'Internal Server Error',
                    ], Response::HTTP_INTERNAL_SERVER_ERROR),
                };
            }

            return null;
        });
    })->create();
