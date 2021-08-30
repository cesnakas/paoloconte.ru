<?php
include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Diag\Debug,
    Bitrix\Main\Application,
    Bitrix\Main\Loader,
    \Citfact\Paolo;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    return false;
}

if (!Loader::includeModule("catalog")) {
    Debug::writeToFile('не удалось подключить модуль catalog', 'ERROR ' . $dateTime, $pathToLog);
    return false;
}

$pathToLog = '/local/var/logs/check_ostatki.log';
$request = Application::getInstance()->getContext()->getRequest()->toArray();
$storeId = (int)trim(strip_tags($request["storeId"]));
$artNum = trim(strip_tags($request["artNum"]));
$size = ((int)trim(strip_tags($request["size"]))) ?: 'не назначена';
$dateTime = date('d-m-Y H:i:s');
$success = false;
$msg = "";
$arResult = $err = [];

if (!$storeId || !$artNum) {
    Debug::writeToFile('не правильно передан ID склада или Артикул товара', 'ERROR ' . $dateTime, $pathToLog);
    return false;
}

// ищем внешний код для розницы по id склада
$ob = \CCatalogStore::GetList(
    [],
    ['ACTIVE' => 'Y', 'ID' => $storeId],
    false,
    false,
    ["ID", "UF_RETAIL_ID"]
);
$retailId = '';
if ($res = $ob->GetNext()) {
    $retailId = $res["~UF_RETAIL_ID"];
}

if (!$retailId) {
    Debug::writeToFile('Не найден внешний код розницы для склада с ID=' . $storeId, 'ERROR ' . $dateTime, $pathToLog);
    return false;
}

try {
    $itemRemain = Paolo::GetReserveItemRemain($retailId, $artNum, $size);
    if ($size == 'не назначена') {
        $size = "Без размера";
    }

    if ((int)$itemRemain > 0) { // товар есть в наличии
        $success = true;
        $msg = "Товар в наличии: ID склада:" . $storeId . ", Retail ID:" . $retailId . ", Артикул товара:" . $artNum . ", Размер:" . $size;
        Debug::writeToFile($msg, 'SUCCESS ' . $dateTime, $pathToLog);
    } else { // товара нет
        $msg = "Товар отсутствует: ID склада:" . $storeId . ", Retail ID:" . $retailId . ", Артикул товара:" . $artNum . ", Размер:" . $size;
        Debug::writeToFile($msg, 'ERROR ' . $dateTime, $pathToLog);
        $err = [
            "title" => 'Товар только что продан',
            "msg" => 'К сожалению, данный товар в этом магазине только что продан. Попробуйте выбрать другой магазин.'
        ];
    }
} catch (\Exception $e) {
    $err = [
        "title" => 'Ошибка выполнения запроса',
        "msg" => $e->getMessage()
    ];
    Debug::writeToFile($err, 'ERROR ' . $dateTime, $pathToLog);
}

if (!$success && empty($err)) {
    $err = [
        "title" => 'Ошибка выполнения запроса',
        "msg" => 'Произошла ошибка при выполнении запроса. Пожалуйста, попробуйте позже.'
    ];
}

$arResult['success'] = $success;
$arResult['error'] = $err;

echo json_encode($arResult);
