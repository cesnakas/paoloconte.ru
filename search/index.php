<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "поиск, интернет-магазин, магазин");
$APPLICATION->SetPageProperty("keywords", "женская обувь, мужская обувь, женские аксессуары, мужские аксессуары, поиск товаров, магазины");
$APPLICATION->SetPageProperty("description", "Поиск женской и мужской обуви, аксессуаров в интернет-магазине Paolo Conte");
$APPLICATION->SetTitle("Поиск товаров и магазинов");
?><?$APPLICATION->IncludeComponent(
	"bitrix:search.page",
	".default",
	array(
		"RESTART" => "N",
		"NO_WORD_LOGIC" => "N",
		"CHECK_DATES" => "Y",
		"USE_TITLE_RANK" => "Y",
		"DEFAULT_SORT" => "rank",
		"FILTER_NAME" => "",
		"arrFILTER" => array(
			0 => "iblock_catalog",
			1 => "iblock_info",
			2 => "iblock_tools",
		),
		"arrFILTER_iblock_catalog" => array(
			0 => "10",
		),
		"arrFILTER_iblock_info" => array(
			0 => "all",
		),
		"arrFILTER_iblock_tools" => array(
			0 => "30",
		),
		"SHOW_WHERE" => "N",
		"SHOW_WHEN" => "N",
		"PAGE_RESULT_COUNT" => "1000",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600",
		"USE_LANGUAGE_GUESS" => "N",
		"USE_SUGGEST" => "N",
		"SHOW_ITEM_TAGS" => "N",
		"SHOW_ITEM_DATE_CHANGE" => "N",
		"SHOW_ORDER_BY" => "N",
		"SHOW_TAGS_CLOUD" => "N",
		"SHOW_RATING" => "",
		"RATING_TYPE" => "",
		"PATH_TO_USER_PROFILE" => "",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Результаты поиска",
		"PAGER_SHOW_ALWAYS" => "Y",
		"PAGER_TEMPLATE" => "",
		"AJAX_OPTION_ADDITIONAL" => ""
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>