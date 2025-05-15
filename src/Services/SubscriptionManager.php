<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\SubscribeCommand;
use App\Entity\ConfirmToken;
use App\Entity\Email;
use App\Entity\SubscriptionEntity;
use App\Entity\UnsubscribeToken;
use App\Enum\Frequency;
use App\Events\SubscriptionCreated;
use App\Exceptions\EmailAlreadySubscribedException;
use App\Exceptions\TokenNotFountException;
use App\Repository\SubscriptionRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class SubscriptionManager
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private MessageBusInterface $messageBus,
        private TokenGenerator $tokenGenerator,
    ) {
    }

    public function subscribe(SubscribeCommand $command): void
    {
        $email = Email::fromString($command->email);
        $subscription = $this->subscriptionRepository->findByEmail($email);

        if ($subscription) {
            if ($subscription->isUnsubscribed()) {
                $subscription->renew();
                $this->subscriptionRepository->save($subscription);

                return;
            }
            throw new EmailAlreadySubscribedException($email);
        }

        $newSubscription = new SubscriptionEntity(
            $email,
            $command->city,
            Frequency::from($command->frequency),
            $this->tokenGenerator->generateConfirmToken(),
            UnsubscribeToken::next()
        );

        $this->subscriptionRepository->save($newSubscription);

        $this->messageBus->dispatch(new SubscriptionCreated($newSubscription->getEmail()));
    }

    public function confirm(string $token): void
    {
        $token = ConfirmToken::fromString($token);

        $subscription = $this->subscriptionRepository->findByConfirmToken($token->getToken());

        if (null === $subscription) {
            throw new TokenNotFountException();
        }

        $subscription->confirm($token->getToken());

        $this->subscriptionRepository->save($subscription);
    }

    public function unsubscribe(string $token): void
    {
        $token = UnsubscribeToken::fromString($token);

        $subscription = $this->subscriptionRepository->findByUnsubscribeToken($token->getToken());

        if (null === $subscription) {
            throw new TokenNotFountException();
        }

        $subscription->unsubscribe();

        $this->subscriptionRepository->save($subscription);
    }
}
