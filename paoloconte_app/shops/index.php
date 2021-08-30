<?require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Магазины Paolo Conte");
?><?$APPLICATION->IncludeComponent(
	"citfact:shops.page",
	"page_app",
	Array(
		//"IBLOCK_ID" => 16,
		"CITY_CODE" => $_REQUEST['CODE'],
		"DIRECTORY_CODE" => 'shops',
		"ONE_SHOP_REDIRECT" => 'N'
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>