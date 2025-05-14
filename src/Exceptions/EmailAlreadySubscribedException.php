<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Entity\Email;

final class EmailAlreadySubscribedException extends \DomainException
{
    public function __construct(Email $email)
    {
        parent::__construct(sprintf('Email "%s" is already subscribed.', $email->getValue()), 409);
    }
}
