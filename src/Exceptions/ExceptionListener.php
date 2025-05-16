<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();

        if ($e instanceof \DomainException) {
            $response = new JsonResponse([
                'error' => $e->getMessage(),
            ], $e->getCode());
            $event->setResponse($response);
        }

        if ($e instanceof NotFoundHttpException) {
            $response = new JsonResponse([
                'error' => $e->getMessage(),
            ], 404);
            $event->setResponse($response);
        }

        if ($e instanceof \InvalidArgumentException) {
            $response = new JsonResponse([
                'error' => 'Invalid input',
            ], 400);
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 1000],
        ];
    }
}
