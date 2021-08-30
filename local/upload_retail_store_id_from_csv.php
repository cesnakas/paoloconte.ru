<?php
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader,
    Bitrix\Main\Diag\Debug,
    Bitrix\Catalog\StoreTable;

global $USER;

if (!$USER->IsAdmin()) {
    die();
}

if (!Loader::includeModule("catalog")) {
    Debug::writeToFile('модуль catalog не подключен', $dateTime, $pathToLog);
    die();
}

$pathToCSV = $_SERVER["DOCUMENT_ROOT"].'/local/retail_xml.csv';
$pathToLog = '/local/logs/upload_retail_store_id.log';
$dateTime = date("d.m.Y H:i:s");
$arCSVData = [];
$row = 1;

if (($handle = fopen($pathToCSV, "r")) !== FALSE) {
    while (($res = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (!empty($res)) {
            $arCSVData[$res[0]] = $res[1];
            $row++;
        }
    }
    fclose($handle);
}

Debug::writeToFile('CSV файл обработан. Кол-во строк:'.$row, $dateTime, $pathToLog);

if (!empty($arCSVData)) {
    ksort($arCSVData);
    foreach ($arCSVData as $storeId => $retailId) {
        $res = StoreTable::update($storeId, ['fields' => ["UF_RETAIL_ID" => $retailId]]);
        if ($res->isSuccess()) {
            Debug::writeToFile('Успешно записан retailID:' . $retailId . ' для склада с ID:' . $storeId, 'SUCCESS '.$dateTime, $pathToLog);
        } else {
            Debug::writeToFile('Ошибка записи retailID:' . $retailId . ' для склада с ID:' . $storeId, 'ERROR '.$dateTime, $pathToLog);
        }
    }
}
