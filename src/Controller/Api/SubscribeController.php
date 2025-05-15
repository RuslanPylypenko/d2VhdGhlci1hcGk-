<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Commands\SubscribeCommand;
use App\Exceptions\ValidationException;
use App\Services\SubscriptionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SubscribeController extends AbstractController
{
    public function __construct(
        private ValidatorInterface $validator,
        private SubscriptionManager $subscriptionManager,
    ) {
    }

    #[Route(path: '/subscribe', name: 'subscribe', methods: ['POST'])]
    public function subscribe(Request $request): JsonResponse
    {
        $command = SubscribeCommand::fromArray($request->request->all());

        $errors = $this->validator->validate($command);
        if (count($errors) > 0) {
            throw ValidationException::create($errors);
        }
        $this->subscriptionManager->subscribe($command);

        return $this->json(['message' => 'Subscription successful. Confirmation email sent.']);
    }

    #[Route(path: '/confirm/{token}', name: 'subscription_confirm', methods: ['GET'])]
    public function confirm(string $token): JsonResponse
    {
        $this->subscriptionManager->confirm($token);

        return $this->json(['message' => 'Subscription confirmed successfully']);
    }

    #[Route(path: '/unsubscribe/{token}', name: 'unsubscribe', methods: ['GET'])]
    public function unsubscribe(string $token): JsonResponse
    {
        $this->subscriptionManager->unsubscribe($token);

        return $this->json(['message' => 'Unsubscribed successfully']);
    }
}
