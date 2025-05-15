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

    #[Mapping\Column(length: self::TOKEN_LENGTH, unique: true, nullable: true)]
    private ?string $token = null;

    #[Mapping\Column(nullable: true)]
    private ?\DateTimeImmutable $expiredAt = null;

    public function __construct(string $token, \DateTimeImmutable $expiredAt)
    {
        $pattern = sprintf('/^[1-9A-HJ-NP-Za-km-z]{%d}$/', self::TOKEN_LENGTH);
        Assert::regex($token, $pattern, 'Invalid token');

        $this->token = $token;
        $this->expiredAt = $expiredAt;
    }

    public static function fromString(string $token, ?\DateTimeImmutable $expiredAt = null): self
    {
        return new self($token, $expiredAt ?? new \DateTimeImmutable());
    }

    public function validate(string $token, \DateTimeImmutable $date): void
    {
        Assert::uuid(Uuid::fromBase58($token)->toString(), 'Invalid token');

        if (!$this->isEqualTo($token)) {
            throw new \DomainException('Not equal to token');
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
