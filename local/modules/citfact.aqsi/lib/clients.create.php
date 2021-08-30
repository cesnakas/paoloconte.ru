<?php

/**
 * Добавление клиента в aQsi
 * https://api.aqsi.ru/#tag/Clients/paths/~1v2~1Clients/post
 */

namespace Citfact\Aqsi\Clients;

class Create extends \Citfact\Aqsi\Clients {

    /**
     * Массив данных клиента
     * @var array 
     */
    private $clientData;

    /**
     * Конструктор
     * @param string $key ключ авторизавии API
     */
    public function __construct(string $key) {
        parent::__construct($key);
        $this->curlHttpMethod = "POST";
    }

    /**
     * Добавление клиента
     * @param array $clientData сформированный для curl массив данных клиента
     */
    public function add(array $clientData) {
        $this->clientData = $clientData;
        $this->curlUrl();
        $this->dataForCurl();
    }

    /**
     * Устанавливает идентификатор группы клиента
     * @param string $groupId
     */
    public function setGroupId(string $groupId) {
        $this->groupId = $groupId;
    }

    /**
     * Формирует данные для cUrl запроса и записывает в переменную экземпляра класса
     */
    protected function dataForCurl() {
        if (empty($this->clientData["group"])) {
            $this->clientData["group"] = ["id" => $this->groupId];
        }
        $this->arPostFields = $this->clientData;
    }
}

