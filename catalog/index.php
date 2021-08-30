<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use Citfact\Tools;

$APPLICATION->SetPageProperty("title", "Каталог женской и мужской обуви, аксессуаров интернет-магазина «Paolo Conte»");
$APPLICATION->SetPageProperty("description", "Широкий ассортимент мужской и женской обуви, аксессуаров от производителя в Москве. Бесплатная доставка с примеркой по России в интернет-магазине «Paolo Conte». Есть пункты самовывоза.");
$APPLICATION->SetTitle("Paolo Conte :: магазин обуви и аксессуаров");

//переменная $catalogMode задается в header.php шаблона и имеет всего два положения для элементов - ELEMENT, для раздела SECTION
global $catalogMode;
if ($catalogMode == 'ELEMENT') {
    include_once('detail.php');
} else {
    // Проверяем наличие такого раздела && (текущий url == полный адрес радела || текущий url == короткий адрес /catalog/РАЗДЕЛ/ )
    $arFilter = Array("IBLOCK_ID" => IBLOCK_CATALOG, "CODE" => $_REQUEST["CATALOG_CODE"], "ACTIVE_DATE" => "Y", "GLOBAL_ACTIVE" => "Y");
    $res = CIBlockSection::GetList(Array(), $arFilter, false, Array("nPageSize" => 1), Array("ID", "IBLOCK_ID", "SECTION_PAGE_URL", "CODE"));
    if (($ob = $res->GetNextElement(false, false)) && (
            $APPLICATION->GetCurDir() == $ob->fields["SECTION_PAGE_URL"]
            || $APPLICATION->GetCurDir() == '/catalog/' . $ob->fields['CODE'] . '/'
            || $APPLICATION->GetCurDir() == '/catalog/'
        )) {
        include_once('section.php');
        ?>
        <div class="container">
            <?
//                    include_once('bigdata.php');
            ?>
        </div>
        <?php
    } else {
        CHTTP::SetStatus('404 Not Found');
        $APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");
        $APPLICATION->IncludeFile(
            SITE_DIR . "include/not_found_catalog.php",
            Array(),
            Array("MODE" => "text")
        );
        $filter = Array("IBLOCK_ID" => IBLOCK_CATALOG,"CODE"=>$_REQUEST['CATALOG_CODE']);
        $result = CIBlockElement::GetList(Array(), $filter, false, false, ['IBLOCK_SECTION_ID', 'ID']);
        if($rsSect=$result->Fetch()){
            $section = $rsSect['IBLOCK_SECTION_ID'];
        };
        ?>
        <div class="container">
            <div class="product" itemscope itemtype="http://schema.org/Product">
                <?
                global $arrFilter, $arrFilterBigData;
                if(!empty($arrFilter)){
                    $arrFilterBigData = array_merge($arrFilter, Tools::getFilterForBigData());
                } else {
                    $arrFilterBigData =  Tools::getFilterForBigData();
                }

                $APPLICATION->IncludeComponent(
                    "bitrix:catalog.section",
                    "bootstrap_v4_paolo",
                    array(
                        "ACTION_VARIABLE" => "action",
                        "ADD_PICT_PROP" => "-",
                        "ADD_PROPERTIES_TO_BASKET" => "Y",
                        "ADD_SECTIONS_CHAIN" => "N",
                        "ADD_TO_BASKET_ACTION" => "ADD",
                        "AJAX_MODE" => "N",
                        "AJAX_OPTION_ADDITIONAL" => "",
                        "AJAX_OPTION_HISTORY" => "N",
                        "AJAX_OPTION_JUMP" => "N",
                        "AJAX_OPTION_STYLE" => "Y",
                        "BACKGROUND_IMAGE" => "-",
                        "BASKET_URL" => BASKET_URL,
                        "BROWSER_TITLE" => "-",
                        "CACHE_FILTER" => "N",
                        "CACHE_GROUPS" => "Y",
                        "CACHE_TIME" => "0",
                        "CACHE_TYPE" => "N",
                        "COMPATIBLE_MODE" => "Y",
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO",
                        "CONVERT_CURRENCY" => "N",
                        "CUSTOM_FILTER" => "{\"CLASS_ID\":\"CondGroup\",\"DATA\":{\"All\":\"AND\",\"True\":\"True\"},\"CHILDREN\":[]}",
                        "DETAIL_URL" => "",
                        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
                        "DISPLAY_BOTTOM_PAGER" => "N",
                        "DISPLAY_COMPARE" => "N",
                        "DISPLAY_TOP_PAGER" => "N",
                        "ENLARGE_PRODUCT" => "STRICT",
                        "FILTER_NAME" => "arrFilterBigData",
                        "HIDE_NOT_AVAILABLE" => "Y",
                        "HIDE_NOT_AVAILABLE_OFFERS" => "N",
                        "IBLOCK_ID" => "10",
                        "IBLOCK_TYPE" => "catalog",
                        "INCLUDE_SUBSECTIONS" => "Y",
                        "LABEL_PROP" => array(
                        ),
                        "LAZY_LOAD" => "N",
                        "LINE_ELEMENT_COUNT" => "3",
                        "LOAD_ON_SCROLL" => "N",
                        "MESSAGE_404" => "",
                        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                        "MESS_BTN_BUY" => "Купить",
                        "MESS_BTN_DETAIL" => "Подробнее",
                        "MESS_BTN_SUBSCRIBE" => "Подписаться",
                        "MESS_NOT_AVAILABLE" => "Нет в наличии",
                        "META_DESCRIPTION" => "-",
                        "META_KEYWORDS" => "-",
                        "OFFERS_CART_PROPERTIES" => array(
                            "RAZMER",
                        ),
                        "OFFERS_FIELD_CODE" => array(
                            0 => "",
                            1 => "",
                        ),
                        "OFFERS_LIMIT" => "5",
                        "OFFERS_PROPERTY_CODE" => array(
                            "RAZMER",
                            "HIT",
                            "NEW",
                        ),
                        "ELEMENT_SORT_FIELD" => $sort,
                        "ELEMENT_SORT_ORDER" => $order,
                        "ELEMENT_SORT_FIELD2" => $sort2,
                        "ELEMENT_SORT_ORDER2" => $order2,
                        "PAGER_BASE_LINK_ENABLE" => "N",
                        "PAGER_DESC_NUMBERING" => "N",
                        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                        "PAGER_SHOW_ALL" => "N",
                        "PAGER_SHOW_ALWAYS" => "N",
                        "PAGER_TEMPLATE" => ".default",
                        "PAGER_TITLE" => "Товары",
                        "PAGE_ELEMENT_COUNT" => "0",
                        "PARTIAL_PRODUCT_PROPERTIES" => "N",
                        "PRICE_CODE" => $_SESSION['GEO_PRICES']['TO_PARAMS'],
                        "PRICE_VAT_INCLUDE" => "Y",
                        "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons",
                        "PRODUCT_DISPLAY_MODE" => "Y",
                        "PRODUCT_ID_VARIABLE" => "id",
                        "PRODUCT_PROPERTIES" => array(
                            "HIT",
                            "NEW",
                            "NAIMENOVANIE_MARKETING",
                            "CML2_ARTICLE",
                            "TSVET_DLYA_FILTRA",
                        ),
                        "PRODUCT_PROPS_VARIABLE" => "prop",
                        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                        "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':true}]",
                        "PRODUCT_SUBSCRIPTION" => "N",
                        "PROPERTY_CODE" => array(
                            0 => "",
                            1 => "",
                        ),
                        "PROPERTY_CODE_MOBILE" => array(
                        ),
                        "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
                        "RCM_TYPE" => "personal",
                        "SECTION_ID" => $section,
                        "SECTION_ID_VARIABLE" => "SECTION_ID",
                        "SECTION_URL" => $SECTION_CODE_PATH,
                        "SECTION_USER_FIELDS" => array(
                            0 => "",
                            1 => "",
                        ),
                        "SEF_MODE" => "N",
                        "SET_BROWSER_TITLE" => "N",
                        "SET_LAST_MODIFIED" => "N",
                        "SET_META_DESCRIPTION" => "N",
                        "SET_META_KEYWORDS" => "N",
                        "SET_STATUS_404" => "N",
                        "SET_TITLE" => "N",
                        "SHOW_404" => "N",
                        "SHOW_ALL_WO_SECTION" => "N",
                        "SHOW_CLOSE_POPUP" => "Y",
                        "SHOW_DISCOUNT_PERCENT" => "Y",
                        "SHOW_FROM_SECTION" => "Y",
                        "SHOW_MAX_QUANTITY" => "N",
                        "SHOW_OLD_PRICE" => "Y",
                        "SHOW_PRICE_COUNT" => "1",
                        "SHOW_SLIDER" => "Y",
                        "SLIDER_INTERVAL" => "3000",
                        "SLIDER_PROGRESS" => "N",
                        "TEMPLATE_THEME" => "blue",
                        "USE_ENHANCED_ECOMMERCE" => "N",
                        "USE_MAIN_ELEMENT_SECTION" => "N",
                        "USE_PRICE_COUNT" => "N",
                        "USE_PRODUCT_QUANTITY" => "N",
                        "COMPONENT_TEMPLATE" => "bootstrap_v4_paolo",
                        "OFFER_ADD_PICT_PROP" => "-",
                        "OFFER_TREE_PROPS" => array(
                        ),
                        "LABEL_PROP_MOBILE" => "",
                        "LABEL_PROP_POSITION" => "top-left",
                        "DISCOUNT_PERCENT_POSITION" => "bottom-right",
                        'HIDE_SECTION_DESCRIPTION' => 'Y',
                    ),
                    false
                );?>
            </div>
        </div>

       <?php include($_SERVER['DOCUMENT_ROOT'] . SITE_DIR . "include/viewRRSection.php");
    }
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");