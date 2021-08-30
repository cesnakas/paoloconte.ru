<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/local/modules/sberbank.ecom/config.php");

define('LOG_FILENAME', $_SERVER['DOCUMENT_ROOT'] . '/local/modules/sberbank.ecom/log.txt');


class RBS
{
    const test_url = TEST_URL;
    const prod_url = PROD_URL;
    const log_file = LOG_FILE;

    private $user_name;
    private $password;
    private $two_stage;
    private $test_mode;
    private $logging;

    public $orderStatus = array(
        'NOT_PAID' => 0, // - Заказ зарегистрирован, но не оплачен;
        'APPROVED' => 1, // - Предавторизованная сумма захолдирована (для двухстадийных платежей);
        'DEPOSITED' => 2, // - Проведена полная авторизация суммы заказа;
        'REVERSED' => 3, // - Авторизация отменена;
        'REFUNDED' => 4, // - По транзакции была проведена операция возврата;
        'THROUGH_ACS' => 5, // - Инициирована авторизация через ACS банка-эмитента;
        'REJECTED' => 6, // - Авторизация отклонена.
    );

    public function RBS($user_name, $password, $two_stage, $test_mode, $logging)
    {
        $this->user_name = $user_name;
        $this->password = $password;
        $this->two_stage = $two_stage;
        $this->test_mode = $test_mode;
        $this->logging = $logging;
    }

    private function gateway($method, $data)
    {
        $data['userName'] = $this->user_name;
        $data['password'] = $this->password;
        $data['CMS'] = 'Bitrix';
        $data['jsonParams'] = json_encode(array('CMS' => 'Bitrix', 'Module-Version' => VERSION));
        $dataEncoded = http_build_query($data);

        if (SITE_CHARSET != 'UTF-8') {
            global $APPLICATION;
            $dataEncoded = $APPLICATION->ConvertCharset($dataEncoded, 'windows-1251', 'UTF-8');
            $data = $APPLICATION->ConvertCharsetArray($data, 'windows-1251', 'UTF-8');
        }

        if ($this->test_mode) {
            $url = self::test_url;
        } else {
            $url = self::prod_url;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . $method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $dataEncoded,
            CURLOPT_HTTPHEADER => array('CMS: Bitrix', 'Module-Version: ' . VERSION),
            CURLOPT_SSLVERSION => 6
        ));
        $response = curl_exec($curl);
        curl_close($curl);

        if (!$response) {
            $client = new \Bitrix\Main\Web\HttpClient(array(
                'waitResponse' => true
            ));
            $client->setHeader('CMS', 'Bitrix');
            $client->setHeader('Module-Version', VERSION);
            $response = $client->post($url . $method, $data);
        }

        if (!$response) {
            $response = array(
                'errorCode' => 999,
                'errorMessage' => 'The server does not have SSL/TLS encryption on port 443',
            );
        } else {
            if (SITE_CHARSET != 'UTF-8') {
                global $APPLICATION;
                $APPLICATION->ConvertCharset($response, 'windows-1251', 'UTF-8');
            }
            $response = \Bitrix\Main\Web\Json::decode($response);

            if ($this->logging) {
                $this->logger($url, $method, $data, $response);
            }
        }

        return $response;
    }

    private function logger($url, $method, $data, $response)
    {
        return AddMessage2Log('RBS PAYMENT ' . $url . $method . ' REQUEST: ' . json_encode($data) . ' RESPONSE: ' . json_encode($response), 'sberbank.ecom');
    }

    function register_order($order_number, $amount, $return_url, $currency, $orderDescription = '', $arCheck = null)
    {
        $iso = COption::GetOptionString("sberbank.ecom", "iso", serialize(array()));
        $arCurrency = unserialize($iso);
        $arCurrency = array_filter($arCurrency);
        $arDefaultIso = unserialize(DEFAULT_ISO);

        if (is_array($arDefaultIso)) {
            $arCurrency = array_merge($arDefaultIso, $arCurrency);
        }

        $data = array(
            'orderNumber' => $order_number,
            'amount' => $amount,
            'returnUrl' => $return_url,
            'description' => $orderDescription,
        );

        if ($currency && isset($arCurrency[$currency])) {
            $data['currency'] = $arCurrency[$currency];
        }

        if ($arCheck) {
            $data = array_merge($data, $arCheck);
            $data['orderBundle'] = \Bitrix\Main\Web\Json::encode($data['orderBundle']);
        }

        if ($this->two_stage) {
            $method = 'registerPreAuth.do';
        } else {
            $method = 'register.do';
        }

        $response = $this->gateway($method, $data);

        return $response;
    }

    function refund($orderId, $amount = 0, $refundItems = array())
    {
        $data = array(
            'orderId' => $orderId,
            'amount' => $amount
        );

        if (!empty($refundItems)) {
            $data['refundItems']['items'] = $refundItems;
            $data['refundItems'] = json_encode($data['refundItems']);
        }

        $response = $this->gateway('refund.do', $data);

        return $response;
    }

    function deposit($orderId, $amount = 0, $depositItems = array())
    {
        $data = array(
            'orderId' => $orderId,
            'amount' => $amount
        );

        if (!empty($depositItems)) {
            $data['depositItems']['items'] = $depositItems;
            $data['depositItems'] = json_encode($data['depositItems']);
        }

        $response = $this->gateway('deposit.do', $data);

        return $response;
    }

    function reverse($orderId)
    {
        $data = array(
            'orderId' => $orderId
        );

        $response = $this->gateway('reverse.do', $data);

        return $response;
    }

    public function get_order_status_by_orderId($orderId)
    {
        $data = array('orderId' => $orderId);
        $response = $this->gateway('getOrderStatusExtended.do', $data);

        return $response;
    }

    public function get_order_status_by_orderNumber($order_number)
    {
        $data = array('orderNumber' => $order_number);
        $response = $this->gateway('getOrderStatusExtended.do', $data);

        return $response;
    }
}