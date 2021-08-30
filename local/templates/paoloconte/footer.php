<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Composite\BufferArea;

if ($_REQUEST['PAGEN_3'] || $_REQUEST['PAGEN_2']) {
    $titleH1 = $APPLICATION->GetTitle();
    $titleForDescr = mb_strtolower($titleH1);
    $APPLICATION->SetPageProperty('title', $titleH1 . ' - страница ' . $_REQUEST['PAGEN_3'] . ' каталога интернет-магазина Paolo Conte ');
    $APPLICATION->SetPageProperty('description', 'Предлагаем купить ' . $titleForDescr . ' от Paolo Conte. Каталог интернет-магазина, страница '. $_REQUEST['PAGEN_3']);
} elseif ($_REQUEST['PAGEN_2']) {
    $titleH1 = $APPLICATION->GetTitle();
    $titleForDescr = mb_strtolower($titleH1);
    $APPLICATION->SetPageProperty('title', $titleH1 . ' - страница ' . $_REQUEST['PAGEN_3'] . ' каталога интернет-магазина Paolo Conte ');
    $APPLICATION->SetPageProperty('description', 'Предлагаем купить ' . $titleForDescr . ' от Paolo Conte. Каталог интернет-магазина, страница '. $_REQUEST['PAGEN_3']);
}

$pageTitle = $APPLICATION->GetProperty("title");
if(strpos($pageTitle,'()') == true){
    $pageTitle= str_replace('()','', $pageTitle);
    $APPLICATION->SetPageProperty("title", $pageTitle);
}

?>

<? if ($cabinet_page == true) { ?>
    </div> <!-- class="aside__main" -->
    </div> <!-- class="aside" -->
    </div> <!-- class="container" -->
<? } elseif ($left_about == true) { ?>
    </div> <!-- class="aside__main" -->
    </div> <!-- class="aside" -->
    </div> <!-- class="container" -->
<? } elseif ($left_help == true) { ?>
    </div> <!-- class="aside__main" -->
    </div> <!-- class="aside" -->
    </div> <!-- class="container" -->
<? } elseif ($catalog_page == true) { ?>

<? } elseif ($register_page == true) { ?>
    </div> <!-- class="container b-static" -->
<? } elseif ($search_page == true) { ?>

<? } elseif ($shops_page == true) { ?>

<? } elseif ($main_page == true) { ?>

<? } else { ?>
    </div> <!-- class="container b-static" -->
<? } ?>

<!--modals-->
<div class="modal-overlay basket-modal-wrap">
    <div class="modal-window">
        <div class="modal-basket modal-new">
            <div class="title-1">
                Модель добавлена в корзину
            </div>

            <span class="close close-moved-pannel"></span>

            <div class="modal-basket__info">
                <div class="modal-basket__img">
                    <img id="basket-modal-wrap_img" src="<?= SITE_TEMPLATE_PATH . '/images/no_photo.png'; ?>" alt="">
                </div>

                <div class="modal-basket__params">
                    <div id="basket-modal-wrap_name"></div>
                    <div id="basket-modal-wrap_color-title">Цвет: <span id="basket-modal-wrap_color"></span></div>
                    <div id="basket-modal-wrap_size-title">Размер: <span id="basket-modal-wrap_size"></span></div>
                </div>
            </div>

            <div class="modal-btns">
                <a href=" javascript:void(0);"
                   id="btn-close-window"
                   class="btn btn--transparent window-modal_close">
                    <span>Продолжить покупки</span>
                </a>

                <a href="/cabinet/basket/"
                   class="btn btn--black">
                    Оформить заказ
                </a>
            </div>

            <div class="interes-you">
                <div id='idSale' data-retailrocket-markup-block="5899b49c5a6588415cb8f1a9" data-product-ids=""></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade priceModal" id="priceModal" tabindex="-1" role="dialog" aria-label="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <div class="modal-price modal-new"></div>
        </div>
    </div>
</div>

<div class="modal fade sizeModal" id="sizeModal" tabindex="-1" role="dialog" aria-label="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <div class="modal-size modal-new"></div>
        </div>
    </div>
</div>

<div class="modal fade cityModal" id="cityModal" tabindex="-1" role="dialog" aria-label="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <? $APPLICATION->IncludeComponent(
                "citfact:elements.list",
                "list_cities_modal_new_var",
                Array(
                    'CUR_PAGE' => $APPLICATION->GetCurPageParam(), // NOTE: Fix caching
                    "IBLOCK_ID" => 20,
                    "PROPERTY_CODES" => array('OBLAST', 'MAIN'),
                    "CURRENT_CITY_ID" => $_SESSION["CITY_ID"],
                    "SHOW_WINDOW" => $showWindow,
                )
            ); ?>
        </div>
    </div>
</div>
<?if(stripos($page = $APPLICATION->GetCurPage(), 'vacancy')):?>
<div class="modal fade cityModal" id="cityModalInVacancy" tabindex="-1" role="dialog" aria-label="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <? $APPLICATION->IncludeComponent(
                "citfact:elements.list",
                "list_cities_modal_invacancy",
                array(
                    'CUR_PAGE' => $APPLICATION->GetCurPageParam(), // NOTE: Fix caching
                    "IBLOCK_ID" => 20,
                    "PROPERTY_CODES" => array('OBLAST', 'MAIN'),
                    "CURRENT_CITY_ID" => $_SESSION["CITY_ID"],
                )
            ); ?>
        </div>
    </div>
</div>
<?endif?>
<? // Быстрый просмотр?>
<div class="modal fade fastViewModal" id="fastViewModal" tabindex="-1" role="dialog" aria-label="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<? // Изменение адреса доставки в ЛК ?>
<div class="modal fade" id="editAddressModal" tabindex="-1" role="dialog" aria-label="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<div class="modal fade callbackModal" id="callbackModal" tabindex="-1" role="dialog" aria-label="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <div class="modal-body">
                <div class="modal-title">
                    Заказ обратного звонка
                </div>
                <? $APPLICATION->IncludeComponent(
                    "citfact:form.ajax",
                    "callback",
                    Array(
                        "IBLOCK_ID" => 27,
                        "SHOW_PROPERTIES" => array(
                            'USERNAME' => array('type' => 'text', 'placeholder' => 'Введите ваше имя', 'required' => 'Y'),
                            'USERPHONE' => array('type' => 'text', 'placeholder' => 'Введите ваш телефон', 'required' => 'Y'),
                        ),
                        //"EVENT_MESSAGE_ID" => Array("32"), // тут id шаблона, а не события
                        "EVENT_NAME" => 'CALLBACK_FORM',
                        "SUCCESS_MESSAGE" => 'Ваша заявка принята. Мы перезвоним вам в ближайшее время.',
                        "ELEMENT_ACTIVE" => 'Y'
                    )
                ); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade reviewModal" id="reviewModal" tabindex="-1" role="dialog" aria-label="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <div class="modal-body">
                <div class="modal-title">
                    Оставить отзыв об интернет-магазине
                </div>
                <? $APPLICATION->IncludeComponent(
                    "citfact:form.ajax",
                    "review",
                    Array(
                        "IBLOCK_ID" => 25,
                        "SHOW_PROPERTIES" => array(
                            'USERNAME' => array('type' => 'text', 'placeholder' => 'Введите ваше имя', 'required' => 'Y'),
                            'USERPHONE' => array('type' => 'text', 'placeholder' => 'Введите ваш телефон', 'required' => 'Y'),
                            'REVIEW_TEXT' => array('type' => 'textarea', 'placeholder' => 'Введите текст отзыва', 'required' => 'Y'),
                        ),
                        //"EVENT_MESSAGE_ID" => Array("32"), // тут id шаблона, а не события
                        "EVENT_NAME" => 'REVIEW_SITE_FORM',
                        "SUCCESS_MESSAGE" => 'Спасибо за отзыв! Мы покажем его на сайте после проверки модератором.',
                        "ELEMENT_ACTIVE" => 'N'
                    )
                ); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade enterModal" id="enterModal" tabindex="-1" role="dialog" aria-label="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>

            <div class="modal-body modal-new modal-new--auth">
                <div class="title-4">
                    ВХОД
                </div>
                <? $APPLICATION->IncludeComponent(
                    "citfact:authorize.ajax",
                    "popup",
                    Array(
                        "REDIRECT_TO" => '',
                        "FORM_ID" => 'popup'
                    )
                ); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="toFavoriteModal" tabindex="-1" role="dialog" aria-label="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-basket modal-new">
            <button type="button" class="close" data-dismiss="modal"></button>

            <div class="modal-body">
                <div class="title-1">
                    Товар добавлен в список желаний
                </div>

                <div class="modal-basket__info">
                    <div class="modal-basket__img image">
                        <img src="<?= SITE_TEMPLATE_PATH . '/images/content/catalog-item-3.jpg'; ?>" alt="Фото товара">
                    </div>

                </div>
                <div class="modal-basket__info">
                    <div class="modal-basket__params">
                        <div id="basket-modal-wrap_info">Вы можете найти выбранный Вами товар в разделе «<a href="/cabinet/favorites/">Список желаний</a>» в личном кабинете.</div>
                    </div>
                </div>

                <div class="modal-btns">
                    <a href="/cabinet/favorites/" class="btn btn--transparent window-modal_close">Просмотреть список желаний</a>
                    <a href="#" class="btn btn--black" data-dismiss="modal">Продолжить покупки</a>
                </div>
                
                <div class="interes-you">
                    <div id='idSaleInteres' data-retailrocket-markup-block="5899b49c5a6588415cb8f1a9" data-product-ids=""></div>
                </div>                

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="chooseSizeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>

            <div class="modal-body">
                <div class="modal-title">
                    Выберите размер
                </div>
                <div>
                    Чтобы добавить товар в корзину, сначала выберите размер товара.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="offerInbasketModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 99999;">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>

            <div class="modal-body">
                <div class="title-1">
                    Товар уже есть в корзине
                </div>
                <div class="modal-text">
                    Вы уже добавили этот товар в корзину.
                </div>
            </div>
        </div>
    </div>
</div>
<noindex>
<div class="modal fade franchiseModal" id="franchiseModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-new">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <div class="modal-body">
                <div class="title-1">
                    Анкета франшизы
                </div>
                <?$APPLICATION->IncludeComponent(
                    "bitrix:form.result.new",
                    "franchise",
                    Array(
                        "COMPONENT_TEMPLATE" => ".default",
                        "WEB_FORM_ID" => "1",
                        "IGNORE_CUSTOM_TEMPLATE" => "N",
                        "USE_EXTENDED_ERRORS" => "N",
                        "SEF_MODE" => "N",
                        "VARIABLE_ALIASES" => Array("WEB_FORM_ID"=>"WEB_FORM_ID","RESULT_ID"=>"RESULT_ID"),
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "3600",
                        "LIST_URL" => "result_list.php",
                        "EDIT_URL" => "result_edit.php",
                        "SUCCESS_URL" => "",
                        "CHAIN_ITEM_TEXT" => "",
                        "CHAIN_ITEM_LINK" => "",
                        "AJAX_MODE" => 'Y'
                    )
                );?>
            </div>
        </div>
    </div>
</div>
<!--end modals-->
</noindex>

</main>
<?
$uri = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->getRequestUri();
$isFiltered = preg_match('/set_filter=y/', $uri);

$isSeoMetaPage = false;

try {
    // проверка является ли страница сгенерированной СЕО модулем
    if(
        IsModuleInstalled('sotbit.seometa')
        && \Bitrix\Main\Loader::includeModule('sotbit.seometa')
        && is_callable(['\Sotbit\Seometa\SeometaUrlTable', 'getByRealUrl']) // метод проверки актуальный для 1.4.9 доступен
    ){
        $result = \Sotbit\Seometa\SeometaUrlTable::getByRealUrl($uri);
        $isSeoMetaPage = ( $result && !empty($result['NEW_URL']));
    }
} catch (\Exception $e) {}
?>
<?php
if($cabinet_basket_page){
    ?>
    <div class="container">
<?php
//include_once($_SERVER['DOCUMENT_ROOT'] . '/cabinet/basket/bigdata.php');
}
?></div>
<footer class="footer">
    <? if ($catalog_page == true) { ?>
        <div class="footer-seo">
            <div class="container">
                <div class="footer-seo__inner">
                    <?php
                    if(!$isSeoMetaPage){
                        $APPLICATION->ShowViewContent('catalog_section_seotext');
                    }
                    global $sotbitSeoMetaAddDesc;
                    if (!empty($sotbitSeoMetaAddDesc)){
                        echo $sotbitSeoMetaAddDesc;
                    }
                    ?>
                </div>
            </div>
        </div>
    <? } ?>

    <div class="container">
        <div class="footer-nav"  itemscope itemtype="http://www.schema.org/SiteNavigationElement">
            <div class="footer-nav__item footer-nav__item--form desktop">
                <? $APPLICATION->IncludeComponent(
                "citfact:form.ajax",
                "subscribe_footer",
                Array(
                    "IBLOCK_ID" => 38,
                    "SHOW_PROPERTIES" => array(
                        'USERNAME' => array('type' => 'text', 'placeholder' => 'Введите ваше имя', 'required' => 'Y'),
                        'EMAIL' => array('type' => 'text', 'placeholder' => 'Введите ваш e-mail', 'required' => 'Y'),
                        'HASH' => array('type' => 'hidden', 'value' => ''),
                    ),
                    //"EVENT_MESSAGE_ID" => Array("32"), // тут id шаблона, а не события
                    "EVENT_NAME" => 'INDEX_PROMO_FORM',
                    "SUCCESS_MESSAGE" => 'Ваша заявка на подписку принята.',
                    "ELEMENT_ACTIVE" => 'Y',
                    "GENERATE_COUPON" => "Y",
                )
            ); ?>
                <div class="footer-form__pp">
                    <? $APPLICATION->IncludeFile(
                        SITE_DIR . "/include/oferta_inorder.php",
                        Array(),
                        Array("MODE" => "text")
                    ); ?>
                </div>
            </div>
            <? $APPLICATION->IncludeComponent(
                "citfact:elements.list",
                "menu_footer",
                Array(
                    "IBLOCK_ID" => 15,
                    "PROPERTY_CODES" => array('LINK'),
                )
            ); ?>
            <div class="footer-social">
                <? $APPLICATION->IncludeFile(
                    SITE_DIR . "include/footer_social_links.php",
                    Array(),
                    Array("MODE" => "text")
                ); ?>
            </div>
        </div>

        <div class="footer__bottom">
            <div class="footer__pay">
                <img src="/local/templates/paoloconte/images/icons/mastercard.svg" alt="">
                <img src="/local/templates/paoloconte/images/icons/maestro.svg" alt="">
                <img src="/local/templates/paoloconte/images/icons/visa.svg" alt="">
                <img src="/local/templates/paoloconte/images/icons/mir.svg" alt="">
                <img src="/local/templates/paoloconte/images/icons/halva.svg" alt="">
            </div>

            <div class="footer__copyright">
                © 2009–<?= date('Y'); ?> Paolo Conte
            </div>
        </div>
    </div>
</footer>
<div class="loader">
    <div class="loader_inner">
        <img src="/local/templates/paoloconte/images/loader.gif" alt="">
    </div>
</div>
</div>
</div>

<!-- Yandex.Metrika counter -->
<script>
  (function (d, w, c) {
    (w[c] = w[c] || []).push(function () {
      try {
        w.yaCounter209275 = new Ya.Metrika({
          id: 209275,
          clickmap: true,
          trackLinks: true,
          accurateTrackBounce: true,
          webvisor: true,
          ecommerce: "dataLayer"
        });
      } catch (e) {
      }
    });

    var n = d.getElementsByTagName("script")[0],
      s = d.createElement("script"),
      f = function () {
        n.parentNode.insertBefore(s, n);
      };
    s.async = true;
    s.src = "https://mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
      d.addEventListener("DOMContentLoaded", f, false);
    } else {
      f();
    }
  })(document, window, "yandex_metrika_callbacks");
</script>

<?
$frame = new BufferArea("ya_counter");
$frame->begin('');

global $USER;
if ($USER->IsAuthorized()) { ?>
    <script>
      window.onload = function () {
        yaCounter209275.setUserID('<?=$USER->GetID();?>');
      };

      dataLayer.push({'USER_ID_CUSTOM': '<?=$USER->GetID();?>'});
    </script>
    <?
}
$frame->end();
?>

<noscript>
    <div><img src="https://mc.yandex.ru/watch/209275" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
<!-- /Yandex.Metrika counter -->

<script>window.dataLayer = window.dataLayer || [];</script>
<script>
  /* <![CDATA[ */
  var google_conversion_id = 919796283;
  var google_custom_params = window.google_tag_params;
  var google_remarketing_only = true;
  /* ]]> */
</script>
<script id="bx24_form_link" data-skip-moving="true">
  (function (w, d, u, b) {
    w['Bitrix24FormObject'] = b;
    w[b] = w[b] || function () {
      arguments[0].ref = u;
      (w[b].forms = w[b].forms || []).push(arguments[0])
    };
    if (w[b]['forms']) return;
    s = d.createElement('script');
    r = 1 * new Date();
    s.async = 1;
    s.src = u + '?' + r;
    h = d.getElementsByTagName('script')[0];
    h.parentNode.insertBefore(s, h);
  })(window, document, 'https://paoloconte.bitrix24.ru/bitrix/js/crm/form_loader.js', 'b24form');

  b24form({"id": "16", "lang": "ru", "sec": "u85ysf", "type": "link", "click": ""});
</script>
<script>
(function(w,d,u){
var s=d.createElement('script');s.async=true;s.src=u+'?'+(Date.now()/60000|0);
var h=d.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);
})(window,document,'https://cdn.bitrix24.ru/b261707/crm/tag/call.tracker.js');
</script>
<script src="//www.googleadservices.com/pagead/conversion.js"></script>
<script src="/bitrix/js/socialservices/ss.js"></script>
<script src="<?=SITE_TEMPLATE_PATH;?>/javascript/cookie.js"></script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt=""
             src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/919796283/?value=1&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>
<script>
  (function (a, e, c, f, g, h, b, d) {
    var k = {ak: "919796283", cl: "gUhGCJ_Iy3MQu_TLtgM", autoreplace: "+7 (499) 350-7138"};
    a[c] = a[c] || function () {
      (a[c].q = a[c].q || []).push(arguments)
    };
    a[g] || (a[g] = k.ak);
    b = e.createElement(h);
    b.async = 1;
    b.src = "//www.gstatic.com/wcm/loader.js";
    d = e.getElementsByTagName(h)[0];
    d.parentNode.insertBefore(b, d);
    a[f] = function (b, d, e) {
      a[c](2, b, k, d, null, new Date, e)
    };
    a[f]()
  })(window, document, "_googWcmImpl", "_googWcmGet", "_googWcmAk", "script");
</script>
<script>
  (function (a, e, c, f, g, h, b, d) {
    var k = {ak: "919796283", cl: "iaLNCNWBsnMQu_TLtgM", autoreplace: "+7 (800) 333 70 77"};
    a[c] = a[c] || function () {
      (a[c].q = a[c].q || []).push(arguments)
    };
    a[g] || (a[g] = k.ak);
    b = e.createElement(h);
    b.async = 1;
    b.src = "//www.gstatic.com/wcm/loader.js";
    d = e.getElementsByTagName(h)[0];
    d.parentNode.insertBefore(b, d);
    a[f] = function (b, d, e) {
      a[c](2, b, k, d, null, new Date, e)
    };
    a[f]()
  })(window, document, "_googWcmImpl", "_googWcmGet", "_googWcmAk", "script");
</script>
<script>
    $('.modal').on('hidden.bs.modal', function (e) {
        if($('.modal').hasClass('in')) {
            $('body').addClass('modal-open');
        }
    });
</script>
</body>
</html>
