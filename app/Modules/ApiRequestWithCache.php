<?php

namespace App\Modules;

use App\Modules\Exceptions\GetRequestException;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;

/**
 * Пример запроса с кешированием
 */
class ApiRequestWithCache
{
    private const api_url = 'https://api.example.com';
    private const TTL = 7 * 86400;

    /**
     * @var Client Клиент запроса
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
     * @param $id
     * @return mixed
     * @throws GetRequestException
     */
    public function getData($id): mixed
    {
        try {
            return Cache::remember(
                "key-for-save",
                self::TTL,
                function () use ($id) {
                    $res = $this->client->get(
                        self::api_url,
                        ['param' => $id]
                    );
                    return json_decode($res->getBody()->getContents(), true);
                }
            );
        } catch (Exception $exception) {
            throw new GetRequestException($exception->getMessage());
        }
    }
}
