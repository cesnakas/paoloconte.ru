<? define('STOP_STATISTICS', true);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
//$GLOBALS['APPLICATION']->RestartBuffer();
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.element",
	"paoloconte_fastview",
	array(
		"IBLOCK_TYPE" => 'catalog',
		"IBLOCK_ID" => 10,
		"ELEMENT_ID" => $_REQUEST["ELEMENT_ID"],
//		"ELEMENT_CODE" => $_REQUEST["CATALOG_CODE"],
//		"SECTION_ID" => $_REQUEST["SECTION_ID"],
//		"SECTION_CODE" => $_REQUEST["CATALOG_CODE"],
		"HIDE_NOT_AVAILABLE" => "N",
		"PROPERTY_CODE" => array(
			0 => "MATERIAL_VERKHA_MARKETING",
			1 => "MATERIAL_PODKLADKI_MARKETING",
			2 => "CML2_ARTICLE",
			3 => "CML2_MANUFACTURER",
			4 => "KOLODKA",
			5 => "TSVET_MARKETING",
			6 => "VYSOTA_KABLUKA",
			7 => "VYSOTA_GOLENISHCHA_PROIZVODSTVO",
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
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "Y",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "Y",
		"META_DESCRIPTION" => "-",
		"SET_STATUS_404" => "N",
		"ADD_SECTIONS_CHAIN" => "Y",
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
		"BASKET_URL" => '/cabinet/basket/',
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
		"BACK_URL" => $_REQUEST['BACK_URL'],
		"RELOAD_PAGE_2_BASKET" => $_REQUEST['RELOAD'],
	),
	false
);?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>