<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\SubscribeCommand;
use App\Entity\Email;
use App\Entity\SubscriptionEntity;
use App\Entity\Token;
use App\Enum\Frequency;
use App\Events\SubscriptionCreated;
use App\Exceptions\EmailAlreadySubscribedException;
use App\Exceptions\TokenNotFountException;
use App\Repository\SubscriptionRepository;
use App\Services\Weather\WeatherService;
use Symfony\Component\Messenger\MessageBusInterface;

class SubscriptionManager
{
    public function __construct(
        private WeatherService $weatherService,
        private SubscriptionRepository $subscriptionRepository,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function subscribe(SubscribeCommand $command): void
    {
        $city = $this->weatherService->getCity($command->city);
        $email = Email::fromString($command->email);
        $subscription = $this->subscriptionRepository->findByEmail($email);

        if ($subscription) {
            throw new EmailAlreadySubscribedException($email);
        }

        $newSubscription = new SubscriptionEntity(
            $email,
            $city,
            Frequency::from($command->frequency),
            Token::next(),
            Token::next()
        );

        $this->subscriptionRepository->save($newSubscription);

        $this->messageBus->dispatch(new SubscriptionCreated($newSubscription->getEmail()));
    }

    public function confirm(string $token): void
    {
        $token = Token::fromString($token);

        $subscription = $this->subscriptionRepository->findByConfirmToken($token);

        if (null === $subscription) {
            throw new TokenNotFountException();
        }

        $subscription->confirm($token);

        $this->subscriptionRepository->save($subscription);
    }

    public function unsubscribe(string $token): void
    {
        $token = Token::fromString($token);

        $subscription = $this->subscriptionRepository->findByUnsubscribeToken($token->getToken());

        if (null === $subscription) {
            throw new TokenNotFountException();
        }

        $subscription->unsubscribe();

        $this->subscriptionRepository->save($subscription);
    }
}
