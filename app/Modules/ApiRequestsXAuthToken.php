<?php

namespace App\Modules;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ApiRequestsXAuthToken
{
    /**
     * @var Client Клиент
     */
    private Client $client;

    /**
     * Конструктор
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Запрос
     *
     * @param string $url
     * @param string $method
     * @param array $data
     * @return array|mixed|null
     */
    private function sendRequest(string $url, string $method, array $data): mixed
    {
        $data = [
            'request' => $data,
        ];

        $response = null;
        try {
            $data['headers'] = ['X-Auth-Token' => $this->getToken()];

            $result = $this->client->$method($url, $data);
            $response = json_decode($result->getBody(), true) ?? [];

            $data['response_code'] = $result->getStatusCode();

            if ($data['response_code'] == ResponseAlias::HTTP_OK) {
                $data['response'] = $response;
                Log::channel('daily')->debug('Успешный запрос ', $data);
            }
        } catch (Exception $e) {
            $data['error_text'] = $e->getMessage();
            Log::channel('daily')->error('Запрос с ошибкой ', $data);
        }

        return $response;
    }
}
