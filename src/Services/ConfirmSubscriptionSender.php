<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\SubscriptionCreated;
use App\Repository\SubscriptionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

#[AsMessageHandler]
class ConfirmSubscriptionSender
{
    public function __construct(
        private LoggerInterface $logger,
        private SubscriptionRepository $subscriptionRepository,
        private MailerInterface $mailer,
        private RouterInterface $router,
    ) {
    }

    public function __invoke(SubscriptionCreated $event): void
    {
        $subscription = $this->subscriptionRepository->findByEmail($event->getEmail());

        if (!$subscription) {
            return;
        }

        if (!$subscription->getConfirmToken()) {
            return;
        }

        $url = $this->router->generate(
            'subscription_confirm',
            ['token' => $subscription->getConfirmToken()->getToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $mail = (new TemplatedEmail())
            ->to(new Address($subscription->getEmail()->getValue()))
            ->subject('Please confirm your Weather API subscription')
            ->htmlTemplate('emails/welcome.html.twig')
            ->context([
                'recipientEmail' => $subscription->getEmail()->getValue(),
                'confirmationUrl' => $url,
            ]);

        $this->mailer->send($mail);

        $this->logger->debug('Mail sent to '.$event->getEmail(), [
            'email' => $subscription->getEmail(),
        ]);
    }
}
