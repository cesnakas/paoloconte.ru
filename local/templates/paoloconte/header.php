<?session_start();
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Composite\BufferArea;
use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');
CJSCore::init(['fx', 'ajax']);

if ($_GET['m'] == '0') {
    setcookie("mredir", '0', 0, "/");
}

$cur_page = $APPLICATION->GetCurPage(true);
$cur_page_no_index = $APPLICATION->GetCurPage(false);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <link href="/jivosite/jivosite.css" rel="stylesheet">
    <script src="/jivosite/jivosite.js"></script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="cmsmagazine" content="a20ba548f835fe67804c54da44e19294">
    <meta property="og:title" content="Paolo Conte">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://paoloconte.ru">
    <meta property="og:image" content="https://paoloconte.ru/local/templates/paoloconte/images/logo_paolo_conte.png">
    <script>
      window.INLINE_SVG_REVISION = <?= filemtime($_SERVER['DOCUMENT_ROOT'] . '/local/templates/paoloconte/build/sprite.svg') ?>
    </script>
    <?php
    $curPage = $APPLICATION->GetCurPage(false);
    if (preg_match('/^\/catalog/', $curPage) && $_REQUEST['PAGEN_3']) {
        echo '<link rel="canonical" href="https://' . $_SERVER['SERVER_NAME'] . $curPage . '"/>';
    }
    ?>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="apple-touch-icon" href="/images/apple_touch_icons/57x57.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/images/apple_touch_icons/72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/images/apple_touch_icons/114x114.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/images/apple_touch_icons/144x144.png">
    <title><? $APPLICATION->ShowTitle() ?></title>

    <?
    $APPLICATION->ShowMeta("robots", false, true);
    $APPLICATION->ShowMeta("keywords", false, true);
    $APPLICATION->ShowMeta("description", false, true);

    $APPLICATION->ShowCSS(true, true);
    $APPLICATION->ShowHeadStrings();
    $APPLICATION->ShowHeadScripts();
    $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . "/scripts.js");

    $APPLICATION->SetAdditionalCSS("/bitrix/js/socialservices/css/ss.css");

    $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . "/template_styles_dop.css");

    $APPLICATION->IncludeComponent("articul.geolocation.detect_ip", "", array("IBLOCK_CODE" => "city"));

    $APPLICATION->AddBufferContent([$APPLICATION, 'GetLink'], 'canonical'); // вывод canonical

    global $showWindow;
    if (!isset($_COOKIE['cookie_city_id']) || $_COOKIE['cookie_city_id'] == '') {
        $showWindow = false;
    } else {
        $showWindow = false;
    }

    $domain_raw = str_replace("www.", "", $_SERVER['SERVER_NAME']);
    setcookie("cookie_city_id", $_SESSION['CITY_ID'], time() + 3600 * 24 * 7, "/", $domain_raw);
    ?>

    <!--[if lt IE 9]>
    <script async src="<?=SITE_TEMPLATE_PATH;?>/vendor/html5shiv/dist/html5shiv.js"></script>
    <![endif]-->

    <script async src="//vk.com/js/api/openapi.js?105"></script>
    <!-- Facebook Pixel Code -->
    <script>
      !function (f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function () {
          n.callMethod ?
            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
      }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '2211662452407847');
      fbq('track', 'PageView');
    </script>
    <!-- End Facebook Pixel Code -->

    <!-- Retail Rocket tracking code   -->
    <script data-skip-moving="true">
        var rrPartnerId = "566ae1b89872e5140c6d08c5";
        var rrApi = {};
        var rrApiOnReady = rrApiOnReady || [];
        rrApi.addToBasket = rrApi.order = rrApi.categoryView = rrApi.view =
            rrApi.recomMouseDown = rrApi.recomAddToCart = function() {};
        (function(d) {
            var ref = d.getElementsByTagName('script')[0];
            var apiJs, apiJsId = 'rrApi-jssdk';
            if (d.getElementById(apiJsId)) return;
            apiJs = d.createElement('script');
            apiJs.id = apiJsId;
            apiJs.async = true;
            apiJs.src = "//cdn.retailrocket.ru/content/javascript/tracking.js";
            ref.parentNode.insertBefore(apiJs, ref);
        }(document));
    </script>
    <!-- End Retail Rocket tracking code   -->
    
    <? if ($curPage == '/'){ ?>
    <meta name="google-site-verification" content="Ov_RAWsNxNh7LMVBL4Af6UBRqPP1MmG0WcYPBA4u6tA" />
    <? } ?>
</head>
<?
$main_page = true;
$show_title = true;
$curdir = $APPLICATION->GetCurDir();
if ((strpos($_SERVER['REQUEST_URI'], '/main') === false) && ($curdir != "/")) {
    $main_page = false;
    $detail_page = false;
    $action_page = false;
    $error_page = false;

    if ((strpos($_SERVER['REQUEST_URI'], '/detail') === false)) {
        $detail_page = true;
    }
    if (!(strpos($_SERVER['REQUEST_URI'], '/actions/detail.php') === false)) {
        $detail_page = true;
        $action_page = true;
    }
}
if (defined('ERROR_404')) {
    $error_page = true;
}

$left_aside = false;
$catalog_page = false;
$catalog_detail_page = false;
$cabinet_page = false;
$sertificate_page = false;
$search_page = false;
$cabinet_basket_page = false;
$left_about = false;
$left_help = false;
$about_page = false;
$contacts_page = false;
$events_page = false;
$social_page = false;
$vacancy_page = false;
$shops_page = false;
$dogovor_page = false;
$franchise_page = false;
$oformlenie_page = false;
$oplata_page = false;
$vozvrat_page = false;
$vybor_page = false;
$gid_page = false;
$pravila_page = false;
$programma_page = false;
$register_page = false;
$help_page = false;

if (strpos($_SERVER['REQUEST_URI'], '/catalog') !== false) {
    $catalog_page = true;

    //определяем к каталоге карточку товара
    global $catalogMode;
    $catalogMode = 'SECTION'; // для секции также скрываем заголовок
    // $_REQUEST["CATALOG_CODE"] заполняется в init.php
    if (isset($_REQUEST["CATALOG_CODE"]) && !empty($_REQUEST["CATALOG_CODE"])) {
        $arFilter = Array("IBLOCK_ID" => IBLOCK_CATALOG, "CODE" => $_REQUEST["CATALOG_CODE"], "ACTIVE_DATE" => "Y", "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 1), Array("ID", "IBLOCK_ID"));
        if ($ob = $res->GetNextElement(false, false)) {
            $catalogMode = 'ELEMENT';
            $show_title = false;
        } else {
            $show_title = false;
        }
    }
}

if (strpos($_SERVER['REQUEST_URI'], '/cabinet') !== false/*$APPLICATION->GetCurDir() == '/cabinet/' && $USER->IsAuthorized()*/) {
    $cabinet_page = true;
}
if ($curdir == '/sertificate/') {
    $sertificate_page = true;
}
if ($curdir == '/search/') {
    $search_page = true;
}
if ($curdir == '/cabinet/basket/') {
    $cabinet_basket_page = true;
}
if ($curdir == '/about/') {
    $about_page = true;
}
if ($curdir == '/contacts/') {
    $contacts_page = true;
}
if (($curdir == '/events/') || (strpos($_SERVER['REQUEST_URI'], '/events/') !== false)) {
    $events_page = true;
}
if ($curdir == '/socialnye-sety/') {
    $social_page = true;
}
if ($curdir == '/vacancy/') {
    $vacancy_page = true;
}
if ($curdir == '/shops/') {
    $shops_page = true;
}
if ($curdir == '/help/dogovor-oferty/') {
    $dogovor_page = true;
}
if ($curdir == '/about/franchise/') {
    $franchise_page = true;
}
if ($curdir == '/help/') {
    $help_page = true;
}
if ($curdir == '/help/oformlenie-zakaza/') {
    $oformlenie_page = true;
}
if ($curdir == '/help/oplata-i-dostavka/') {
    $oplata_page = true;
}
if ($curdir == '/help/vozvrat/') {
    $vozvrat_page = true;
}
if ($curdir == '/help/vybor-razmera/') {
    $vybor_page = true;
}
if ($curdir == '/help/gid-po-vidam-obuvi/') {
    $gid_page = true;
}
if ($curdir == '/help/pravila-ukhoda-za-obuvyu/') {
    $pravila_page = true;
}
if ($curdir == '/about/programma-loyalnosti/') {
    $programma_page = true;
}
if ($curdir == '/register/') {
    $register_page = true;
}


if ($cabinet_page || $sertificate_page || ($catalog_page && $catalogMode != 'ELEMENT') || $search_page) {
    $left_aside = true;
}
if ($about_page || $contacts_page || $events_page || $social_page || $vacancy_page || $dogovor_page || $franchise_page) {
    $left_about = true;
}
if ($help_page || $oformlenie_page || $oplata_page || $vozvrat_page || $vybor_page || $gid_page || $pravila_page || $programma_page) {
    $left_help = true;
}

?>

<body <? if ($main_page == true && $error_page == false) { ?>class="mainpage" <? } else {?>class="insidepage"<?}?>>
<noscript>
    <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=2211662452407847&ev=PageView&noscript=1" alt="">
</noscript>

<!-- Google Tag Manager -->
<noscript>
    <iframe src="//www.googletagmanager.com/ns.html?id=GTM-5R29MN" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<script data-skip-moving="true">(function (w, d, s, l, i) {
    w[l] = w[l] || [];
    w[l].push({
      'gtm.start':
        new Date().getTime(), event: 'gtm.js'
    });
    var f = d.getElementsByTagName(s)[0],
      j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
    j.async = true;
    j.src =
      '//www.googletagmanager.com/gtm.js?id=' + i + dl;
    f.parentNode.insertBefore(j, f);
  })(window, document, 'script', 'dataLayer', 'GTM-5R29MN');
</script>
<!-- End Google Tag Manager -->
<?
Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("header_banner");
$APPLICATION->IncludeComponent(
    "artdepo:notifybar.list",
    "",
    Array(
        "IBLOCK_TYPE" => "notifybar",
        "IBLOCK_ID" => "notifybar",
        "SORT_BY1" => "RAND",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "60",
    )
);
Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("header_banner", "");
?>

<div id="fb-root"></div>
<script>(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.4&appId=1423783074519537";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

<div class="hidden" itemscope itemtype="http://schema.org/PeopleAudience">
    <span itemprop="suggestedMinAge">16</span>
    <span itemprop="suggestedMaxAge">60</span>
</div>

<? $APPLICATION->ShowPanel(); ?>

<div id="wrapper">
    <nav id="side-cart" class="moved-panel">
        <div class="moved-panel-wrap">
            <? $APPLICATION->IncludeComponent(
                "bitrix:sale.basket.basket.line",
                "list",
                Array(
                    "PATH_TO_BASKET" => "/cabinet/basket/",
                    "PATH_TO_ORDER" => "/cabinet/basket/",
                    "SHOW_NUM_PRODUCTS" => "Y",
                    "SHOW_TOTAL_PRICE" => "N",
                    "SHOW_EMPTY_VALUES" => "N",
                    "SHOW_PERSONAL_LINK" => "N",
                    "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
                    "SHOW_AUTHOR" => "N",
                    "PATH_TO_REGISTER" => SITE_DIR . "login/",
                    "PATH_TO_PROFILE" => SITE_DIR . "personal/",
                    "SHOW_PRODUCTS" => "Y",
                    "POSITION_FIXED" => "N"
                )
            ); ?>
        </div>
    </nav>

    <div id="content-container">
        <header class="header">
            <div class="header__inner" data-m-fixed-header>
                <div class="container">
                    <div class="header__top">
                        <div class="header__tel-wrap">
                            <a href="#" class="header__tel b24-web-form-popup-btn-16">
                                <? $APPLICATION->IncludeComponent("articul.geolocation.city_current", "phone", array(), false); ?>
                            </a>
                        </div>
                        <div class="header-m__burger">
                            <a href="javascript:void(0);" class="header-btn" data-menu-link="burger">
                                <span class="header-btn__line"></span>
                            </a>
                        </div>
                        <a href="/" class="header__logo">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/logo_paolo_conte.svg" alt="Paolo Conte">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/logo_mobile.svg" alt="Paolo Conte">
                        </a>

                        <div class="header__actions">

                            <div class="header__city">
                                <a href="#" class="" data-toggle="modal" data-target="#cityModal">
                                    <? $APPLICATION->IncludeComponent("articul.geolocation.city_current", "desktop", array(), false); ?>
                                </a>
                            </div>
                            <div class="header__search">
                                <div class="search-line">
                                    <? $APPLICATION->IncludeComponent(
                                        "bitrix:search.form",
                                        ".default",
                                        array(
                                            "PAGE" => "#SITE_DIR#search/"
                                        ),
                                        false
                                    ); ?>
                                </div>

                                <div class="search-btn no-select to-search">
                                    <svg class='i-icon'>
                                        <use xlink:href='#search'/>
                                    </svg>
                                </div>
                            </div>

                            <div class="search-mask"></div>

                            <div class="header-cart">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:sale.basket.basket.line",
                                    ".default",
                                    array(
                                        "PATH_TO_BASKET" => SITE_DIR . "cabinet/basket/",
                                        "SHOW_NUM_PRODUCTS" => "Y",
                                        "SHOW_TOTAL_PRICE" => "N",
                                        "SHOW_EMPTY_VALUES" => "N",
                                        "SHOW_PERSONAL_LINK" => "N",
                                        "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
                                        "SHOW_AUTHOR" => "N",
                                        "PATH_TO_REGISTER" => SITE_DIR . "login/",
                                        "PATH_TO_PROFILE" => SITE_DIR . "personal/",
                                        "SHOW_PRODUCTS" => "Y",
                                        "POSITION_FIXED" => "N",
                                        "COMPONENT_TEMPLATE" => ".default",
                                        "PATH_TO_ORDER" => SITE_DIR . "personal/order/make/",
                                        "SHOW_DELAY" => "N",
                                        "SHOW_NOTAVAIL" => "N",
                                        "SHOW_SUBSCRIBE" => "N",
                                        "SHOW_IMAGE" => "Y",
                                        "SHOW_PRICE" => "Y",
                                        "SHOW_SUMMARY" => "Y",
                                        "HIDE_ON_BASKET_PAGES" => "N"
                                    ),
                                    false
                                ); ?>
                            </div>

                            <div class="header__lk">
                                <? $frame = new BufferArea("mobile_lk");
                                $frame->begin(); ?>
                                <? if ($USER->IsAuthorized()): ?>
                                    <a href="/cabinet/" class="title">
                                        <svg class='i-icon'>
                                            <use xlink:href='#lk'/>
                                        </svg>
                                    </a>
                                <? else: ?>
                                    <a href="#" data-toggle="modal" data-target="#enterModal">
                                        <svg class='i-icon'>
                                            <use xlink:href='#lk'/>
                                        </svg>
                                    </a>
                                <? endif; ?>
                                <? $frame->beginStub() ?>
                                <a href="#" data-toggle="modal" data-target="#enterModal">
                                    <svg class='i-icon'>
                                        <use xlink:href='#lk'/>
                                    </svg>
                                </a>
                                <? $frame->end(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header-nav" data-fixed-header>
                <div class="container">
                    <div class="header-nav__inner">
                        <a href="/" class="header-nav__logo">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/images/logo_paolo_conte.svg" alt="Paolo Conte">
                        </a>

                        <? $APPLICATION->IncludeComponent(
                            "citfact:elements.list",
                            "menu_main",
                            Array(
                                "IBLOCK_ID" => 16,
                                "IBLOCK_CATALOG_ID" => 10,
                                "PROPERTY_CODES" => array('LINK', 'CATALOG_SECTION'),
                                "PAGE" => $cur_page,
                                "PAGE_NO_INDEX" => $cur_page_no_index,
                                "CACHE_TIME" => 86400000,
                                "CACHE_TYPE" => 'A',
                            )
                        ); ?>

                        <a href="#" class="header__tel b24-web-form-popup-btn-16">
                            <? $APPLICATION->IncludeComponent("articul.geolocation.city_current", "phone", array(), false); ?>
                        </a>

                        <div class="header__actions">
                            <div class="header__city header__city--fixed">
                                <a href="#" class="" data-toggle="modal" data-target="#cityModal">
                                    <svg class='i-icon'>
                                        <use xlink:href='#geotag'/>
                                    </svg>
                                </a>
                            </div>
                            <div class="header__search">
                                <div class="search-line">
                                    <? /*<input type="text" placeholder="Искать товар или магазин">*/ ?>
                                    <? $APPLICATION->IncludeComponent(
                                        "bitrix:search.form",
                                        ".default",
                                        array(
                                            "PAGE" => "#SITE_DIR#search/"
                                        ),
                                        false
                                    ); ?>
                                </div>

                                <div class="search-btn no-select to-search">
                                    <svg class='i-icon'>
                                        <use xlink:href='#search'/>
                                    </svg>
                                </div>
                            </div>

                            <div class="header-cart">
                                <? $APPLICATION->IncludeComponent(
                                    "bitrix:sale.basket.basket.line",
                                    ".default",
                                    array(
                                        "PATH_TO_BASKET" => SITE_DIR . "cabinet/basket/",
                                        "SHOW_NUM_PRODUCTS" => "Y",
                                        "SHOW_TOTAL_PRICE" => "N",
                                        "SHOW_EMPTY_VALUES" => "N",
                                        "SHOW_PERSONAL_LINK" => "N",
                                        "PATH_TO_PERSONAL" => SITE_DIR . "personal/",
                                        "SHOW_AUTHOR" => "N",
                                        "PATH_TO_REGISTER" => SITE_DIR . "login/",
                                        "PATH_TO_PROFILE" => SITE_DIR . "personal/",
                                        "SHOW_PRODUCTS" => "Y",
                                        "POSITION_FIXED" => "N",
                                        "COMPONENT_TEMPLATE" => ".default",
                                        "PATH_TO_ORDER" => SITE_DIR . "personal/order/make/",
                                        "SHOW_DELAY" => "N",
                                        "SHOW_NOTAVAIL" => "N",
                                        "SHOW_SUBSCRIBE" => "N",
                                        "SHOW_IMAGE" => "Y",
                                        "SHOW_PRICE" => "Y",
                                        "SHOW_SUMMARY" => "Y",
                                        "HIDE_ON_BASKET_PAGES" => "N"
                                    ),
                                    false
                                ); ?>
                            </div>

                            <div class="header__lk">
                                <? $frame = new BufferArea("lk");
                                $frame->begin(); ?>
                                <? if ($USER->IsAuthorized()): ?>
                                    <a href="/cabinet/" class="title">
                                        <svg class='i-icon'>
                                            <use xlink:href='#lk'/>
                                        </svg>
                                    </a>
                                <? else: ?>
                                    <a href="#" data-toggle="modal" data-target="#enterModal">
                                        <svg class='i-icon'>
                                            <use xlink:href='#lk'/>
                                        </svg>
                                    </a>
                                <? endif; ?>
                                <? $frame->beginStub() ?>
                                <a href="#" data-toggle="modal" data-target="#enterModal">
                                    <svg class='i-icon'>
                                        <use xlink:href='#lk'/>
                                    </svg>
                                </a>
                                <? $frame->end(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="header-m" data-menu="burger">
            <div class="header-m__inner">
                <div class="header-m__close" data-menu-close>
                    <div class="plus plus--cross"></div>
                </div>
                <div class="header-m__title">
                    <a href="#" class="header-m__city" data-toggle="modal" data-target="#cityModal">
                        <? $APPLICATION->IncludeComponent("articul.geolocation.city_current", "desktop", array(), false); ?>
                    </a>
                </div>

                <div class="header-m__items">
                    <? $APPLICATION->IncludeComponent(
                        "citfact:elements.list",
                        "mobile_menu_main",
                        Array(
                            "IBLOCK_ID" => 16,
                            "IBLOCK_CATALOG_ID" => 10,
                            "PROPERTY_CODES" => array('LINK', 'CATALOG_SECTION'),
                            "PAGE" => $cur_page,
                            "PAGE_NO_INDEX" => $cur_page_no_index,
                            "CACHE_TIME" => 86400000,
                            "CACHE_TYPE" => 'A',
                        )
                    ); ?>
                    <? $APPLICATION->IncludeComponent(
                        "citfact:elements.list",
                        "mobile_menu_footer",
                        Array(
                            "IBLOCK_ID" => 15,
                            "PROPERTY_CODES" => array('LINK'),
                        )
                    ); ?>
                    <?/* if (!$USER->IsAuthorized()) { ?>
                        <div class="header-m-cart">
                            <div class="header-m-cart__inner">
                                <a href="/cabinet/auth/" class="header-cart__link">
                                    <svg class='i-icon'>
                                        <use xlink:href='#lk'/>
                                    </svg>
                                </a>

                                <a href="/cabinet/auth/">
                                    <span>Вход</span>
                                </a>
                            </div>
                        </div>
                    <? }*/ ?>


                    <div class="header-m-tel">
                        <div class="header-m-tel__inner">
                            <span><a href="tel:+7-499-350-71-38">+7 (499) 350 71 38</a></span>

                            <a href="#" class="b24-web-form-popup-btn-16">
                                Заказать звонок
                            </a>
                        </div>
                    </div>

                    <div class="header-m-social">
                       <? $APPLICATION->IncludeFile(
                            SITE_DIR . "include/header-m_social_links.php",
                            Array(),
                            Array("MODE" => "text")
                        ); ?>
                    </div>

                </div>
            </div>
        </div>

        <div class="header-m-mask" data-menu-mask></div>

        <main class="<?=($cabinet_basket_page == true) ? 'basket-page' : ''?>">
            <? if ($main_page == false): ?>
                <? if (($catalog_page == false) || (($catalog_page == true) && ($catalogMode == 'ELEMENT'))): ?>
                    <div class="page-top">
                        <div class="container">
                            <div class="page-top__inner">
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
                            </div>
                        </div>
                    </div>
                <? endif; ?>
            <? endif; ?>

            <? if ($cabinet_page == true) { ?>
                <!-- Оригинальный заголовок для десктопа -->
                <div class="container desktop">
                    <div class="title-page <? echo ($cabinet_basket_page == true) ? 'title-page--cart' : ''; ?>">
                        <? if ($cabinet_basket_page == true) { ?>
                            <div class="title-page__placeholder"></div>
                        <? } ?>
                        <h1 class="title-1"><? $APPLICATION->ShowTitle(false); ?></h1>
                        <? if ($cabinet_basket_page == true) { ?>
                            <div class="title-page__btn" id="basket_products"></div>
                        <? } ?>
                    </div>
                </div>
                <!-- ================= -->
            <? } ?>

            <? if ($cabinet_page == true) { ?>
            <div class="container">
                <div class="<?=(!$cabinet_basket_page) ? 'aside' : ''?>">
                    <? if (!$cabinet_basket_page) { ?>
                    <div class="aside__sidebar">
                        <? if ($cabinet_page == true) { ?>
                            <? if ($USER->IsAuthorized()): ?>
                                <?
                                $rsUser = $USER->GetByID($USER->GetID());
                                $arUser = $rsUser->Fetch();
                                ?>

                                <div class="aside-nav-profile" id="aside-nav-profile-id">
                                    <? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("aside-nav-profile-id"); ?>
                                    <a href="#" class="aside-nav-profile__inner">
                                        <svg class='i-icon'>
                                            <use xlink:href='#lk'/>
                                        </svg>

                                        <span><? echo $USER->GetFullName() . " (" . $USER->GetLogin() . ")"; ?></span>
                                    </a>

                                    <a href="<? echo $APPLICATION->GetCurPageParam("logout=yes", array(
                                        "login",
                                        "logout",
                                        "register",
                                        "forgot_password",
                                        "change_password")); ?>"
                                       class="aside-nav__link aside-nav__link--logout"
                                       title="Выход">
                                        Выход
                                        <svg class='i-icon'>
                                            <use xlink:href='#arrow-lk'/>
                                        </svg>
                                    </a>
                                    <? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("aside-nav-profile-id", ""); ?>
                                </div>
                            <? endif; ?>

                            <? $APPLICATION->IncludeComponent(
                                "citfact:elements.list",
                                "menu_cabinet",
                                Array(
                                    "IBLOCK_ID" => 19,
                                    "PROPERTY_CODES" => array('LINK', 'BLOCK', 'SHOW_NOT_AUTH'),
                                    "FILTER" => array(),
                                    "AUTH" => ($USER->IsAuthorized() ? 'Y' : 'N'),
                                    "PAGE" => $cur_page,
                                    "PAGE_NO_INDEX" => $cur_page_no_index,
                                )
                            ); ?>
                        <? } ?>
                    </div>
                    <!-- Дублированный заголовок для мобилок -->
                    <div class="container mobile">
                        <div class="title-page <? echo ($cabinet_basket_page == true) ? 'title-page--cart' : ''; ?>">
                            <? if ($cabinet_basket_page == true) { ?>
                                <div class="title-page__placeholder"></div>
                            <? } ?>
                            <h1 class="title-1"><? $APPLICATION->ShowTitle(false); ?></h1>
                            <? if ($cabinet_basket_page == true) { ?>
                                <div class="title-page__btn" id="basket_products_mobile"></div>
                            <? } ?>
                        </div>
                    </div>
                    <!-- ================= -->
                    <? } ?>

                    <div class="<?=(!$cabinet_basket_page) ? 'aside__main' : ''?>">
                        <? } elseif ($left_about == true) { ?>
                        <?/*<!-- Оригинальный заголовок для десктопа -->
                        <div class="container desktop">
                            <div class="title-page">
                                <div class="title-page__placeholder"></div>
                                <h1 class="title-1"><? $APPLICATION->ShowTitle(false); ?></h1>
                            </div>
                        </div>
                        <!-- ================= -->*/?>

                        <div class="container">
                            <div class="aside">
                                <div class="aside__sidebar">
                                    <? $APPLICATION->IncludeComponent(
                                        "citfact:elements.list",
                                        "menu_cabinet",
                                        Array(
                                            "IBLOCK_ID" => 15,
                                            "PROPERTY_CODES" => array('LINK'),
                                            "FILTER" => array("SECTION_CODE" => 'about'),
                                            "PAGE" => $cur_page,
                                            "PAGE_NO_INDEX" => $cur_page_no_index,
                                        )
                                    ); ?>
                                </div>

                                <!-- Дублированный заголовок для мобилок -->
                                <div class="container mobile">
                                    <div class="title-page">
                                        <div class="title-page__placeholder"></div>
                                        <h1 class="title-1"><? $APPLICATION->ShowTitle(false); ?></h1>
                                    </div>
                                </div>
                                <!-- ================= -->

                                <div class="aside__main">
                                    <!-- Оригинальный заголовок для десктопа -->
                                    <div class="container desktop" style="padding-left:0;">
                                        <div style="text-align:left;" class="title-page <? echo ($cabinet_basket_page == true) ? 'title-page--cart' : ''; ?>">
                                            <? if ($cabinet_basket_page == true) { ?>
                                                <div class="title-page__placeholder"></div>
                                            <? } ?>
                                            <h1 class="title-1"><? $APPLICATION->ShowTitle(false); ?></h1>
                                            <? if ($cabinet_basket_page == true) { ?>
                                                <div class="title-page__btn" id="basket_products"></div>
                                            <? } ?>
                                        </div>
                                    </div>
                                    <!-- ================= -->
                                    <? } elseif ($left_help == true) { ?>
                                    <!-- Оригинальный заголовок для десктопа -->
                                    <div class="container desktop">
                                        <div class="title-page">
                                            <div class="title-page__placeholder"></div>
                                            <h1 class="title-1"><? $APPLICATION->ShowTitle(false); ?></h1>
                                        </div>
                                    </div>
                                    <!-- ================= -->

                                    <div class="container">
                                        <div class="aside">
                                            <div class="aside__sidebar">
                                                <? $APPLICATION->IncludeComponent(
                                                    "citfact:elements.list",
                                                    "menu_cabinet",
                                                    Array(
                                                        "IBLOCK_ID" => 15,
                                                        "PROPERTY_CODES" => array('LINK'),
                                                        "FILTER" => array("SECTION_CODE" => 'help'),
                                                        "PAGE" => $cur_page,
                                                        "PAGE_NO_INDEX" => $cur_page_no_index,
                                                    )
                                                ); ?>
                                            </div>

                                            <!-- Дублированный заголовок для мобилок -->
                                            <div class="container mobile">
                                                <div class="title-page">
                                                    <div class="title-page__placeholder"></div>
                                                    <h1 class="title-1"><? $APPLICATION->ShowTitle(false); ?></h1>
                                                </div>
                                            </div>
                                            <!-- ================= -->

                                            <div class="aside__main">
                                                <? } elseif ($catalog_page == true) { ?>
                                                <? } elseif ($register_page == true) { ?>
                                                    <div class="container">
                                                        <div class="title-page">
                                                            <div class="title-page__placeholder"></div>
                                                            <h1 class="title-1"><? $APPLICATION->ShowTitle(false); ?></h1>
                                                        </div>
                                                    </div>
                                                <? }
                                                elseif ($search_page == true) { ?>
                                                <div class="container">
                                                    <div class="title-page">
                                                        <div class="title-page__placeholder"></div>
                                                        <h1 class="title-1"><? $APPLICATION->ShowTitle(false); ?></h1>
                                                    </div>
                                                </div>
                                                <div class="container b-static">
                                                    <? } elseif ($shops_page == true) { ?>

                                                    <? } elseif ($main_page == true) { ?>

                                                    <? }
                                                    else { ?>
                                                    <div class="container">
                                                        <? } ?>
