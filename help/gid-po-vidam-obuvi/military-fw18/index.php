<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Капсульная коллекция Military");
?><?$APPLICATION->IncludeComponent(
	"citfact:page.static",
	"",
	Array(
		"IBLOCK_ID" => "21",
		"IBLOCK_TYPE" => "info",
		"PAGE_ID" => "",
		"USE_CODE" => "Y"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>