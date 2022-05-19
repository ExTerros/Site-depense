<?php

namespace App\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallWeatherApiService
{


    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     */
    public function getWheatherData():array
    {
        $response = $this->client->request(
            'GET',
            'https://api.openweathermap.org/data/2.5/weather?lat=49.443232&lon=1.099971&appid=58e1e9dc4b22eb3d016b88104a272a15&lang=fr&units=metric'
        );
        // TODO: Remove API KEY 'https://api.openweathermap.org/data/2.5/weather?lat=49.443232&lon=1.099971&appid={key}&lang=fr&units=metric'
        return $response->toArray();
    }
}