<?php

namespace App\Domains\Wallet\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

class InsufficientBalanceException extends DomainException
{
    protected int $statusCode = Response::HTTP_CONFLICT;

    public function __construct(string $message = 'Insufficient balance.', array $headers = [])
    {
        parent::__construct($message, 0, null, $headers);
    }
}
