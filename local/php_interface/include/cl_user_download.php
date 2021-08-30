<?
if (empty($_SERVER['DOCUMENT_ROOT'])){
    $_SERVER['DOCUMENT_ROOT'] = str_replace('/local/php_interface/include', '', __DIR__);
}
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');


//Выгрузка в csv файл пользователей

use Bitrix\Main\Loader;
Loader::includeModule('sale');

//---Получаем список пользователей
global $USER;
$arUsers = array(
    "HEADER" => array(
        "ID",
        "E-mail",
        "Фамилия",
        "Имя",
        "Отчество",
        "Телефон",
        "Карта",
        "Дата регистрации",
        "Дата рождения",
        "Количество покупок",
        "Сумма покупок",
    )
);
$arUserIds = array();

$filter = Array(
    "ACTIVE" => "Y",
);

$arParameters = array(
    'SELECT' => array(
        "ID",
        "UF_LOYALTY_CARD"
    ) ,
);

$rsUsers = CUser::GetList(($by = "ID") , ($order = "asc") , $filter, $arParameters); // выбираем пользователей
while ($arUser = $rsUsers->Fetch())
{
    $arUsers[$arUser["ID"]] = array(
        "ID" => $arUser["ID"],
        "EMAIL" => trim($arUser["EMAIL"]),        
        "LAST_NAME" => trim($arUser["LAST_NAME"]),
        "NAME" => trim($arUser["NAME"]),        
        "SECOND_NAME" => trim($arUser["SECOND_NAME"]),
        "PERSONAL_PHONE" => trim($arUser["PERSONAL_PHONE"]),
        "CARD" => "'".trim($arUser["UF_LOYALTY_CARD"]),
        "DATE_REGISTER" => $arUser["DATE_REGISTER"],
        "PERSONAL_BIRTHDAY" => $arUser["PERSONAL_BIRTHDAY"],
        "COUNT_FULL_PAID_ORDER" => "0",
        "SUM_PAID" => "0",
    );
        
    $arUserIds[] = $arUser["ID"];
}

//---Сопоставляем пользователей с покупателями
$arFilter = array(
    "USER_ID" => $arUserIds
);

$buyersFilter = [];
$buyersFilter['filter'] = $arFilter;
$buyersFilter['select'] = array(
    'USER_ID',
    'COUNT_FULL_PAID_ORDER',
    'SUM_PAID',
);

$buyersFilter['order'] = array(
    "USER_ID" => "ASC"
);

$buyersData = \Bitrix\Sale\BuyerStatistic::getList($buyersFilter);

$arBuyers = array();

while ($buyer = $buyersData->fetch())
{
    $arUsers[$buyer["USER_ID"]]["COUNT_FULL_PAID_ORDER"] = intval($buyer["COUNT_FULL_PAID_ORDER"]);
    $arUsers[$buyer["USER_ID"]]["SUM_PAID"] = round($buyer["SUM_PAID"], 2);
}

unlink('users.csv');
$fp = fopen('users.csv', 'w');

foreach ($arUsers as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);


?>
