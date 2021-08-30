<?php

/**
 * https://api.aqsi.ru/#tag/Orders/paths/~1v2~1Orders~1simple/post - Добавление заказа в aQsi
 */

namespace Citfact\Aqsi;

/**
 * Работа с заказами
 *
 * @author Arseny Mogilev
 */
abstract class Orders extends Rest {

    /**
     * Идентификатор заказа в системе Bitrix
     * @var int|string 
     */
    protected $bxOrderId;

    /**
     * Метод
     * @var string 
     */
    protected $func;

    /**
     * Конструктор
     * @param string $key ключ авторизавии API
     */
    public function __construct(string $key) {
        parent::__construct($key);
    }

    /**
     * Установка идентификатора магазина в aQsi
     */
    abstract public function setShop(string $shop);

    /**
     * 
     */
    public function save() {
        return parent::save();
    }

    /**
     * Записывает url запроса в переменную экземпляра класса
     */
    protected function curlUrl() {
        $this->curlUrl = $this->baseUrl;
        $this->curlUrl .= $this->arUrl["Orders::" . $this->func];
    }

}
