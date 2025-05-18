<?php

declare(strict_types=1);

namespace App\Command;

use App\Enum\Frequency;
use App\Services\Weather\SubscriberWeatherNotifier;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'weather:update', description: 'Update the current weather hourly')]
class UpdateWeatherCommand extends Command
{
    public function __construct(
        private SubscriberWeatherNotifier $weatherNotifier,
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

        $this->weatherNotifier->run($frequency);

        $output->writeln('<info>All weather update messages dispatched successfully.</info>');

        return Command::SUCCESS;
    }
}
