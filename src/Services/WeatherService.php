<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Weather;
use App\Exceptions\WeatherApiException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

class WeatherService
{
    private string $baseUrl;

    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger,
        private string $apiToken,
    ) {
        $this->baseUrl = 'http://api.weatherapi.com/v1';
    }

    public function getCurrent(?string $city): Weather
    {
        Assert::notEmpty($city, 'The city should not be empty');

        try {
            $response = $this->httpClient->request(
                method: 'GET',
                url: sprintf('%s/current.json', $this->baseUrl),
                options: [
                    'query' => ['q' => $city, 'key' => $this->apiToken],
                ]
            );

            $data = $response->toArray();
            $current = $data['current'];

            return new Weather($current['temp_c'], $current['humidity'], $current['condition']['text']);
        } catch (HttpExceptionInterface $e) {
            $this->logger->error('Weather API request failed', [
                'city' => $city,
                'exception' => $e,
            ]);

            throw new WeatherApiException('City not found');
        }
    }
}
