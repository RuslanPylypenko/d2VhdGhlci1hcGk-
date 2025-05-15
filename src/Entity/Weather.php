<?php

declare(strict_types=1);

namespace App\Entity;

class Weather
{
    public function __construct(
        public float $temperature,
        public float $humidity,
        public string $description,
    ) {
    }
}
