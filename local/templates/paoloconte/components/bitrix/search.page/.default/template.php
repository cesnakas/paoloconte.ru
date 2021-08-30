<? use Citfact\Tools;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$APPLICATION->SetPageProperty("title", GetMessage("SEARCH_TITLE"));
?>
<div class="search-page">
<form action="" method="get">
<?if($arParams["USE_SUGGEST"] === "Y"):
	if(strlen($arResult["REQUEST"]["~QUERY"]) && is_object($arResult["NAV_RESULT"]))
	{
		$arResult["FILTER_MD5"] = $arResult["NAV_RESULT"]->GetFilterMD5();
		$obSearchSuggest = new CSearchSuggest($arResult["FILTER_MD5"], $arResult["REQUEST"]["~QUERY"]);
		$obSearchSuggest->SetResultCount($arResult["NAV_RESULT"]->NavRecordCount);
	}
	?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:search.suggest.input",
		"",
		array(
			"NAME" => "q",
			"VALUE" => $arResult["REQUEST"]["~QUERY"],
			"INPUT_SIZE" => 40,
			"DROPDOWN_SIZE" => 10,
			"FILTER_MD5" => $arResult["FILTER_MD5"],
		),
		$component, array("HIDE_ICONS" => "Y")
	);?>
<?else:?>
	<input type="text" name="q" value="<?=$arResult["REQUEST"]["QUERY"]?>" size="40" class="search-input" />
<?endif;?>
	<button class="search-btn no-select" type="submit">
        <svg class='i-icon'>
            <use xlink:href='#search'/>
        </svg>
    </button>
	<input type="hidden" name="how" value="<?echo $arResult["REQUEST"]["HOW"]=="d"? "d": "r"?>" />
<?if($arParams["SHOW_WHEN"]):?>
	<script>
	var switch_search_params = function()
	{
		var sp = document.getElementById('search_params');
		var flag;
		var i;

		if(sp.style.display == 'none')
		{
			flag = false;
			sp.style.display = 'block'
		}
		else
		{
			flag = true;
			sp.style.display = 'none';
		}

		var from = document.getElementsByName('from');
		for(i = 0; i < from.length; i++)
			if(from[i].type.toLowerCase() == 'text')
				from[i].disabled = flag;

		var to = document.getElementsByName('to');
		for(i = 0; i < to.length; i++)
			if(to[i].type.toLowerCase() == 'text')
				to[i].disabled = flag;

		return false;
	}
	</script>
	<br /><a class="search-page-params" href="#" onclick="return switch_search_params()"><?echo GetMessage('CT_BSP_ADDITIONAL_PARAMS')?></a>
	<div id="search_params" class="search-page-params" style="display:<?echo $arResult["REQUEST"]["FROM"] || $arResult["REQUEST"]["TO"]? 'block': 'none'?>">
		<?$APPLICATION->IncludeComponent(
			'bitrix:main.calendar',
			'',
			array(
				'SHOW_INPUT' => 'Y',
				'INPUT_NAME' => 'from',
				'INPUT_VALUE' => $arResult["REQUEST"]["~FROM"],
				'INPUT_NAME_FINISH' => 'to',
				'INPUT_VALUE_FINISH' =>$arResult["REQUEST"]["~TO"],
				'INPUT_ADDITIONAL_ATTR' => 'size="10"',
			),
			null,
			array('HIDE_ICONS' => 'Y')
		);?>
	</div>
<?endif?>
</form><br />

</div>

<div class="aside aside--catalog">
<?global $arrFilter;
if (!empty($arResult['CATALOG_IDS'])){
	$arrFilter = array('ID' => $arResult['CATALOG_IDS']);

	$arFilter_dop = array(
		'>CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID'] => 0,
        'ACTIVE' => 'Y',
        'PROPERTY_HAS_PHOTO' => 'Y',
        '>PROPERTY_OFFERS_AMOUNT' => 0,
	);
	$arrFilter = array_merge($arrFilter, $arFilter_dop);
?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.section",
	"paoloconte",
	array(
		"IBLOCK_TYPE" => IBLOCK_CATALOG_TYPE,
		"IBLOCK_ID" => IBLOCK_CATALOG,
		"SECTION_ID" => '',
		"SECTION_CODE" => '',
		"SECTION_USER_FIELDS" => array(),
		"ELEMENT_SORT_FIELD" => $sort,
		"ELEMENT_SORT_ORDER" => $order,
		"ELEMENT_SORT_FIELD2" => 'CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID'],
		"ELEMENT_SORT_ORDER2" => "asc",
		"FILTER_NAME" => "arrFilter",
		"INCLUDE_SUBSECTIONS" => "A",
		"SHOW_ALL_WO_SECTION" => "N",
		"HIDE_NOT_AVAILABLE" => "N",
		"PAGE_ELEMENT_COUNT" => 60,
		"LINE_ELEMENT_COUNT" => "5",
		"PROPERTY_CODE" => array(
			0 => "MATERIAL_VERKHA_MARKETING",
			1 => "MATERIAL_PODKLADKI_MARKETING",
			2 => "CML2_ARTICLE",
			3 => "CML2_MANUFACTURER",
			4 => "COUNTRY",
			5 => "KOLODKA",
			6 => "",
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
		"SECTION_URL" => "/catalog/#SECTION_CODE#/",
		"DETAIL_URL" => "/catalog/#SECTION_CODE#/#CODE#/",
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
		"ADD_SECTIONS_CHAIN" => "Y",
		"SET_STATUS_404" => "N",
		"CACHE_FILTER" => "N",
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
		"PRODUCT_PROPERTIES" => array(
		),
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
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?>
<? $APPLICATION->ShowViewContent("catalog-section-pager"); ?>
<?}
else{?>
	К сожалению, товары по вашему запросу не найдены.
<?}?>
<?if (!empty($arResult["SHOPS_CURRENT"])):?>
	<div class="map-link-list shops-insearch">
		<div class="title">Найдены магазины</div>
		<div class="list-wrap">
			<div class="city-cont">
				<?foreach ($arResult['SHOPS_CURRENT'] as $arShop):?>
					<div class="list-item collapsed clear-after">
						<div class="float-left third">
							<?=$arShop['NAME']?>
						</div>
						<div class="float-left third">
							<?=$arShop['PROPERTY_ADDRESS_VALUE']?>
						</div>
						<div class="float-left third">
							<?if($arShop['~PROPERTY_GRAPHICK_VALUE']['TEXT'] != ''):?><?=$arShop['~PROPERTY_GRAPHICK_VALUE']['TEXT']?><br><?endif?>
							<?if ($arShop['PROPERTY_PHONE_VALUE'] != ''):?>Телефон: <?=$arShop['PROPERTY_PHONE_VALUE']?><?endif;?>
							<div class="title-link no-wrap">
								<a href="/shops/<?=$arShop['PROPERTY_CITY_CODE']?>/<?=$arShop['CODE']?>/"><span>Подробнее</span> <i class="fa fa-play"></i></a>
							</div>
						</div>
					</div>
				<?endforeach?>
			</div>
		</div>
	</div>
<?endif;?>
</div>

</div>
<div class="container">
    <div class="product">
        <?
        global $arrFilterBigData;
        $arrFilterBigData = Tools::getFilterForBigData();

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
                "DETAIL_URL" => "/catalog/#CODE#/",
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
                "LABEL_PROP" => array(),
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
                    0 => "RAZMER",
                ),
                "OFFERS_FIELD_CODE" => array(
                    0 => "",
                    1 => "",
                ),
                "OFFERS_LIMIT" => "5",
                "OFFERS_PROPERTY_CODE" => array(
                    0 => "RAZMER",
                    1 => "",
                ),
//                "ELEMENT_SORT_FIELD" => $sort,
//                "ELEMENT_SORT_ORDER" => $order,
//                "ELEMENT_SORT_FIELD2" => $sort2,
//                "ELEMENT_SORT_ORDER2" => $order2,
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
                "PROPERTY_CODE_MOBILE" => array(),
                "RCM_PROD_ID" => '',
                "RCM_TYPE" => "similar",
                "SECTION_ID" => '',
                "SECTION_ID_VARIABLE" => "SECTION_ID",
                "SECTION_URL" => "/catalog/#SECTION_CODE#/",
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
                "OFFER_TREE_PROPS" => array(),
                "LABEL_PROP_MOBILE" => "",
                "LABEL_PROP_POSITION" => "top-left",
                "DISCOUNT_PERCENT_POSITION" => "bottom-right",
                'HIDE_SECTION_DESCRIPTION' => 'Y',
            ),
            false
        ); ?>
    </div>
</div>
<div class="container b-static">
