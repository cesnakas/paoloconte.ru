<?
$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


use Citfact\InfoBip\Proceed;
use Bitrix\Main\Config\Option;
use Bitrix\Sale;

$arOrdersToCheck = getOrders();
checkSmsStatus($arOrdersToCheck);


/**
 * Получаем список заказов со статусом СМС "Отправлено / PENDING"
 * @return array
 */
function getOrders()
{
    $arOrders = array();
    $arFilter = array(
        'PROPERTY_VAL_BY_CODE_SMS_STATUS' => 'PENDING'
    );

    $enableSmsActivityAuto = Option::get('sale', 'ORDER_ON_SMS_ACTIVITY_AUTO');
    if ($enableSmsActivityAuto == 'Y'){
        $arFilter['STATUS_ID'] = 'C';
    }

    $arSelect = array(
        'ID',
        'STATUS_ID',
        'PROPERTY_VAL_BY_CODE_SMS_STATUS',
        'PROPERTY_VAL_BY_CODE_SMS_MESSAGE_ID',
    );

    $resOrders = \CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter, false, false, $arSelect);
    while ($arOrder = $resOrders->Fetch()) {
        $arOrders[$arOrder['ID']] = $arOrder;
    }

    return $arOrders;
}


function checkSmsStatus($arOrdersToCheck)
{
    $enableSmsActivity = Option::get('sale', 'ORDER_ON_SMS_ACTIVITY');
    if ($enableSmsActivity != 'Y'){
        return;
    }
    $enableSmsActivityAuto = Option::get('sale', 'ORDER_ON_SMS_ACTIVITY_AUTO');

    $arNavMessages = array();
    foreach ($arOrdersToCheck as $arOrder) {
        $parameters[] = 'messageId=' . $arOrder['PROPERTY_VAL_BY_CODE_SMS_MESSAGE_ID'];
        $arNavMessages[$arOrder['PROPERTY_VAL_BY_CODE_SMS_MESSAGE_ID']] = $arOrder['ID'];
    }

    $operationName = Proceed::OPERATION_GET_SMS_STATUS;

    $operation = new Proceed($parameters, $operationName);
    Proceed::log('getSmsStatus ' . date('Y-m-d H:i:s'));
    $operation->send();
    $response = $operation->getResponse();
    $arResponse = json_decode($response, true);


    //--Обновляем свойство заказа
    foreach ($arResponse['results'] as $arResult) {
        $orderId = $arNavMessages[$arResult['messageId']];
        $smsStatus = $arResult['status']['groupName'];
        $propertyStatus = $arOrdersToCheck[$orderId]['PROPERTY_VAL_BY_CODE_SMS_STATUS'];
        if ($smsStatus != $propertyStatus) {
            switch ($smsStatus) {
                case 'ACCEPTED':
                case 'PENDING':
                    $propertyStatus = 'PENDING';
                    break;
                case 'DELIVERED':
                    $propertyStatus = 'DELIVERED';
                    break;
                case 'UNDELIVERABLE':
                case 'EXPIRED':
                case 'REJECTED':
                    $propertyStatus = 'UNDELIVERABLE';
                    break;
            }

            $order = Sale\Order::load($orderId);
            if ($enableSmsActivityAuto == 'Y'){
                $order->setField('STATUS_ID', 'P');
            }
            foreach ($order->getPropertyCollection() as $property) {
                if ($property->getField('CODE') == 'SMS_STATUS') {
                    $property->setValue($propertyStatus);
                    break;
                }
            }

            $saveResult = $order->getPropertyCollection()->save();
            $order->save();
        }
    }

}

?>