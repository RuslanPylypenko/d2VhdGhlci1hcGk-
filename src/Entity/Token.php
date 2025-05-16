<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping;
use Symfony\Component\Uid\Uuid;
use Webmozart\Assert\Assert;

#[Mapping\Embeddable]
class Token
{
    private const int TOKEN_LENGTH = 22;

    #[Mapping\Column(name: 'token', length: 22, unique: true, nullable: true)]
    private string $token;

    private function __construct(string $token)
    {
        $pattern = sprintf('/^[1-9A-HJ-NP-Za-km-z]{%d}$/', self::TOKEN_LENGTH);
        Assert::regex($token, $pattern, 'Invalid token');

        $this->token = $token;
    }

    public static function next(): self
    {
        return new self(Uuid::v4()->toBase58());
    }

    public static function fromString(string $token): self
    {
        return new self($token);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isEqual(Token $token): bool
    {
        return $this->token === $token->token;
    }
}
