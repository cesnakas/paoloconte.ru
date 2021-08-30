<?require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "доставка, оплата заказа, возврат товара, способы доставки");
$APPLICATION->SetPageProperty("description", "Оплата и доставка заказа в интернет-магазине Paolo Conte");
$APPLICATION->SetTitle("Оплата и доставка");
?>

<?$APPLICATION->IncludeComponent(
	"citfact:page.static",
	"app_static",
	Array(
		"COMPONENT_TEMPLATE" => ".app_static",
		"IBLOCK_TYPE" => "info",
		"IBLOCK_ID" => "21",
		"USE_CODE" => "Y",
		"PAGE_ID" => "",
	)
);?>

<script>
	app.setPageTitle({"title" : "Оплата и доставка"});
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>