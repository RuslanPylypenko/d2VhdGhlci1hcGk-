<?php

declare(strict_types=1);

namespace App\Exceptions;

final class TokenNotFountException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Token not found', 404);
    }
}
