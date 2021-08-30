<?php
include($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Diag\Debug,
    Bitrix\Main\Application,
    Bitrix\Main\Mail\Event,
    Bitrix\Main\Loader,
    \Citfact\Paolo;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    return false;
}

if (!Loader::includeModule("catalog")) {
    Debug::writeToFile('не удалось подключить модуль catalog', 'ERROR ' . $dateTime, $pathToLog);
    return false;
}

if (!Loader::includeModule("iblock")) {
    Debug::writeToFile('не удалось подключить модуль iblock', 'ERROR ' . $dateTime, $pathToLog);
    return false;
}

$pathToLog = '/local/var/logs/reserve.log';
$request = Application::getInstance()->getContext()->getRequest()->toArray();

$arFormdata = [];
parse_str($request['formdata'], $arFormdata);

if ($arFormdata['yarobot'] != '' || !(check_bitrix_sessid())) {
    Debug::writeToFile('Не прошла проверка на заполнение формы роботом', 'ERROR ' . $dateTime, $pathToLog);
    return false;
}

$name = trim(strip_tags($arFormdata["form_reserve_name"]));
$phone = trim(strip_tags($arFormdata["form_reserve_phone"]));
$productId = (int)trim(strip_tags($arFormdata["form_reserve_product_id"]));

$storeId = (int)trim(strip_tags($request["storeId"]));
$shopId = (int)trim(strip_tags($request["shopId"]));
$shopName = trim(strip_tags($request["shopName"]));
$productName = trim(strip_tags($request["productName"]));
$artNum = trim(strip_tags($request["artNum"]));
$size = ((int)trim(strip_tags($request["size"]))) ?: 'не назначена';

$dateTime = date('d-m-Y H:i:s');
$success = false;
$msg = "";
$arResult = $err = [];

if (!$storeId || !$artNum || !$name || !$phone) {
    Debug::writeToFile('Не правильно переданы данные из формы', 'ERROR ' . $dateTime, $pathToLog);
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
    $reserve = Paolo::SetReserve($retailId, $name, $phone, $artNum, $size);
    if ($size == 'не назначена') {
        $size = "Без размера";
    }

    if ($reserve) { // товар зарезервирован
        $success = true;
        $msg = "Товар зарезирвирован: ID склада:" . $storeId .
            ", Retail ID:" . $retailId .
            ", Артикул товара:" . $artNum .
            ", Размер:" . $size .
            ", Имя покупателя:" . $name .
            ", Телефон покупателя:" . $phone .
            ", Сообщение от сервиса:" . $reserve;
        Debug::writeToFile($msg, 'SUCCESS ' . $dateTime, $pathToLog);

        // записать данные в инфблок
        $el = new \CIBlockElement;
        $arProp = [
            "NAME" => $name,
            "PHONE" => $phone,
            "PRODUCT_NAME" => $productName,
            "SHOP_NAME" => $shopName,
            "SIZE" => $size,
            "PRODUCT_ID" => $productId,
            "ART_NUM" => $artNum,
            "SHOP_ID" => $shopId,
            "STORE_ID" => $storeId,
            "RETAIL_ID" => $retailId,
            "SERVICE_MSG" => $reserve
        ];
        $arLoadProduct = [
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => IBLOCK_RESERVE,
            "PROPERTY_VALUES" => $arProp,
            "NAME" => "Запрос на бронирование",
            "ACTIVE" => "Y",
        ];
        if ($elId = $el->Add($arLoadProduct)) {
            Debug::writeToFile('Успешная запись в инфоблок. ID записанного элемента:' . $elId, 'SUCCESS ' . $dateTime, $pathToLog);

            // отправить email
            $obResult = Event::send([
                "EVENT_NAME" => "RESERVE_FORM",
                "LID" => "s1",
                "C_FIELDS" => [
                    "NAME" => $name,
                    "PHONE" => $phone,
                    "PRODUCT_NAME" => $productName,
                    "SHOP_NAME" => $shopName,
                    "SIZE" => $size,
                    "PRODUCT_ID" => $productId,
                    "ART_NUM" => $artNum,
                    "SHOP_ID" => $shopId,
                    "STORE_ID" => $storeId,
                    "RETAIL_ID" => $retailId,
                    "SERVICE_MSG" => $reserve,
                ],
            ]);

            if ($obResult->isSuccess()) {
                Debug::writeToFile('Email успешно отправлен', 'SUCCESS ' . $dateTime, $pathToLog);
            } else {
                Debug::writeToFile('Ошибка отправки email', 'ERROR ' . $dateTime, $pathToLog);
            }
        } else { // не удалось записать в инфоблок
            Debug::writeToFile('Ошибка записи в инфоблок. Текст ошибки:' . $el->LAST_ERROR, 'ERROR ' . $dateTime, $pathToLog);
        }
    } else { // не удалось зарезервировать товар
        $msg = "Ошибка резервирования товара: ID склада:" . $storeId .
            ", Retail ID:" . $retailId .
            ", Артикул товара:" . $artNum .
            ", Размер:" . $size .
            ", Имя покупателя:" . $name .
            ", Телефон покупателя:" . $phone;
        Debug::writeToFile($msg, 'ERROR ' . $dateTime, $pathToLog);
        $err = [
            "title" => 'Ошибка резервирования товара',
            "msg" => 'К сожалению, произошла ошибка при резервировании товара. Пожалуйста, Попробуйте позже.'
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
