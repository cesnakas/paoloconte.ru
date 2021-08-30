<?
IncludeModuleLangFile(__FILE__);

require_once('bank.php');

global $mess;
$mess["module_name"] = GetMessage('MODULE_NAME_' . BANK);//"����� �������� ����� ��������";
$mess["module_description"] = GetMessage('MODULE_DESCRIPTION_' . BANK); //"�������� - http://www.sberbank.ru/";
$mess["partner_name"] = GetMessage('PARTNER_NAME_' . BANK);//"��������";
$mess["partner_uri"] = GetMessage('PARTNER_URI_' . BANK);//"http://www.sberbank.ru/";

if (!defined('VERSION'))
    define(VERSION, '2.18.8');
if (!defined('VERSION_DATE'))
    define(VERSION_DATE, '2018-03-20 13:00:00');

$status = COption::GetOptionString("sberbank.ecom", "result_order_status", "P");
if (!defined('RESULT_ORDER_STATUS'))
    define('RESULT_ORDER_STATUS', $status);

$arDefaultIso = array(
    'USD' => 840,
    'EUR' => 978,
    'CNY' => 156,
    'RUB' => 643,
    'RUR' => 643,
);

switch (BANK) {
    case 'SBERBANK':
        if (!defined('PROD_URL'))
            define('PROD_URL', 'https://securepayments.sberbank.ru/payment/rest/'); 
        if (!defined('TEST_URL'))
            define('TEST_URL', 'https://3dsec.sberbank.ru/payment/rest/');
        break;
}

if (!defined('DEFAULT_ISO'))
    define(DEFAULT_ISO, serialize($arDefaultIso));

$SBER_PAY_SYSTEMS_ID = array(19, 20);
$PROPERTY_PS_ORDER_ID = 44;