<?php

namespace Citfact\Aqsi;

/**
 * Методы для API aQsi
 *
 * @author Arseny Mogilev
 */
abstract class Rest {

    /**
     * Ключ для авторизации в API aQsi
     * @var string 
     */
    protected $key;

    /**
     * Идентификатор магазина в aQsi
     * @var string 
     */
    protected $shop;

    /**
     * Параметры для сеанса cUrl
     * @var type array
     */
    protected $curlOptions = [];

    /**
     * Заголовок сеанса cUrl
     * @var array 
     */
    protected $curlHeader;

    /**
     * Принимать только ответ JSON запроса cUrl
     * @var bool 
     */
    protected $acceptOnlyJson = true;

    /**
     * Метод http запроса cUrl
     * @var string 
     */
    protected $curlHttpMethod;

    /**
     * URL запроса метода REST
     * @var string 
     */
    protected $curlUrl;

    /**
     * Массив полей для CURLOPT_POSTFIELDS
     * @var array 
     */
    protected $arPostFields;

    /**
     * Получать в ответе заголовки запроса
     * @var bool|int 
     */
    protected $acceptHeader = false;

    /**
     * Возврата результата вместо прямого вывода в браузер
     * @var bool 
     */
    protected $returnTransfer = true;

    /**
     * Базовый url API
     * @var string 
     */
    protected $baseUrl = "https://api.aqsi.ru";

    /**
     * url для методв
     * @var array 
     */
    protected $arUrl = [
        "Orders::create" => "/pub/v2/Orders/simple",
        "Clients::create" => "/pub/v2/Clients",
        "Clients::read" => "/pub/v2/Clients/{clientId}"
    ];

    /**
     * Формирует массив $arPostFields
     */
    abstract protected function dataForCurl();

    /**
     * Конструктор
     * @param string $key ключ API
     */
    public function __construct(string $key) {
        $this->key = $key;
    }

    /**
     * Сохранение данных и выпонение запроса в aQsi
     */
    public function save() {
        $this->makeHeader();
        $this->makeOptions();
        return $this->curlExec();
    }

    /**
     * Принимать в ответе cUrl только JSON
     * @param bool $onlyJson
     */
    public function acceptOnlyJson(bool $onlyJson = true) {
        $this->acceptOnlyJson = $onlyJson;
    }

    /**
     * Выполнение cUrl запроса к API
     * @return string
     * @throws Exception Ошибка запроса
     */
    protected function curlExec(): string {
        $ch = curl_init();
        curl_setopt_array($ch, $this->curlOptions);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }
        curl_close($ch);
        try {
            return $response;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Проверка, является ли строка $string json
     * @param string $string
     * @return bool
     */
    protected function isJson(string $string): bool {
        return (is_array(json_decode($string, true)) && (json_last_error() == \JSON_ERROR_NONE));
    }

    /**
     * Фомирует массив для CURLOPT_HTTPHEADER
     */
    protected function makeHeader() {
        $this->curlHeader = ["x-client-key: Application {$this->key}"];
        $this->curlHeader[] = "Content-Type: application/json";
        if ($this->acceptOnlyJson) {
            $this->curlHeader[] = "Accept: application/json";
        }
    }

    /**
     * Формирует CURLOPT
     */
    protected function makeOptions() {
        $this->curlOptions = [
            CURLOPT_URL => $this->curlUrl,
            CURLOPT_HTTPHEADER => $this->curlHeader,
            CURLOPT_HEADER => $this->acceptHeader
        ];
        if ($this->returnTransfer) {
            $this->curlOptions[CURLOPT_RETURNTRANSFER] = $this->returnTransfer;
        }
        if (strtoupper($this->curlHttpMethod) === "POST") {
            $this->curlOptions[CURLOPT_POST] = true;
            if (!empty($this->arPostFields)) {
                $this->curlOptions[CURLOPT_POSTFIELDS] = json_encode($this->arPostFields);
            }
        }
    }

    protected function genUuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                // 16 bits for "time_mid"
                mt_rand(0, 0xffff),
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand(0, 0x0fff) | 0x4000,
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand(0, 0x3fff) | 0x8000,
                // 48 bits for "node"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

}
