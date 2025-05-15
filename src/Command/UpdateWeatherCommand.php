<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'app:update-weather', description: 'Update the current weather')]
class UpdateWeatherCommand extends Command
{

}