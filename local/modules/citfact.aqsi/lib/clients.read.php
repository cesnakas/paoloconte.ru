<?php

/**
 * Получение данных клиента
 * https://api.aqsi.ru/#tag/Clients/paths/~1v2~1Clients~1{clientId}/get
 */

namespace Citfact\Aqsi\Clients;

class Read extends \Citfact\Aqsi\Clients {

    private $clientId;

    /**
     * Конструктор
     * @param string $key ключ авторизавии API
     */
    public function __construct(string $key) {
        parent::__construct($key);
        $this->curlHttpMethod = "GET";
    }

    /**
     * Получение данных клиента
     * @param string $clientId
     * @return array
     */
    public function get(string $clientId): array {
        $this->clientId = $clientId;
        $this->dataForCurl();
        $result = $this->save();
        return $this->isJson($result) ? json_decode($result, true) : (array) $result;
    }

    /**
     * Данные для запроса cUrl
     */
    protected function dataForCurl() {
        $this->curlUrl();
    }

    /**
     * Формирует URL для запроса cUrl
     */
    protected function curlUrl() {
        $this->curlUrl = $this->baseUrl . str_replace("{clientId}", $this->clientId, $this->arUrl["Clients::read"]);
    }

}

