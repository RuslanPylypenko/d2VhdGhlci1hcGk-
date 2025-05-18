<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\Frequency;
use App\Events\WeatherUpdate;
use App\Repository\SubscriptionRepository;
use App\Services\WeatherService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'weather:update', description: 'Update the current weather hourly')]
class UpdateWeatherCommand extends Command
{
    public function __construct(
        private SubscriptionRepository $subscriptionRepository,
        private WeatherService $weatherService,
        private MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('frequency', InputArgument::REQUIRED, 'Subscription frequency');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $freqArg = strtolower((string) $input->getArgument('frequency'));
        $frequency = Frequency::from($freqArg);

        $cities = $this->subscriptionRepository->getUniqueCities();

        foreach ($cities as $city) {
            $weather = $this->weatherService->getCurrent($city);
            $subscribers = $this->subscriptionRepository->findActiveSubscribers($city, $frequency);

            foreach ($subscribers as $s) {
                $message = new WeatherUpdate($city, $s->getEmail(), $s->getUnsubscribeToken(), $weather);
                $this->messageBus->dispatch($message);
            }
        }

        $output->writeln('<info>All weather update messages dispatched successfully.</info>');

        return Command::SUCCESS;
    }
}