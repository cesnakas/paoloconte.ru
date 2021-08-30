<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Карта   сайта интернет-магазина по продаже обуви и аксессуаров «Paolo Conte»");
$APPLICATION->SetPageProperty("tags", "карта сайта, разделы, категории товаров");
$APPLICATION->SetPageProperty("keywords", "карта сайта, категории товаров, разделы, купить обувь, купить аксессуары, интернет-магазин");
$APPLICATION->SetPageProperty("description", "Карта сайта интернет-магазина по   продаже обуви и аксессуаров с доставкой по Москве и всей России «Paolo Conte»");
$APPLICATION->SetTitle("Карта сайта");
?>
<?$APPLICATION->IncludeComponent(
    "bitrix:catalog.section.list",
    "sitemap",
    array(
        "ADD_SECTIONS_CHAIN" => "N",
        "CACHE_GROUPS" => "N",
        "CACHE_TIME" => "36000000",
        "CACHE_TYPE" => "A",
        "COUNT_ELEMENTS" => "N",
        "IBLOCK_ID" => IBLOCK_CATALOG,
        "IBLOCK_TYPE" => "catalog",
        "SECTION_CODE" => "",
        "SECTION_FIELDS" => array(
            0 => "",
            1 => "",
        ),
        "SECTION_ID" => $_REQUEST["SECTION_ID"],
        "SECTION_URL" => "",
        "SECTION_USER_FIELDS" => array(
            0 => "",
            1 => "",
        ),
        "SHOW_PARENT_NAME" => "Y",
        "TOP_DEPTH" => "3",
        "VIEW_MODE" => "LIST",
        "COMPONENT_TEMPLATE" => ".default",
        "HIDE_SECTION_NAME" => "N",
        "EXCLUDE_SECTION_CODES" => [
            'test',
            'pol-ne-opredelen',
            'sapogi-m-sale',
            'gruppa-ne-opredelena_1',
            'gruppa-ne-opredelena_2',
            'snikersy',
            'timberlendy',
            'uggi_1',
            'uggi',
            'gruppa-ne-opredelena_3',
            'gruppa-ne-opredelena',
            'remen',
            'promo-new-collection',
            'soputstvuyushchie-tovary_1',
            'soputstvuyushchie-tovary',
            'braslety',
            'klatchi_1',
            'ryukzaki',
            'lofery-m',
            'slipery-m',
            'vizitnitsy',
            'noski',
            'oblozhki',
            'klyuchnitsy',
            'perchatki-3',
            'sumka',
            'portmone',
            'zazhim',
        ],
    ),
    false
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>