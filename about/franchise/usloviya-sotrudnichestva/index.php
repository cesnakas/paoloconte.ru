<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Магазин обуви по франшизе Paolo Conte.");
$APPLICATION->SetPageProperty("description", "Магазин обуви по франшизе: условия франчайзинга Paolo Conte.");
$APPLICATION->SetTitle("Условия сотрудничества франчайзи с Paolo Conte");?>

<?$APPLICATION->IncludeComponent(
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