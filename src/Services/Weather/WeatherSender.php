<?php

declare(strict_types=1);

namespace App\Services\Weather;

use App\Events\WeatherUpdate;
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
        private MailerInterface $mailer,
        private RouterInterface $router,
    ) {
    }

    public function __invoke(WeatherUpdate $event): void
    {
        $unsubscribeUrl = $this->router->generate(
            'unsubscribe',
            ['token' => $event->getUnsubscribeToken()->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $mail = (new TemplatedEmail())
            ->to(new Address($event->getEmail()->getValue()))
            ->subject('Weather update for '.$event->getCity())
            ->htmlTemplate('emails/weather.html.twig')
            ->context([
                'unsubscribeUrl' => $unsubscribeUrl,
                'weather' => $event->getWeather(),
            ]);

        $this->mailer->send($mail);

        $this->logger->debug('Weather sent to '.$event->getEmail()->getValue());
    }
}
