<?
define('NEED_AUTH', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Моя скидка");
?>
<?
$APPLICATION->IncludeComponent(
	"citfact:loyalty_card.page",
	"",
	Array(
		"COMPONENT_TEMPLATE" => ".default"
	)
);
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>