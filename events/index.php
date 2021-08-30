<?require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "новости, акции, события");
$APPLICATION->SetPageProperty("title", "Акции и новости Паоло Конте");
$APPLICATION->SetPageProperty("description", "Новости бренда и магазинов Paolo Conte, акции и другие актуальные события.");
$APPLICATION->SetTitle("Акции и события");

$APPLICATION->SetAdditionalCSS("/local/templates/paoloconte/components/bitrix/news.detail/actions/js/owl.carousel.css");
$APPLICATION->AddHeadScript('/local/templates/paoloconte/components/bitrix/news.detail/actions/js/owl.carousel.min.js');
?>

<?

$arFilterEvents = array(
    '!PROPERTY_HIDE_VALUE' => 'Да'
);

$APPLICATION->IncludeComponent(
    "bitrix:news.list",
    "actions",
    Array(
        "IBLOCK_TYPE" => "info",
        "IBLOCK_ID" => "24",
        "NEWS_COUNT" => "12",
        "SORT_BY1" => "ACTIVE_FROM",
        "SORT_ORDER1" => "DESC",
        "SORT_BY2" => "ID",
        "SORT_ORDER2" => "DESC",
        "FILTER_NAME" => "arFilterEvents",
        "FIELD_CODE" => array("DATE_ACTIVE_TO",""),
        "PROPERTY_CODE" => array("DATE_END",""),
        "CHECK_DATES" => "Y",
        "DETAIL_URL" => "",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "Y",
        "AJAX_OPTION_HISTORY" => "N",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "86400",
        "CACHE_FILTER" => "N",
        "CACHE_GROUPS" => "Y",
        "PREVIEW_TRUNCATE_LEN" => "",
        "ACTIVE_DATE_FORMAT" => "j F Y",
        "SET_TITLE" => "N",
        "SET_BROWSER_TITLE" => "Y",
        "SET_META_KEYWORDS" => "Y",
        "SET_META_DESCRIPTION" => "Y",
        "SET_STATUS_404" => "N",
        "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
        "ADD_SECTIONS_CHAIN" => "Y",
        "HIDE_LINK_WHEN_NO_DETAIL" => "N",
        "PARENT_SECTION" => "",
        "PARENT_SECTION_CODE" => "",
        "INCLUDE_SUBSECTIONS" => "Y",
        "DISPLAY_DATE" => "Y",
        "DISPLAY_NAME" => "Y",
        "DISPLAY_PICTURE" => "Y",
        "DISPLAY_PREVIEW_TEXT" => "Y",
        "PAGER_TEMPLATE" => "paolo_modern",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TITLE" => "Акции",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
        "PAGER_SHOW_ALL" => "N"
    )
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>