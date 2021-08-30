<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "размер");
$APPLICATION->SetPageProperty("description", "Как выбрать свой размер обуви в интернет-магазине Paolo Conte: таблица размеров, легкий способ определить свой размер.");
$APPLICATION->SetTitle("Выбор размера");
?><?$APPLICATION->IncludeComponent(
	"citfact:page.static",
	"",
	Array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_ID" => "21",
		"IBLOCK_TYPE" => "info",
		"PAGE_ID" => "",
		"USE_CODE" => "Y"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>