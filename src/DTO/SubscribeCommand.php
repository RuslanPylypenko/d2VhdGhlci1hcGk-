<?php

declare(strict_types=1);

namespace App\DTO;

use App\Enum\Frequency;
use Symfony\Component\Validator\Constraints as Assert;

final class SubscribeCommand
{
    #[Assert\NotBlank(message: 'Email must not be blank.')]
    #[Assert\Email(message: '“{{ value }}” is not a valid email.')]
    public ?string $email;

    #[Assert\NotBlank(message: 'City must not be blank.')]
    public ?string $city;

    #[Assert\NotBlank(message: 'Frequency must not be blank.')]
    #[Assert\Choice(
        choices: Frequency::VALUES,
        message: 'Frequency must be either "hourly" or "daily".'
    )]
    public ?string $frequency;

    public function __construct(
        ?string $email,
        ?string $city,
        ?string $frequency,
    ) {
        $this->email = $email;
        $this->city = $city;
        $this->frequency = $frequency;
    }

    /**
     * @param array<string> $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self(
            $payload['email'] ?? null,
            $payload['city'] ?? null,
            $payload['frequency'] ?? null
        );
    }
}
