<?php

namespace App\Modules\Response;

/**
 * Объект отмены запросом к API
 */
class Created
{
    /**
     * @var string Идентификатор данных
     */
    public string $dataId;

    /**
     * @var string Статус
     */
    public string $status;

    /**
     * @var string Код статуса
     */
    public string $statusCode;

    /**
     * Конструктор
     *
     * @param $dataId
     * @param $status
     * @param $statusCode
     */
    public function __construct($dataId, $status, $statusCode)
    {
        $this->dataId = $dataId;
        $this->status = $status;
        $this->statusCode = $statusCode;
    }
}
