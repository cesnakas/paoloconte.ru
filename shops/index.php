<? require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Сеть магазинов обуви и аксессуаров Paolo Conte: список городов России и СНГ, карта, адреса, контакты.");
$APPLICATION->SetTitle("Магазины Paolo Conte");
?>
<? $APPLICATION->AddHeadScript("//api-maps.yandex.ru/2.1AddHeadScript/?lang=ru_RU"); ?>
<? $APPLICATION->IncludeComponent(
    "citfact:shops.page",
    "page",
    Array(
        //"IBLOCK_ID" => 16,
        "CITY_CODE" => $_SESSION['CITY_CODE'],
        "DIRECTORY_CODE" => 'shops'
    )
); ?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>