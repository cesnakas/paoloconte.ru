<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "скидка, дисконт");
$APPLICATION->SetPageProperty("description", "Клубная карта Paolo Conte: скидки на обувь и аксессуары для постоянных покупателей.");
$APPLICATION->SetTitle(" Дисконтная система Paolo Conte");
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