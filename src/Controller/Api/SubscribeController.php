<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Commands\SubscribeCommand;
use App\Exceptions\ValidationException;
use App\Services\SubscriptionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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

        return $this->json([]);
    }
}
