<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;
$rsUser = CUser::GetByID($USER->GetID());
$arUser = $rsUser->Fetch();
$userFullName = $arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME'];

$resObj = CIBlockElement::GetList(Array(), Array("ID" => htmlspecialchars($_GET['ELEMENT_ID']), "ACTIVE" => "Y"), false, Array(), Array("ID", "NAME"));
while ($ob = $resObj->GetNext()) {
        $arName = $ob['NAME'];
}

$APPLICATION->IncludeComponent("citfact:form.ajax", "subscribe_size", Array(
    "IBLOCK_ID" => 52,
    "SHOW_PROPERTIES" => array(
        "TOVAR_ID" => array(
            "type" => "hidden",
            "required" => "Y",
            "value" => htmlspecialchars($_GET['ELEMENT_ID'])
        ),
        "TOVAR_NAME" => array(
            "type" => "hidden",
            "required" => "Y",
            "value" => $arName,
        ),
        "CITY_ID" => array(
            "type" => "hidden",
            "required" => "Y",
            "value" => $_SESSION['CITY_CODE']
        ),
        "USER_ID" => array(
            "type" => "hidden",
            "value" => $USER->GetID()
        ),
        "USER_FULL_NAME" => array(
            "type" => "hidden",
            "value" => $userFullName
        ),
        "SIZE" => array(
            "type" => "radio",
            "placeholder" => "",
            "required" => "Y",
            "value" => '',
            "name" => 'Размер',
        ),
        "EMAIL" => array(
            "type" => "text",
            "placeholder" => "Получить уведомление письмом",
            "required" => "Y",
            "value" => '',
            "name" => 'Email',
        ),
        "PHONE_NUMBER" => array(
            "type" => "text",
            "placeholder" => "Получить уведомление звонком",
            "required" => "Y",
            "value" => '',
            "name" => 'Телефон',
        ),
        "COLOR" => array(
            "type" => "color",
            "placeholder" => "",
            "required" => "Y",
            "value" => '',
            "name" => 'Цвет',
        ),
        "SUBSCRIBE" => array(
            "type" => "checkbox",
            "value" => 'Y',
        ),
    ),
    "EVENT_NAME" => "SUBSCRIBE_NO_SIZE",
    "SUCCESS_MESSAGE" => "Спасибо за заявку, размер объявлен в розыск.",
    "ELEMENT_ACTIVE" => "Y",
    "CHECK_EQUAL_PROPS" => array('EMAIL', 'TOVAR_ID'),
    "COLOR_ID" => htmlspecialchars($_GET['COLOR_ID'])
),
    false
);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>