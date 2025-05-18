<?php

declare(strict_types=1);

namespace App\Scheduler\Handler;

use App\Scheduler\Message\WeatherUpdateMessage;
use App\Services\Weather\SubscriberWeatherNotifier;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class WeatherUpdateHandler
{
    public function __construct(
        private SubscriberWeatherNotifier $weatherNotifier,
    ) {
    }

    public function __invoke(WeatherUpdateMessage $message): void
    {
        $this->weatherNotifier->run($message->frequency);
    }
}
