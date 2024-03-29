<?$arPrices = \Citfact\Paolo::GetRegionPriceTypes($_SESSION['CITY_ID']);?>
<?
$arFilter = array(
    "IBLOCK_ID"=>IBLOCK_CATALOG,
    "IBLOCK_ACTIVE"=>"Y",
    "ACTIVE"=>"Y",
    "GLOBAL_ACTIVE"=>"Y",
);
$page = explode("/", $_REQUEST['SECTION_PATH']);
if($page[0]=='rasprodazha'){
    $findFilter = array(
        "IBLOCK_ID" => IBLOCK_CATALOG,
        "IBLOCK_LID" => SITE_ID,
        "IBLOCK_ACTIVE" => "Y",
        "ACTIVE_DATE" => "Y",
        "CHECK_PERMISSIONS" => "Y",
        "MIN_PERMISSION" => 'R',
    );
    $ELEMENT_ID = CIBlockFindTools::GetElementID(
        '',
        $_REQUEST["CATALOG_CODE"],
        false,
        false,
        $findFilter
    );
    $db_old_groups = CIBlockElement::GetElementGroups($ELEMENT_ID);
    $idSection = '';
    while($obElement = $db_old_groups->GetNext()) {
        $idSection = '';
        $arFilter["ID"]=$obElement['ID'];
        $rsSection = CIBlockSection::GetList(array(), $arFilter, false, $arSelect);
        $rsSection->SetUrlTemplates("", '/catalog/#SECTION_CODE#/');
        while($arPath = $rsSection->GetNext()){
            $arPath['arPage'] = explode("/", $arPath['SECTION_PAGE_URL']);
            if($arPath['arPage'][2] == $page[0])
                $idSection = $arPath['ID'];
        }
    }
    $_REQUEST["SECTION_ID"] = $idSection;
}
?>
<? $APPLICATION->IncludeComponent(
    "bitrix:catalog.element",
    "paoloconte",
    array(
        "IBLOCK_TYPE" => IBLOCK_CATALOG_TYPE,
        "IBLOCK_ID" => IBLOCK_CATALOG,
        "ELEMENT_ID" => $_REQUEST["ELEMENT_ID"],
        "ELEMENT_CODE" => $_REQUEST["CATALOG_CODE"],
        "SECTION_ID" => $_REQUEST["SECTION_ID"],
        "SECTION_CODE" => $_REQUEST["CATALOG_CODE"],
        "HIDE_NOT_AVAILABLE" => "N",
        "PROPERTY_CODE" => array(
            0 => "MATERIAL_VERKHA_MARKETING",
            1 => "MATERIAL_PODKLADKI_MARKETING",
            3 => "CML2_MANUFACTURER",
            4 => "SEZONNOST",
            5 => "STIL",
            6 => "TREND",
            7 => "TSVET_MARKETING",
            8 => "VYSOTA_KABLUKA",
            9 => "VYSOTA_GOLENISHCHA_PROIZVODSTVO",
            //10 => "HIT",
            //11 => "NEW",
            //12 => "SALE",
            13 => "CML2_ARTICLE",
        ),
        "OFFERS_LIMIT" => "0",
        "TEMPLATE_THEME" => "blue",
        "DISPLAY_NAME" => "Y",
        "DETAIL_PICTURE_MODE" => "IMG",
        "ADD_DETAIL_TO_SLIDER" => "N",
        "DISPLAY_PREVIEW_TEXT_MODE" => "E",
        "PRODUCT_SUBSCRIPTION" => "N",
        "SHOW_DISCOUNT_PERCENT" => "Y",
        "SHOW_OLD_PRICE" => "Y",
        "SHOW_MAX_QUANTITY" => "N",
        "SHOW_CLOSE_POPUP" => "N",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        "USE_VOTE_RATING" => "Y",
        "USE_COMMENTS" => "Y",
        "BRAND_USE" => "N",
        "SECTION_URL" => "",
        "DETAIL_URL" => "",
        "SECTION_ID_VARIABLE" => "SECTION_CODE",
        "CHECK_SECTION_ID_VARIABLE" => "N",
        "CACHE_TYPE" => "N",
        "CACHE_TIME" => "36000000",
        "CACHE_GROUPS" => "Y",
        "SET_TITLE" => "Y",
        "SET_BROWSER_TITLE" => "Y",
        "BROWSER_TITLE" => "-",
        "SET_META_KEYWORDS" => "Y",
        "META_KEYWORDS" => "-",
        "SET_META_DESCRIPTION" => "Y",
        "META_DESCRIPTION" => "-",
        "SET_STATUS_404" => "Y",
        "ADD_SECTIONS_CHAIN" => "N",
        "ADD_ELEMENT_CHAIN" => "Y",
        "USE_ELEMENT_COUNTER" => "Y",
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id",
        "DISPLAY_COMPARE" => "N",
		"PRICE_CODE" => $_SESSION['GEO_PRICES']['TO_PARAMS'],
        "USE_PRICE_COUNT" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "PRICE_VAT_INCLUDE" => "Y",
        "PRICE_VAT_SHOW_VALUE" => "N",
        "CONVERT_CURRENCY" => "N",
        "BASKET_URL" => BASKET_URL,
        "USE_PRODUCT_QUANTITY" => "N",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        "PRODUCT_PROPERTIES" => array(
        ),
        "ADD_TO_BASKET_ACTION" => array(
            0 => "BUY",
        ),
        "LINK_IBLOCK_TYPE" => "",
        "LINK_IBLOCK_ID" => "",
        "LINK_PROPERTY_SID" => "",
        "LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
        "OFFERS_FIELD_CODE" => array(
            0 => "",
            1 => "",
        ),
        "OFFERS_PROPERTY_CODE" => array(
            0 => "RAZMER",
            1 => "",
        ),
        "OFFERS_SORT_FIELD" => "sort",
        "OFFERS_SORT_ORDER" => "asc",
        "OFFERS_SORT_FIELD2" => "id",
        "OFFERS_SORT_ORDER2" => "asc",
        "ADD_PICT_PROP" => "-",
        "LABEL_PROP" => "-",
        "OFFER_ADD_PICT_PROP" => "-",
        "OFFER_TREE_PROPS" => array(
            0 => "RAZMER",
        ),
        "MESS_BTN_COMPARE" => "Сравнить",
        "OFFERS_CART_PROPERTIES" => array(
            0 => "RAZMER",
        ),
        "VOTE_DISPLAY_AS_RATING" => "rating",
        "BLOG_USE" => "N",
        "VK_USE" => "N",
        "FB_USE" => "N",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
		'SET_CANONICAL_URL' => 'Y',
		'USER_CITY_ID' => $_SESSION['CITY_ID'],
    ),
    false
);?>