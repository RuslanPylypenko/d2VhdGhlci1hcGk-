<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Frequency;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
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

    #[ORM\Embedded(class: ConfirmToken::class, columnPrefix: 'confirm_token_')]
    private ?ConfirmToken $confirmToken;

    #[ORM\Embedded(class: UnsubscribeToken::class, columnPrefix: 'unsubscribe_token_')]
    private ?UnsubscribeToken $unsubscribeToken;

    #[ORM\Column]
    private bool $confirmed;

    #[ORM\Column]
    private bool $subscribed;

    public function __construct(
        Email $email,
        string $city,
        Frequency $frequency,
        ConfirmToken $confirmToken,
        UnsubscribeToken $unsubscribeToken,
    ) {
        $this->email = $email;
        $this->city = $city;
        $this->frequency = $frequency;

        $this->confirmed = false;
        $this->subscribed = false;
        $this->confirmToken = $confirmToken;
        $this->unsubscribeToken = $unsubscribeToken;
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

    public function confirm(string $token, ?\DateTimeImmutable $time = null): void
    {
        $time = $time ?? new \DateTimeImmutable();

        $this->confirmToken->validate($token, $time);

        $this->confirmed = true;
        $this->subscribed = true;
        $this->confirmToken = null;
    }

    public function isUnsubscribed(): bool
    {
        return $this->subscribed;
    }

    public function unsubscribe(): void
    {
        $this->unsubscribeToken = null;
        $this->subscribed = false;
    }

    public function renew(): void
    {
        $this->unsubscribeToken = UnsubscribeToken::next();
        $this->subscribed = true;
    }

    public function getConfirmToken(): ?ConfirmToken
    {
        return $this->confirmToken;
    }

    public function getUnsubscribeToken(): ?UnsubscribeToken
    {
        return $this->unsubscribeToken;
    }
}
