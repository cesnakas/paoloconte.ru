<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Заказ, оплата и доставка в интернет-магазине Paolo Conte.");
$APPLICATION->SetPageProperty("description", "Cделать заказ в интернет-магазине Paolo Conte. Оплата и доставка.");
$APPLICATION->SetTitle("Часто задаваемые вопросы");
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