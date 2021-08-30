<?
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Продажа обуви и аксессуаров от   производителя в Москве. Бесплатная доставка с примеркой по России в   интернет-магазине «Paolo Conte». Есть пункты самовывоза.");
$APPLICATION->SetTitle("Интернет-магазин обуви и аксессуаров с   доставкой по Москве и всей России «Paolo Conte»");
?>

    <div class="main-slider-wrap">
        <? $APPLICATION->IncludeComponent(
            "citfact:elements.list",
            "slider_main",
            Array(
                "IBLOCK_ID" => 18,
                "PROPERTY_CODES" => array('LINK', 'IMAGE', 'PODPIS'),
                "FILTER" => [
                    "ACTIVE_DATE" => "Y",
                ]
            )
        ); ?>
    </div>

    <div class="main-content">
        <div class="container">
            <? $APPLICATION->IncludeComponent(
                "citfact:elements.list",
                "main_block",
                Array(
                    "IBLOCK_ID" => 54,
                    "PROPERTY_CODES" => array('TITLE', 'DESC', 'IMAGE', 'LINK', 'BUTTON_TEXT', 'TYPE_BLOCK'),
                    "TYPE_BLOCK" => '1',
                    "FILTER" => [
                        "ACTIVE_DATE" => "Y",
                    ]
                )
            ); ?>

            <? $APPLICATION->IncludeComponent(
                "citfact:elements.list",
                "main_block",
                Array(
                    "IBLOCK_ID" => 54,
                    "PROPERTY_CODES" => array('TITLE', 'DESC', 'IMAGE', 'LINK', 'BUTTON_TEXT', 'TYPE_BLOCK'),
                    "TYPE_BLOCK" => '2',
                    "FILTER" => [
                        "ACTIVE_DATE" => "Y",
                    ]
                )
            ); ?>

            <? $APPLICATION->IncludeComponent(
                "citfact:elements.list",
                "main_block",
                Array(
                    "IBLOCK_ID" => 54,
                    "PROPERTY_CODES" => array('TITLE', 'DESC', 'IMAGE', 'LINK', 'BUTTON_TEXT', 'TYPE_BLOCK'),
                    "TYPE_BLOCK" => '7',
                    "FILTER" => [
                        "ACTIVE_DATE" => "Y",
                    ]
                )
            ); ?>

            <?
            $arFilterEvents = array(
                '!PROPERTY_HIDE_VALUE' => 'Да'
            );

            $APPLICATION->IncludeComponent(
                "bitrix:news.list",
                "main_events_list",
                Array(
                    "IBLOCK_TYPE" => "info",
                    "IBLOCK_ID" => "24",
                    "NEWS_COUNT" => "3",
                    "SORT_BY1" => "ACTIVE_FROM",
                    "SORT_ORDER1" => "DESC",
                    "SORT_BY2" => "ID",
                    "SORT_ORDER2" => "DESC",
                    "FILTER_NAME" => "arFilterEvents",
                    "FIELD_CODE" => array("DATE_ACTIVE_TO", ""),
                    "PROPERTY_CODE" => array("DATE_END", ""),
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
            ); ?>

            <div class="main-accessories">
                <? $APPLICATION->IncludeComponent(
                    "citfact:elements.list",
                    "main_block",
                    Array(
                        "IBLOCK_ID" => 54,
                        "PROPERTY_CODES" => array('TITLE', 'DESC', 'IMAGE', 'LINK', 'BUTTON_TEXT', 'TYPE_BLOCK'),
                        "TYPE_BLOCK" => '3',
                        "FILTER" => [
                            "ACTIVE_DATE" => "Y",
                        ]
                    )
                ); ?>

                <? $APPLICATION->IncludeComponent(
                    "citfact:elements.list",
                    "main_block",
                    Array(
                        "IBLOCK_ID" => 54,
                        "PROPERTY_CODES" => array('TITLE', 'DESC', 'IMAGE', 'LINK', 'BUTTON_TEXT', 'TYPE_BLOCK'),
                        "TYPE_BLOCK" => '4',
                        "FILTER" => [
                            "ACTIVE_DATE" => "Y",
                        ]
                    )
                ); ?>
            </div>

            <div class="main-inst">
                <? $APPLICATION->IncludeComponent(
                    "citfact:elements.list",
                    "main_block",
                    Array(
                        "IBLOCK_ID" => 54,
                        "PROPERTY_CODES" => array('TITLE', 'DESC', 'IMAGE', 'LINK', 'BUTTON_TEXT', 'TYPE_BLOCK'),
                        "TYPE_BLOCK" => '5',
                        "FILTER" => [
                            "ACTIVE_DATE" => "Y",
                        ]
                    )
                ); ?>

                <div class="main-inst__middle">
                    <div class="main-title">
                        Instagram
                    </div>
                    <a href="https://www.instagram.com/paoloconteshoes/" class="btn btn--transparent" target="_blank">Подписаться</a>
                </div>

                <? $APPLICATION->IncludeComponent(
                    "citfact:elements.list",
                    "main_block",
                    Array(
                        "IBLOCK_ID" => 54,
                        "PROPERTY_CODES" => array('TITLE', 'DESC', 'IMAGE', 'LINK', 'BUTTON_TEXT', 'TYPE_BLOCK'),
                        "TYPE_BLOCK" => '6',
                        "FILTER" => [
                            "ACTIVE_DATE" => "Y",
                        ]
                    )
                ); ?>
            </div>
        </div>
    </div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
