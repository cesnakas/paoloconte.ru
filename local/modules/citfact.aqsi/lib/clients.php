<?php

namespace Citfact\Aqsi;

abstract class Clients extends Rest {

    /**
     * Идентификатор группы в системе aQsi
     * @var string 
     */
    protected $groupId;

    /**
     * 
     * @param string $key
     */
    public function __construct(string $key) {
        parent::__construct($key);
    }

    /**
     * Записывает url запроса в переменную экземпляра класса
     */
    protected function curlUrl() {
        $this->curlUrl = $this->baseUrl . $this->arUrl["Clients::create"];
    }
}

