<?php

declare(strict_types=1);

namespace App\Scheduler;

use App\Scheduler\Message\WeatherUpdateMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('weather_update')]
class WeatherUpdateProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(RecurringMessage::cron('0 * * * *', WeatherUpdateMessage::hourly()))
            ->add(RecurringMessage::cron('0 8 * * *', WeatherUpdateMessage::daily()));
    }
}
