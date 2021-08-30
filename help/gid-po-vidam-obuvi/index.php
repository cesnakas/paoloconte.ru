<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "виды обуви, гид по обуви");
$APPLICATION->SetPageProperty("description", "Интернет-магазин обуви и аксессуаров Paolo Conte: путеводитель по видам обуви.");
$APPLICATION->SetTitle("Гид по видам обуви");
?><?$APPLICATION->IncludeComponent(
	"citfact:page.static",
	"",
	Array(
		"COMPONENT_TEMPLATE" => ".default",
		"IBLOCK_TYPE" => "info",
		"IBLOCK_ID" => "21",
		"USE_CODE" => "Y",
		"PAGE_ID" => ""
	)
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>