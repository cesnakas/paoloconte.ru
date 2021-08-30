<?php
$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('sale');


//дата последнего запуска скрипта
$nameOption = 'the_the_last_start_update_status';
const STATUS_SHEEPLA = 'STATUS_SHEEPLA';
const CTN_SHEEPLA = 'CTN_SHEEPLA';

$timeTheLastStartUpdateStatus = \COption::GetOptionString('sale', $nameOption);
if (empty($timeTheLastStartUpdateStatus)) {
    $timeTheLastStartUpdateStatus = strtotime('01.01.2000');
    \COption::SetOptionString('sale', $nameOption, $timeTheLastStartUpdateStatus);
}else{
    $currentTime = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), time());
    \COption::SetOptionString('sale', $nameOption, strtotime($currentTime));
}
$sdekOrders = array();
$status = array();


//отфильтровать по дате возможностями модуля сдек не получилось
//он сравнивает даты как строку
$requests = sqlSdekOrders::select(array(),array());
while($request=$requests->Fetch()){
    $request['UPTIME'] = (int)$request['UPTIME'];
    if($timeTheLastStartUpdateStatus > $request['UPTIME']){
        continue;
    }
    $status[$request['STATUS']] = $request['STATUS'];
    $sdekOrders[$request['ORDER_ID']] = $request;
}

foreach ($status as &$name){
    switch ($name) {
        case 'DELIVD':
            $name = "Заказ уже доставлен клиенту.";
            break;
        case 'ERROR':
            $name = "Заявка отклонена из-за ошибок в параметрах. Исправьте ошибки и отправьте ее заново.";
            break;
        case 'OTKAZ':
            $name = "Клиент уже отказался от заказа.";
            break;
        case 'TRANZT':
            $name = "Заказ в пути.";
            break;
        case  'PVZ':
            $name = "Заказ уже на пункте самовывоза СДЭК.";
            break;
        case 'OK':
            $name = "Заявка на доставку подтверждена.";
            break;
        case 'CORIER':
            $name = "Заказ уже у курьера СДЭК.";
            break;
        case 'NEW':
            $name = "Заявка на доставку заказа еще не отсылалась.";
            break;
        case 'STORE':
            $name = "Заказ уже на складе СДЭК.";
            break;
    }
}


$db_vals = CSaleOrderPropsValue::GetList(
    array(),
    array(
        'ORDER_ID' => array_keys($sdekOrders),
        'CODE' => array(CTN_SHEEPLA, STATUS_SHEEPLA),
    )
);

while ($arVals = $db_vals->Fetch()){
    $STATUS_SHEEPLA_ID = $arVals['ORDER_PROPS_ID'];
    switch ($arVals['CODE']) {
        case STATUS_SHEEPLA:
            CSaleOrderPropsValue::Update($arVals['ID'], array('ORDER_ID' => $arVals['ORDER_ID'], "CODE"=>STATUS_SHEEPLA, 'VALUE' => $status[$sdekOrders[$arVals['ORDER_ID']]['STATUS']]));
            break;
        case CTN_SHEEPLA:
            CSaleOrderPropsValue::Update($arVals['ID'], array('ORDER_ID' => $arVals['ORDER_ID'], "CODE"=>CTN_SHEEPLA, 'VALUE' => $sdekOrders[$arVals['ORDER_ID']]['SDEK_ID']));
            break;
    }
}

return;
