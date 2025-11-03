<?php

namespace App\Domains\Wallet\Exceptions;

use App\Domains\Shared\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

class DailyLimitExceedException extends DomainException
{
    protected int $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    public function __construct(string $message = 'Daily limit exceeded.', array $headers = [])
    {
        parent::__construct($message, 0, null, $headers);
    }

}
