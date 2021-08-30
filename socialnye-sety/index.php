<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Сеть магазинов модной обуви и аксессуаров Paolo Conte в социальных сетях. Подписывайтесь!");
$APPLICATION->SetTitle("Социальные сети");
?><div class="b-social"><?$APPLICATION->IncludeComponent(
    "citfact:page.static",
    "",
    Array(
        "COMPONENT_TEMPLATE" => ".default",
        "IBLOCK_TYPE" => "info",
        "IBLOCK_ID" => "21",
        "USE_CODE" => "Y",
        "PAGE_ID" => ""
    )
);?></div><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>