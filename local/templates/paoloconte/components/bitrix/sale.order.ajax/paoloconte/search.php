<?
define("STOP_STATISTICS", true);
define("PUBLIC_AJAX_MODE", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json; charset=' . LANG_CHARSET);

IncludeModuleLangFile(__FILE__);

$arResult = array(
    "NAME" => '',
);

if (\Bitrix\Main\Loader::includeModule('sale')) {
	if (!empty($_REQUEST["city"])) {
		$city = $APPLICATION->UnJSEscape($_REQUEST["city"]);

        $rsLocationsList = CSaleLocation::getList(
            array(),
            array("CITY_ID" => $city, "LID" => LANGUAGE_ID),
            false,
            false,
            array("ID", "CITY_ID", "CITY_NAME")
        );
        if ($arCity = $rsLocationsList->GetNext()) {
            $arResult = array(
                "NAME" => $arCity["CITY_NAME"],
            );
        }
	}
}

echo json_encode($arResult);
