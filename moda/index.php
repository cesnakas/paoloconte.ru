<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Модные тенденции");
?><?$APPLICATION->IncludeComponent(
	"citfact:page.static",
	"",
	Array(
		"IBLOCK_TYPE" => "info",
		"IBLOCK_ID" => "",
		"USE_CODE" => "Y",
		"PAGE_ID" => ""
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>