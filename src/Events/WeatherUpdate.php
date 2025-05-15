<?php

declare(strict_types=1);

namespace App\Events;

class WeatherUpdate
{
    public function __construct(
        private string $city,
    ) {
    }

    public function getCity(): string
    {
        return $this->city;
    }
}
