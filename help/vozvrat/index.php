<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Как оформить возврат в интернет-магазине Paolo Conte.");
$APPLICATION->SetTitle("Возврат товара");
?>
<?$APPLICATION->IncludeComponent(
	"citfact:page.static",
	"",
	Array(
		"IBLOCK_TYPE" => "info",
		"IBLOCK_ID" => 21,
		"USE_CODE" => "Y",
		"PAGE_ID" => ""
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>