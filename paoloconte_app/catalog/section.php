<div class="container">
	<?/*
		<div class="block-title">
			<h1><?$APPLICATION->ShowTitle(false);?></h1>
			<? $APPLICATION->ShowViewContent('mobile_catalog_prev_path'); ?>
		</div>
	*/?>

	<div class="filter-wrap">

		<div class="tab-box" role="tabpanel">
			<ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="<?/*active*/?> li-filter"><a href="#c1" aria-controls="c1" role="tab" data-toggle="tab"><img src="<?=SITE_TEMPLATE_PATH?>/images/filter.svg"> Фильтровать</a></li>
				<li role="presentation" class="li-sort"><a href="#c2" aria-controls="c2" role="tab" data-toggle="tab"><img src="<?=SITE_TEMPLATE_PATH?>/images/sort.svg"> Сортировать</a></li>
			</ul>

			<div class="tab-content">
				<div role="tabpanel" class="tab-pane fade <?/*in active*/?>" id="c1">
					<?$APPLICATION->IncludeComponent(
						"bitrix:catalog.smart.filter",
						"paoloconte_mobile_filter",
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
							"PRICE_CODE" => $_SESSION['GEO_PRICES']['TO_PARAMS'],
							"XML_EXPORT" => "N",
							"SECTION_TITLE" => "-",
							"SECTION_DESCRIPTION" => "-"
						),
						false
					);?>
				</div>

				<div role="tabpanel" class="tab-pane fade" id="c2">
					<div class="pagination-wrap">
						<? $APPLICATION->ShowViewContent('mobile_catalog_sort'); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel-group" id="" role="tablist" aria-multiselectable="true">
			<?
			$sort = (!empty($_REQUEST["by"]))? $_REQUEST["by"] : "PROPERTY_KOLODKA";
			if ($_REQUEST["by"] == 'PRICE') {
				$sort = 'PROPERTY_PRICE_PROP';
				//$sort = 'CATALOG_PRICE_' . $_SESSION['GEO_PRICES']['PRICE_ID_ACTION'];
			}
			$order = (!empty($_REQUEST["order"]))? $_REQUEST["order"] : "asc";
			$elementCount = (!empty($_REQUEST["count"]))? $_REQUEST["count"] : "6";
			?>
			<?
			global $arrFilter;
			$arFilter_dop = array(
				'>CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID'] => 0,
				'>PROPERTY_OFFERS_AMOUNT' => 0,
				'PROPERTY_HAS_PHOTO' => 'Y',
			);
			$arrFilter = array_merge($arrFilter, $arFilter_dop);
			?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:catalog.section",
				"paoloconte_app",
				array(
					"IBLOCK_TYPE" => IBLOCK_CATALOG_TYPE,
					"IBLOCK_ID" => IBLOCK_CATALOG,
					"SECTION_ID" => $_REQUEST["SECTION_ID"],
					"SECTION_CODE" => $_REQUEST["CATALOG_CODE"],
					"SECTION_USER_FIELDS" => array(),
					"ELEMENT_SORT_FIELD" => $sort,
					"ELEMENT_SORT_ORDER" => $order,
					"ELEMENT_SORT_FIELD2" => 'CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID'],
					"ELEMENT_SORT_ORDER2" => "asc",
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
					"SECTION_URL" => "/paoloconte_app/catalog/#SECTION_CODE#/",
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
					"PRICE_VAT_INCLUDE" => "N",
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
		</div>
	</div>
</div>
