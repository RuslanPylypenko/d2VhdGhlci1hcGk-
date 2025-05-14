<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\Email;

class SubscriptionCreated
{
    public function __construct(
        private Email $email,
    ) {
    }

    public function getEmail(): Email
    {
        return $this->email;
    }
}
