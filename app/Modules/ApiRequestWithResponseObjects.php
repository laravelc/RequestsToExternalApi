<?php

namespace App\Modules;

use App\Modules\Exceptions\ObjectNotCreatedException;
use App\Modules\Exceptions\ProcessDidNotCancelException;
use App\Modules\Response\Canceled;
use App\Modules\Response\Created;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Пример запроса с кешированием
 */
class ApiRequestWithResponseObjects
{
    private const api_url = 'https://api.example.com';

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
     * Создать объект
     *
     * @param object $obj
     * @return Created
     * @throws ObjectNotCreatedException
     */
    public function create(object $obj): Created
    {
        try {
            $result = $this->client->request('post', self::api_url, [
                'json' => $obj->jsonSerialize(),
            ]);
        } catch (GuzzleException|Exception) {
            throw new ObjectNotCreatedException('Не удалось создать объект #' . $obj->id);
        }

        $response = json_decode($result->getBody(), true) ?? [];

        if (!$response['isSuccess']) {
            throw new ObjectNotCreatedException('Не удалось создать объект #' . $obj->id);
        }

        return new Created(
            $response['id'],
            'ok',
            $result->getStatusCode()
        );
    }


    /**
     * @throws ProcessDidNotCancelException
     * @throws GuzzleException
     */
    public function cancel(string $id): Canceled
    {
        $response = $this->client->request(
            'post',
            self::api_url . 'cancel',
            [
                'id' => $id,
            ]
        );

        if (isset($response['id'], $response['status'])) {
            return new Canceled(
                $response['id'],
                'canceled',
                0
            );
        }

        throw new ProcessDidNotCancelException('Не удалось отменить процесс #' . $id);
    }
}
