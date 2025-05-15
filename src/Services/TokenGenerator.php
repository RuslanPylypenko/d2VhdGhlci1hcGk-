<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\ConfirmToken;
use Symfony\Component\Uid\Uuid;

class TokenGenerator
{
    public function __construct(
        private \DateInterval $tokenTTL,
    ) {
    }

    public function generateConfirmToken(?\DateTimeImmutable $time = null): ConfirmToken
    {
        $token = Uuid::v4()->toBase58();
        $time = $time ?? new \DateTimeImmutable();

        return new ConfirmToken($token, $time->add($this->tokenTTL));
    }
}
