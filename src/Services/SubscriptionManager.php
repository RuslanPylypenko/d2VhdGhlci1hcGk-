<?php

declare(strict_types=1);

namespace App\Services;

use App\Commands\SubscribeCommand;
use App\Entity\Email;
use App\Entity\SubscriptionEntity;
use App\Enum\Frequency;
use App\Exceptions\EmailAlreadySubscribedException;
use App\Repository\SubscriptionRepository;

class SubscriptionManager
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
    )
    {
    }

    public function subscribe(SubscribeCommand $command): void
    {
        $email = Email::fromString($command->email);
        $subscription = $this->subscriptionRepository->findByEmail($email);

        if (null !== $subscription) {
            throw new EmailAlreadySubscribedException($email);
        }

        $newSubscription = new SubscriptionEntity(
            $email,
            $command->city,
            Frequency::from($command->frequency),
        );

        $this->subscriptionRepository->save($newSubscription);
    }
}