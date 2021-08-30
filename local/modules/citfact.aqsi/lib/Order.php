<?php

namespace Citfact\Aqsi;

/**
 * Работа с заказами
 *
 * @author Arseny Mogilev
 */
class Orders extends Rest {
    /**
     * Метод
     * @var string 
     */
    private $func;

    /**
     * Сырые данные полученные из вне, из Bitrix Api 
     * @var array 
     */
    private $data;

    /**
     * Идентификатор заказа в системе Bitrix
     * @var int|string 
     */
    private $bxOrderId;

    /**
     * Объект заказа в Bitrix
     * @var \Bitrix\Sale\Order 
     */
    private $bxOrder;

    /**
     * Конструктор
     * @param string $key ключ авторизавии API
     */
    public function __construct(string $key) {
        parent::__construct($key);
    }

    /**
     * Динамический метод
     * @param string $func
     * @param array $args
     */
    public function __call(string $func, array $args) {
        $this->func = $func;
        $this->data = $args;
        $this->curlUrl();
        $this->dataForCurl();
    }

    /**
     * Формирует данные для cUrl запроса и записывает в переменную экземпляра класса
     * @return array
     */
    protected function dataForCurl() {
        $this->arPostFields = [];
        $method = strtolower($this->func);
        switch ($method) {
            case "create":
                $this->bxOrderId = $this->data[0];
                $arOrder = $this->getBxOrderData();
                break;
        }
    }

    /**
     * Записывает url запроса в переменную экземпляра класса
     */
    private function curlUrl() {
        $this->curlUrl = $this->baseUrl;
        $cellUrl = __CLASS__ . "::" . $this->func;
        if (!empty($this->arUrl[$cellUrl])) {
            $this->curlUrl .= $this->arUrl[$cellUrl];
        }
    }
    
    private function getBxOrderData(): array {
        \Bitrix\Main\Loader::includeModule("sale");
        $arOrder = [];
        if (!empty($this->bxOrderId)) {
            $this->bxOrder = \Bitrix\Sale\Order::load($this->bxOrderId);
        }
        return $arOrder;
    }

    
}
