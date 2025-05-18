<?php

declare(strict_types=1);

namespace App\Scheduler\Message;

use App\Enum\Frequency;

class WeatherUpdateMessage
{
    private function __construct(
        public Frequency $frequency,
    ) {
    }

    public static function hourly(): self
    {
        return new self(Frequency::HOURLY);
    }

    public static function daily(): self
    {
        return new self(Frequency::DAILY);
    }
}
