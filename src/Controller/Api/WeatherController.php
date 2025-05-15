<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Services\WeatherService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class WeatherController extends AbstractController
{
    public function __construct(
        private WeatherService $weatherService,
    ) {
    }

    #[Route(path: '/weather', methods: ['GET'])]
    public function current(Request $request): JsonResponse
    {
        $city = $request->query->get('city');

        $weather = $this->weatherService->getCurrent($city);

        return $this->json($weather);
    }
}
