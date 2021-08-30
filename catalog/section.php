<?
$res = CIBlockSection::GetList(array(), array('CODE' => $_REQUEST["CATALOG_CODE"], 'SITE_ID' => "s1"));
$section = $res->Fetch();

$APPLICATION->IncludeComponent(
	"bitrix:catalog.smart.filter", 
	"paoloconte", 
	array(
		"IBLOCK_TYPE" => IBLOCK_CATALOG_TYPE,
		"IBLOCK_ID" => IBLOCK_CATALOG,
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_CODE" => $_REQUEST["CATALOG_CODE"],
		"FILTER_NAME" => "arrFilter",
		"HIDE_NOT_AVAILABLE" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"SAVE_IN_SESSION" => "N",
		"INSTANT_RELOAD" => "N",
		"TEMPLATE_THEME" => "blue",
		"FILTER_VIEW_MODE" => "vertical",
		"POPUP_POSITION" => "right",
		"PRICE_CODE" => array(
		),
		"XML_EXPORT" => "N",
		"SECTION_TITLE" => "-",
		"SECTION_DESCRIPTION" => "-",
		"COMPONENT_TEMPLATE" => "paoloconte",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CONVERT_CURRENCY" => "N"
	),
	false
);

$APPLICATION->IncludeComponent(
    "sotbit:seo.meta",
    "blank",
    Array(
        "FILTER_NAME" => "arrFilter",
        "SECTION_ID" => $section["ID"],
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "36000000",
    )
);

$page = explode("/", $_REQUEST['SECTION_PATH']);
if ($page[0] == 'rasprodazha') {
    $idSection = '';
    $arFilter = array(
        "IBLOCK_ID" => IBLOCK_CATALOG,
        "IBLOCK_ACTIVE" => "Y",
        "ACTIVE" => "Y",
        "GLOBAL_ACTIVE" => "Y",
        "=CODE" => $_REQUEST["CATALOG_CODE"],
    );
    $rsSection = CIBlockSection::GetList(array(), $arFilter, false, $arSelect);
    $rsSection->SetUrlTemplates("", '/catalog/#SECTION_CODE#/');
    while ($arPath = $rsSection->GetNext()) {
        $arPath['arPage'] = explode("/", $arPath['SECTION_PAGE_URL']);
        if ($arPath['arPage'][2] == $page[0])
            $idSection = $arPath['ID'];
    }
    $_REQUEST["SECTION_ID"] = $idSection;
}

global $arrFilter;
//$arrFilterFunc = getCatalogFilter();
//$arrFilter = array_merge($arrFilter, $arrFilterFunc);
$arrFilterDop = getCatalogFilterDop();
$arrFilter = array_merge($arrFilter, $arrFilterDop);

if (!empty($_REQUEST["by"]))
{
    $sort = $_REQUEST["by"];
    $sort2 = 'CATALOG_PRICE_' . $_SESSION['GEO_PRICES']['PRICE_ID'];
    $order2 = 'asc';
}
else
{
    $sort = 'propertysort_KOLLEKTSIYA';
    $sort2 = 'show_counter_start';
    $order2 = 'desc';
}

$order = (!empty($_REQUEST["order"])) ? $_REQUEST["order"] : "desc";

$elementCount = "60";

$SECTION_CODE_PATH = "/catalog/#SECTION_CODE#/";
?>

<?
//Проверка на состояние фильтра
$filterCookieName = "SECTION_FILTER_CONDITION";
$cookieTTL = time()+60*60*24;   //1 сутки
$filterConditionCookie = $APPLICATION->get_cookie($filterCookieName);
$isShowFilter = false;

if ($filterConditionCookie == "HIDE")
{
    $isShowFilter = false;
}
else
{
    $isShowFilter = true;
}
?>

<? if (strpos($APPLICATION->GetCurPage(false), '/search/') === false) { ?>
    <div class="page-top page-top--catalog">
        <div class="container">
            <div class="page-top__inner">
                <div class="page-top-filter">
                    <div class="fastViewModal page-top-filter__inner<?=($isShowFilter ? " active" : "")?>" data-menu-link="filter" data-target="#fastViewModal">
                        <div class="plus"></div>
                        Фильтры
                    </div>
                </div>

                <? $APPLICATION->IncludeComponent(
                    "bitrix:breadcrumb",
                    ".default",
                    array(
                        "START_FROM" => "0",
                        "PATH" => "",
                        "SITE_ID" => "s1",
                        "COMPONENT_TEMPLATE" => ".default"
                    ),
                    false
                ); ?>

                <? $APPLICATION->ShowViewContent('catalog-section-page-top'); ?>
            </div>
        </div>
    </div>
<? } ?>

    <div class="container">
        <? $APPLICATION->ShowViewContent('catalog-section-h1-title'); ?>
        
        <?/* $APPLICATION->ShowViewContent('catalog-section-rr-block'); */?>
        <div class="aside aside--catalog<?=($isShowFilter ? " active" : "")?>">
            <div class="aside__sidebar" style="display:<?=($isShowFilter ? "block" : "none")?>;">
                <? $APPLICATION->ShowViewContent("smart_filter"); ?>
            </div>


            <? $APPLICATION->IncludeComponent(
                "bitrix:catalog.section",
                "paoloconte",
                array(
                    "IBLOCK_TYPE" => IBLOCK_CATALOG_TYPE,
                    "IBLOCK_ID" => IBLOCK_CATALOG,
                    "SECTION_ID" => $_REQUEST["SECTION_ID"],
                    "SECTION_CODE" => $_REQUEST["CATALOG_CODE"],
                    "SECTION_USER_FIELDS" => array("UF_IMAGE", "UF_DESCRIPTION", "UF_BANNER_LINK"),
                    "COOKIE_FILTER_HIDE" => $isShowFilter,
                    "ELEMENT_SORT_FIELD" => $sort,
                    "ELEMENT_SORT_ORDER" => $order,
                    "ELEMENT_SORT_FIELD2" => $sort2,
                    "ELEMENT_SORT_ORDER2" => $order2,
                    "FILTER_NAME" => "arrFilter",
                    "INCLUDE_SUBSECTIONS" => "A",
                    "SHOW_ALL_WO_SECTION" => "N",
                    "HIDE_NOT_AVAILABLE" => "N",
                    "PAGE_ELEMENT_COUNT" => $elementCount,
                    "LINE_ELEMENT_COUNT" => "5",
                    "PROPERTY_CODE" => array(
                        0 => "MATERIAL_VERKHA_MARKETING",
                        1 => "MATERIAL_PODKLADKI_MARKETING",
                        2 => "CML2_ARTICLE",
                        3 => "CML2_MANUFACTURER",
                        4 => "",
                    ),
                    "OFFERS_LIMIT" => "0",
                    "TEMPLATE_THEME" => "blue",
                    "PRODUCT_SUBSCRIPTION" => "N",
                    "SHOW_DISCOUNT_PERCENT" => "Y",
                    "SHOW_OLD_PRICE" => "Y",
                    "SHOW_CLOSE_POPUP" => "Y",
                    "MESS_BTN_BUY" => "Купить",
                    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
                    "MESS_BTN_SUBSCRIBE" => "Подписаться",
                    "MESS_BTN_DETAIL" => "Подробнее",
                    "MESS_NOT_AVAILABLE" => "Нет в наличии",
                    "SECTION_URL" => $SECTION_CODE_PATH,
                    "DETAIL_URL" => "/catalog/#CODE#/",
                    "SECTION_ID_VARIABLE" => "SECTION_CODE",
                    "AJAX_MODE" => "N",
                    "AJAX_OPTION_JUMP" => "N",
                    "AJAX_OPTION_STYLE" => "Y",
                    "AJAX_OPTION_HISTORY" => "N",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "36000000",
                    "CACHE_GROUPS" => "N",
                    "SET_TITLE" => "Y",
                    "SET_BROWSER_TITLE" => "Y",
                    "BROWSER_TITLE" => "-",
                    "SET_META_KEYWORDS" => "Y",
                    "META_KEYWORDS" => "-",
                    "SET_META_DESCRIPTION" => "Y",
                    "META_DESCRIPTION" => "-",
                    "ADD_SECTIONS_CHAIN" => "N",
                    "SET_STATUS_404" => "Y",
                    "CACHE_FILTER" => "Y",
                    "ACTION_VARIABLE" => "action",
                    "PRODUCT_ID_VARIABLE" => "id",
                    "PRICE_CODE" => $_SESSION['GEO_PRICES']['TO_PARAMS'],
                    "USE_PRICE_COUNT" => "N",
                    "SHOW_PRICE_COUNT" => "1",
                    "PRICE_VAT_INCLUDE" => "Y",
                    "CONVERT_CURRENCY" => "N",
                    "BASKET_URL" => BASKET_URL,
                    "USE_PRODUCT_QUANTITY" => "N",
                    "ADD_PROPERTIES_TO_BASKET" => "Y",
                    "PRODUCT_PROPS_VARIABLE" => "prop",
                    "PARTIAL_PRODUCT_PROPERTIES" => "N",
                    "PRODUCT_PROPERTIES" => array(),
                    "ADD_TO_BASKET_ACTION" => "ADD",
                    "DISPLAY_COMPARE" => "N",
                    //"PAGER_TEMPLATE" => "paolo",
                    "PAGER_TEMPLATE" => "paolo_modern",
                    "DISPLAY_TOP_PAGER" => "N",
                    "DISPLAY_BOTTOM_PAGER" => "Y",
                    "PAGER_TITLE" => "",
                    "PAGER_SHOW_ALWAYS" => "N",
                    "PAGER_DESC_NUMBERING" => "N",
                    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                    "PAGER_SHOW_ALL" => "N",
                    "OFFERS_FIELD_CODE" => array(
                        0 => "",
                    ),
                    "OFFERS_PROPERTY_CODE" => array(
                        0 => "RAZMER",
                    ),
                    "OFFERS_SORT_FIELD" => "sort",
                    "OFFERS_SORT_ORDER" => "asc",
                    "OFFERS_SORT_FIELD2" => "id",
                    "OFFERS_SORT_ORDER2" => "desc",
                    "PRODUCT_DISPLAY_MODE" => "Y",
                    "ADD_PICT_PROP" => "MORE_PHOTO",
                    "LABEL_PROP" => "-",
                    "MESS_BTN_COMPARE" => "Сравнить",
                    "OFFERS_CART_PROPERTIES" => array(
                        0 => "RAZMER",
                    ),
                    "OFFER_ADD_PICT_PROP" => "-",
                    "OFFER_TREE_PROPS" => array(
                        0 => "RAZMER",
                    ),
                    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                    "COMPARE_PATH" => "",
                    "AJAX_OPTION_ADDITIONAL" => "",
                    "CATALOG_ROW" => empty(htmlspecialchars($_COOKIE["catalog_row"])) ? 3 : htmlspecialchars($_COOKIE["catalog_row"])
                ),
                false
            ); ?>
			
        </div>

    
        <? $APPLICATION->ShowViewContent("catalog-section-pager"); ?>
        <?if ($_REQUEST['PAGEN_3'] == 0 ) {
            $APPLICATION->IncludeComponent(
                "sw:catalog.tags",
                "",
                [
                    "DIR" => $APPLICATION->GetCurDir(),
                    "BLOCK_TITLE" => 'Популярное',
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "36000000",
                    "CACHE_GROUPS" => "Y",
                    "COMPOSITE_FRAME_MODE" => "A",
                    "COMPOSITE_FRAME_TYPE" => "AUTO",
                ],
                false
            );
        }?>
		<div class="bottom_seo_text" style="margin-top:35px;">
<?
	global $sotbitSeoMetaBottomDesc;//для установки нижнего описания
	echo $sotbitSeoMetaBottomDesc;//вывод нижнего описания
?>
</div>
		
    </div>
	
<?
global $sotbitSeoMetaTitle;
global $sotbitSeoMetaKeywords;
global $sotbitSeoMetaDescription;
global $sotbitSeoMetaBreadcrumbTitle;
global $sotbitSeoMetaH1;

if (!empty($sotbitSeoMetaH1)) {
    $APPLICATION->SetTitle($sotbitSeoMetaH1);
}
if (!empty($sotbitSeoMetaTitle)) {
    $APPLICATION->SetPageProperty("title", $sotbitSeoMetaTitle);
}
if (!empty($sotbitSeoMetaKeywords)) {
    $APPLICATION->SetPageProperty("keywords", $sotbitSeoMetaKeywords);
}
if (!empty($sotbitSeoMetaDescription)) {
    $APPLICATION->SetPageProperty("description", $sotbitSeoMetaDescription);
}
if (!empty($sotbitSeoMetaBreadcrumbTitle)) {
    $APPLICATION->AddChainItem($sotbitSeoMetaBreadcrumbTitle);
}
?>