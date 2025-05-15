<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

#[Mapping\Embeddable]
class ConfirmToken
{
    private const int TOKEN_LENGTH = 22;

    #[Mapping\Column(length: self::TOKEN_LENGTH, nullable: true)]
    private ?string $token = null;

    #[Mapping\Column(nullable: true)]
    private ?\DateTimeImmutable $expiredAt = null;

    public function __construct(string $token, \DateTimeImmutable $expiredAt)
    {
        Assert::notEmpty($token);
        Assert::length($token, self::TOKEN_LENGTH);

        $this->token = $token;
        $this->expiredAt = $expiredAt;
    }

    public static function next(\DateTimeImmutable $expiredAt): self
    {
        return new self(Uuid::v4()->toBase58(), $expiredAt);
    }

    public function validate(string $token, \DateTimeImmutable $date): void
    {
        if (!$this->isEqualTo($token)) {
            throw new \DomainException('Confirm token is invalid.');
        }
        if ($this->isExpiredTo($date)) {
            throw new \DomainException('Confirm token is expired.');
        }
    }

    private function isEqualTo(string $token): bool
    {
        return $this->token === $token;
    }

    private function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expiredAt <= $date;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }
}
