<?php

declare(strict_types=1);

namespace App\Command;

use App\Events\WeatherUpdate;
use App\Repository\SubscriptionRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'weather:update', description: 'Update the current weather')]
class UpdateWeatherCommand extends Command
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cities = $this->subscriptionRepository->getUniqueCities();

        foreach ($cities as $city) {
            $output->writeln('Updating ' . $city);

            $this->messageBus->dispatch(new WeatherUpdate($city));
        }

        return Command::SUCCESS;
    }
}