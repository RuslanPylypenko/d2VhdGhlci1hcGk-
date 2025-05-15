<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\SubscriptionCreated;
use App\Events\WeatherUpdate;
use App\Repository\SubscriptionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

#[AsMessageHandler]
class WeatherSender
{
    public function __construct(
        private LoggerInterface $logger,
        private WeatherService $weatherService,
        private SubscriptionRepository $subscriptionRepository,
        private MailerInterface $mailer,
        private RouterInterface $router,
    ) {
    }

    public function __invoke(WeatherUpdate $event): void
    {
        $subscribers = $this->subscriptionRepository->getActiveSubscribersForCity($event->getCity());

        $this->logger->debug('Subscribers: ', [
            'subscribers' => count($subscribers),
        ]);

        if (empty($subscribers)) {
            return;
        }

        $weather = $this->weatherService->getCurrent($event->getCity());

        foreach ($subscribers as $subscriber) {
            $unsubscribeUrl = $this->router->generate(
                'unsubscribe',
                ['token' => $subscriber->getUnsubscribeToken()->getToken()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $mail = (new TemplatedEmail())
                ->to(new Address($subscriber->getEmail()->getValue()))
                ->subject('Please confirm your Weather API subscription')
                ->htmlTemplate('emails/weather.html.twig')
                ->context([
                    'unsubscribeUrl' => $unsubscribeUrl,
                    'weather' => $weather,
                ]);

            $this->mailer->send($mail);

            $this->logger->debug('Weather sent to '.$subscriber->getEmail()->getValue());
        };
    }
}
