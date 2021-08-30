<?require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "магазин, обувь Paolo Conte,  магазин Paolo Conte,  адрес магазина");
$APPLICATION->SetPageProperty("keywords", "женская обувь, мужская обувь, магазин, аксессуары, города");
$APPLICATION->SetPageProperty("description", "Купить обувь и аксессуары Paolo Conte: адреса и контакты магазинов");
$APPLICATION->SetTitle("Магазины");
?>
<? $APPLICATION->AddHeadScript('//api-maps.yandex.ru/2.1/?apikey=' .
	\COption::GetOptionString("main", "map_yandex_keys") . '&lang=ru_RU'); ?>
<?$APPLICATION->IncludeComponent(
	"citfact:shops.detail",
	"",
	Array(
		"SHOP_ID" => $_REQUEST['SHOP_ID'],
		"SHOP_CODE" => $_REQUEST['SHOP_CODE'],
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>