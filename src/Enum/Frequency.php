<?php

declare(strict_types=1);

namespace App\Enum;

enum Frequency: string
{
    case HOURLY = 'hourly';
    case DAILY = 'daily';

    public const array VALUES = [
        self::HOURLY->value,
        self::DAILY->value,
    ];
}
