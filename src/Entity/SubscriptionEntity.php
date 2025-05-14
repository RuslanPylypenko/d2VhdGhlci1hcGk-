<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Frequency;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'subscription')]
class SubscriptionEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Embedded(class: Email::class, columnPrefix: false)]
    private Email $email;

    #[ORM\Column(length: 255)]
    private string $city;

    #[ORM\Column(type: 'string', length: 10, enumType: Frequency::class)]
    private Frequency $frequency;

    #[ORM\Column()]
    private bool $confirmed = false;

    public function __construct(
        Email $email,
        string $city,
        Frequency $frequency
    ) {
        $this->email = $email;
        $this->city = $city;
        $this->frequency = $frequency;
        $this->confirmed = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getFrequency(): Frequency
    {
        return $this->frequency;
    }

    public function setFrequency(Frequency $frequency): void
    {
        $this->frequency = $frequency;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function confirm(): void
    {
        $this->confirmed = true;
    }
}
