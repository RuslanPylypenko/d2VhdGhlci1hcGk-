<?php

declare(strict_types=1);

namespace App\Services\Weather;

use App\Enum\Frequency;
use App\Events\WeatherUpdate;
use App\Repository\SubscriptionRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class SubscriberWeatherNotifier
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private WeatherService $weatherService,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function run(Frequency $frequency): void
    {
        $cities = $this->subscriptionRepository->getUniqueCities();

        foreach ($cities as $city) {
            $weather = $this->weatherService->getCurrent($city);
            $subscribers = $this->subscriptionRepository->findActiveSubscribers($city, $frequency);

            foreach ($subscribers as $s) {
                $message = new WeatherUpdate($city, $s->getEmail(), $s->getUnsubscribeToken(), $weather);
                $this->messageBus->dispatch($message);
            }
        }
    }
}
