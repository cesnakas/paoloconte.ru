<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Страница не найдена");
$APPLICATION->AddChainItem('404', '#');
?>
<div class="container">
	<div class="error-404-wrap">
		<div class="top-text align-center">
			К сожалению, запрашиваемой вами страницы не существует, но внутри сайта есть много интересного.</br>Попробуйте вернуться на главную и убедиться в этом или воспользуйтесь поиском, если ищете что-то конкретное.
		</div>

		<div class="search-wrap">
			<?/*<form action="#">
				<input type="text" class=""  placeholder="Поиск по каталогу товаров">
				<div class="search-btn no-select"><i class="fa fa-search"></i></div>
				<input type="submit" class="search-icon hide">
			</form>*/?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:search.form",
				"404",
				array(
					"PAGE" => "#SITE_DIR#search/"
				),
				false
			);?>
		</div>

		<div class="btn-box align-center">
			<a href="/" class="btn btn-gray-dark mode2 icon-arrow-right">Вернуться на главную</a>
		</div>
	</div>
	<div data-retailrocket-markup-block="56729fef9872e52a3cbd9a85" ></div>
	<?
	/*
	<div class="main-item-slider-wrap error-404-slider-wrap">
		<div class="container">
			<div class="block-title">
				<h4>Популярные модели</h4>
				<div class="title-link">
					<a href="/catalog/"><span>Все модели</span> <i class="fa fa-play"></i></a>
				</div>
			</div>
			<?
			global $arrFilter_top;
			$arrFilter_top = array(
				'>CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID'] => 0,
				'>PROPERTY_OFFERS_AMOUNT' => 0,
			);
			?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:catalog.top",
				"paoloconte",
				array(
					"COMPONENT_TEMPLATE" => "paoloconte",
					"IBLOCK_TYPE" => "catalog",
					"IBLOCK_ID" => "10",
					"ELEMENT_SORT_FIELD" => "shows",
					"ELEMENT_SORT_ORDER" => "desc",
					"ELEMENT_SORT_FIELD2" => "id",
					"ELEMENT_SORT_ORDER2" => "desc",
					"FILTER_NAME" => "arrFilter_top",
					"HIDE_NOT_AVAILABLE" => "N",
					"ELEMENT_COUNT" => "9",
					"LINE_ELEMENT_COUNT" => "3",
					"PROPERTY_CODE" => array(
						0 => "",
						1 => "",
					),
					"OFFERS_LIMIT" => "0",
					"VIEW_MODE" => "SECTION",
					"SHOW_DISCOUNT_PERCENT" => "N",
					"SHOW_OLD_PRICE" => "Y",
					"SHOW_CLOSE_POPUP" => "N",
					"MESS_BTN_BUY" => "Купить",
					"MESS_BTN_ADD_TO_BASKET" => "В корзину",
					"MESS_BTN_DETAIL" => "Подробнее",
					"MESS_NOT_AVAILABLE" => "Нет в наличии",
					"SECTION_URL" => "",
					"DETAIL_URL" => "",
					"SECTION_ID_VARIABLE" => "SECTION_ID",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_GROUPS" => "Y",
					"CACHE_FILTER" => "N",
					"ACTION_VARIABLE" => "action",
					"PRODUCT_ID_VARIABLE" => "id",
					"PRICE_CODE" => $_SESSION['GEO_PRICES']['TO_PARAMS'],
					"USE_PRICE_COUNT" => "N",
					"SHOW_PRICE_COUNT" => "1",
					"PRICE_VAT_INCLUDE" => "Y",
					"CONVERT_CURRENCY" => "N",
					"BASKET_URL" => "/personal/basket.php",
					"USE_PRODUCT_QUANTITY" => "N",
					"ADD_PROPERTIES_TO_BASKET" => "Y",
					"PRODUCT_PROPS_VARIABLE" => "prop",
					"PARTIAL_PRODUCT_PROPERTIES" => "N",
					"PRODUCT_PROPERTIES" => array(
						0 => "RAZMER",
					),
					"ADD_TO_BASKET_ACTION" => "ADD",
					"DISPLAY_COMPARE" => "N",
					"TEMPLATE_THEME" => "blue",
					"MESS_BTN_COMPARE" => "Сравнить",
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
					"OFFERS_SORT_ORDER2" => "desc",
					"PRODUCT_DISPLAY_MODE" => "N",
					"ADD_PICT_PROP" => "-",
					"LABEL_PROP" => "-",
					"OFFERS_CART_PROPERTIES" => array(
					),
					"PRODUCT_QUANTITY_VARIABLE" => "quantity"
				),
				false
			);?>
		</div>
	</div>
	*/
	?>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>