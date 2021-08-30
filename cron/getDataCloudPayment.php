<?php
$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;

Loader::IncludeModule('sale');


$arDefaultIso = [
    'USD' => 840,
    'EUR' => 978,
    'CNY' => 156,
    'RUB' => 643,
    'RUR' => 643,
];
$orders = [];
$select = [
    'ID',
    'DATE_INSERT',
    'PROPERTY_VAL_BY_CODE_PS_STATUS_CODE',
];
$arFilter = Array("PAY_SYSTEM_ID" => '21', 'PROPERTY_VAL_BY_CODE_PS_STATUS_CODE' => false);
$rsOrder = CSaleOrder::GetList(Array('DATE_INSERT' => 'ASC'), $arFilter, false, false, $select);
while($arOrder = $rsOrder->Fetch())
{
    $date = new DateTime($arOrder['DATE_INSERT']);
    $orders[$date->format('Y-m-d')][] = $arOrder['ID'];
}


foreach (array_keys($orders) as $date){
    $request=array(
        "Date"=>$date,
        "TimeZone"=> "MSK"
    );

    if($curl = curl_init())
    {
        $ch = curl_init('https://api.cloudpayments.ru/payments/list');
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch,CURLOPT_USERPWD,'pk_8e058cfcbc02863916db03cddd782' . ":" . 'a648cfc4f592e1b6134f82125d249985');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        $result = json_decode($content);
        if(count($result->Model) < 1){
            continue;
        }
        foreach ($result->Model as $model){
            if(!in_array($model->InvoiceId, $orders[$date])){
                continue;
            }
            $arOrderFields = array(
                "PS_SUM" => $model->Amount,
                "PS_CURRENCY" => $arDefaultIso[$model->Currency],
                "PS_RESPONSE_DATE" => Date(\CDatabase::DateFormatToPHP(\CLang::GetDateFormat("FULL", LANG))),
                "PS_STATUS" => "Y",
                "PS_STATUS_DESCRIPTION" => $model->CardFirstSix . 'XXXXXX'. $model->CardLastFour /*. ";" . $model->Name*/,
                "PS_STATUS_MESSAGE" => $model->Status,
                "PS_STATUS_CODE" => "Y",
                "UPDATED_1C" => "N"
            );
            CSaleOrder::Update($model->InvoiceId, $arOrderFields);
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/logs/getDataCloudPayment.txt", $model->InvoiceId. ', ', FILE_APPEND | LOCK_EX);
        }


    }
}
