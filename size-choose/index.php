<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Выбор размера обуви в интернет-магазине Paolo Conte.");
$APPLICATION->SetPageProperty("keywords", "женская обувь, мужская обувь, размер, cапоги");
$APPLICATION->SetTitle("Выбор размера");
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