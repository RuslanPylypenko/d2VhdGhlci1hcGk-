<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\Frequency;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ORM\Table(name: 'subscription')]
#[ORM\Index(name: 'city', columns: ['city'])]
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

    #[ORM\Embedded(class: Token::class, columnPrefix: 'confirm_')]
    private ?Token $confirmToken;

    #[ORM\Embedded(class: Token::class, columnPrefix: 'unsubscribe_')]
    private ?Token $unsubscribeToken;

    #[ORM\Column]
    private bool $confirmed;

    #[ORM\Column]
    private bool $subscribed;

    public function __construct(
        Email $email,
        string $city,
        Frequency $frequency,
        Token $confirmToken,
        Token $unsubscribeToken,
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

    public function confirm(Token $token): void
    {
        Assert::true($this->confirmToken->isEqual($token), 'Invalid token');

        $this->confirmed = true;
        $this->subscribed = true;
        $this->confirmToken = null;
    }

    public function unsubscribe(): void
    {
        $this->unsubscribeToken = null;
        $this->subscribed = false;
    }

    public function getConfirmToken(): ?Token
    {
        return $this->confirmToken;
    }

    public function getUnsubscribeToken(): ?Token
    {
        return $this->unsubscribeToken;
    }
}
