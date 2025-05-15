<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolationInterface;

class ExceptionListener implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();
        if ($e instanceof ValidationException) {
            $grouped = [];
            /** @var ConstraintViolationInterface $violation */
            foreach ($e->getErrors() as $violation) {
                $field = $violation->getPropertyPath();
                $grouped[$field][] = $violation->getMessage();
            }

            $response = new JsonResponse(
                ['errors' => $grouped],
                Response::HTTP_BAD_REQUEST
            );

            $event->setResponse($response);
        }

        if ($e instanceof \DomainException) {
            $response = new JsonResponse([
                'errors' => $e->getMessage(),
            ], $e->getCode());
            $event->setResponse($response);
        }

        if ($e instanceof NotFoundHttpException) {
            $response = new JsonResponse([
                'errors' => $e->getMessage(),
            ], 404);
            $event->setResponse($response);
        }

        if ($e instanceof \InvalidArgumentException) {
            $response = new JsonResponse([
                'errors' => $e->getMessage(),
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
