<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Правила оформления заказа в интернет-магазине обуви и аксессуаров Paolo Conte.");
$APPLICATION->SetPageProperty("title", "Как оформить заказ :: Интернет-магазин обуви Paolo Conte");
$APPLICATION->SetPageProperty("tags", "заказ,оформление,интернет-магазин");
$APPLICATION->SetTitle("Как оформить заказ");
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
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>