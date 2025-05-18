<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\Email;
use App\Entity\Token;
use App\Entity\Weather;

class WeatherUpdate
{
    public function __construct(
        private string $city,
        private Email $email,
        private Token $unsubscribeToken,
        private Weather $weather,
    ) {
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getWeather(): Weather
    {
        return $this->weather;
    }

    public function getUnsubscribeToken(): Token
    {
        return $this->unsubscribeToken;
    }
}
