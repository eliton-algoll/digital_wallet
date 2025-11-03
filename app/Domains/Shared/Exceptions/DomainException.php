<?php

namespace App\Domains\Shared\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use RuntimeException;
use Throwable;
class DomainException extends RuntimeException implements HttpExceptionInterface
{
    protected int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;
    protected array $headers = [];

    public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null, array $headers = [])
    {
        parent::__construct($message, $code, $previous);
        $this->headers = $headers;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
