<?
use Citfact\ProductAvailabilityBuy;
use Citfact\Tools;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

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

$this->setFrameMode(true);
$templateLibrary = array('popup');
$isTabReserveActive = ($_REQUEST["tab_reserve"] == "Y");
$arValidCities = array(
    "anapa",
    "armavir",
    "cheboksary",
    "chelyabinsk",
    "ekaterinburg",
    "groznyy",
    "izhevsk",
    "krasnodar",
    "krasnoyarsk",
    "kaluga",
    "kotelniki",    
    "magnitogorsk",    
    "moscow",    
    "nizhniy-novgorod",
    "nizhnevartovsk",
    "novorossiysk",
    "novosibirsk",
    "omsk",
    "orel",
    "perm",
    "rostov-na-donu",
    "pushkino",
    "ryazan",
    "samara",
    "sankt-peterburg",
    "saratov",
    "saransk",
    "surgut",
    "tyumen",    
    "ufa",
);
$arStoresAllowed = array(
    51396,  //Магазин в ТЦ Подсолнух, г. Нижневартовск
    51404,  //Магазин в ТЦ Аура, г. Сургут
    51378, //Магазин в ТЦ Космопорт, г. Самара
    162369, //Магазин в ТЦ Амбар, г. Самара
    51377, //ТРЦ Парк Хаус, Paolo Conte обувь, г. Самара
    51469, //ТЦ Мега ИКЕА, Paolo Conte обувь, Самара
    46653, //ТЦ Гостиный двор, Paolo Conte обувь, г. Магнитогорск
    89508, //ТРЦ Тау Галерея, Paolo Conte обувь, г.Саратов
    51370, //Торгово-развлекательный центр КОЛИЗЕЙ, Paolo Conte обувь
    51367, //СБС Мега Молл, Paolo Conte обувь
    51395, //Торгово-развлекательный центр РИО, Paolo Conte обувь
    242200, //ТРК "Горизонт" Ростов-на-Дону, Paolo Conte обувь
    51391, //Магазин в ТЦ РИО, г. Калуга 
    51374, //Магазин в ТЦ Малина, г. Рязань 
    51373, //Магазин в ТЦ Малина, г. Рязань 
    51375, //Торгово-развлекательный центр ПРЕМЬЕР, Paolo Conte обувь, Рязань
    187832, //Магазин в ТЦ Метрополис, г. Москва 
    51347, //Магазин в ТЦ Охотный ряд, г. Москва
    51348, //Магазин в ТЦ Принц Плаза, г. Москва
    264308, //Магазин в ТЦ Авеню, г. Москва
    254636, //Магазин в ТЦ Саларис, г. Москва
    272352, //Магазин в ТЦ Калейдоскоп, г. Москва 
    111722, //Магазин в ТЦ Круг, г.Москва 
    244296, //Магазин Аутлет-Центр Белая дача, Котельники
    51410, //Уфа Торгово-развлекательный комплекс СЕМЬЯ, Paolo Conte обувь
    51400, //Магазин в ТЦ Центральный, г. Уфа
    51401, //Магазин в ТЦ Мега, г. Уфа
    51402, //Магазин в ТЦ Планета, г. Уфа
    51353, //ТРЦ COLUMBUS, Paolo Conte обувь, Москва
    51390, //ТРЦ 21 век, Paolo Conte обувь, г. Калуга 
    51369, //Магазин в ТЦ Фантастика, г. Нижний Новгород
    51408, //Магазин в ТЦ Седьмое небо, г. Нижний Новгород
    46654, //СТЦ Мега, Paolo Conte обувь, г. Омск
    51360, //ТЦ Июнь, Paolo Conte обувь, Красноярск
    71577, //ТРЦ Кристалл, Paolo Conte обувь, г. Тюмень
    51388, //ТЦ Талисман, Paolo Conte обувь, г. Ижевск
    51393, //ТЦ Мадагаскар, Paolo Conte обувь, г. Чебоксары
    70786, //МЦ Красная площадь, Paolo Conte обувь, г. Анапа  
    46650, //МЦ Красная площадь, Paolo Conte обувь, г. Армавир  
    55016, //Мегацентр «Красная Площадь»,  Paolo Conte обувь, г. Краснодар
    51368, //OZ МОЛЛ, Paolo Conte обувь, г. Краснодар
    51384, //ТРЦ Красная Площадь, Paolo Conte обувь, г. Новороссийск
    235948, //ТРК Европолис, Paolo Conte обувь, г. Санкт-Петербург  
    93360, //ТМК ГРИНН, Paolo Conte обувь,  г.Орел
    51415, //Торгово-развлекательный центр ГРАНД-ПАРК, г. Грозный
    51371, //Торгово-развлекательный комплекс СЕМЬЯ, Paolo Conte обувь, г. Пермь
    51392, //Торгово-развлекательный центр ГУДВИН Paolo Conte Обувь, г. Тюмень
    218954, //ТРЦ Пушкино Парк, Paolo Conte обувь, г. Пушкино
    168924, //МЕГА Белая Дача, Paolo Conte обувь, г. Котельники
    89644, //ТРЦ Алмаз, Paolo Conte обувь, Челябинск
    99634, //ТЦ Родник, Paolo Conte обувь, Челябинск
    70759, //ТРЦ Галерея, магазин обуви Paolo Conte, Новосибирск
    51364, //Торгово-развлекательный центр ТАНДЕМ, Paolo Conte обувь, Казань
    51354, //ТЦ Галерея, Санкт-Петербург
    314855, //ТРЦ Семеновский, Paolo Conte обувь, Москва
    51362, //ТРЦ Гринвич, Екатеринбург
);

$isReserveModalShow = false;

foreach($arResult['SHOPS'] as $arShop){
    if (in_array($arShop['ID'], $arStoresAllowed) && $arShop['PROPERTY_CITY_CODE'] == $_SESSION["CITY_CODE"]){
        //$isReserveModalShow = true; //#83353# отключен функционал "Отложить в магазине"
        break;
    }
}
?>

<? $frame = $this->createFrame()->begin(); ?>
<? $dir = $arResult["PATH"][0]["CODE"];
?>
<script>
  if (window.frameCacheVars !== undefined) {
    BX.addCustomEvent("onFrameDataReceived", function (json) {
      window.Application.Components.Main.labelcheck();
      window.Application.Components.Main.runStaticModals();
      window.Application.Components.Sliders.runAddDetailCarousel();
      window.Application.Components.Sliders.detailWatchedCarousel();
    });
  } else {
    BX.ready(function () {

    });
  }
</script>
<? $frame->end(); ?>

<?
$currencyList = '';
if (!empty($arResult['CURRENCIES'])) {
    $templateLibrary[] = 'currency';
    $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}
$templateData = array(
    'TEMPLATE_THEME' => $this->GetFolder() . '/themes/' . $arParams['TEMPLATE_THEME'] . '/style.css',
    'TEMPLATE_CLASS' => 'bx_' . $arParams['TEMPLATE_THEME'],
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList
);
unset($currencyList, $templateLibrary);

$strMainID = $this->GetEditAreaId($arResult['ID']);
$arItemIDs = array(
    'ID' => $strMainID,
    'PICT' => $strMainID . '_pict',
    'DISCOUNT_PICT_ID' => $strMainID . '_dsc_pict',
    'STICKER_ID' => $strMainID . '_sticker',
    'BIG_SLIDER_ID' => $strMainID . '_big_slider',
    'BIG_IMG_CONT_ID' => $strMainID . '_bigimg_cont',
    'SLIDER_CONT_ID' => $strMainID . '_slider_cont',
    'SLIDER_LIST' => $strMainID . '_slider_list',
    'SLIDER_LEFT' => $strMainID . '_slider_left',
    'SLIDER_RIGHT' => $strMainID . '_slider_right',
    'OLD_PRICE' => $strMainID . '_old_price',
    'PRICE' => $strMainID . '_price',
    'DISCOUNT_PRICE' => $strMainID . '_price_discount',
    'SLIDER_CONT_OF_ID' => $strMainID . '_slider_cont_',
    'SLIDER_LIST_OF_ID' => $strMainID . '_slider_list_',
    'SLIDER_LEFT_OF_ID' => $strMainID . '_slider_left_',
    'SLIDER_RIGHT_OF_ID' => $strMainID . '_slider_right_',
    'QUANTITY' => $strMainID . '_quantity',
    'QUANTITY_DOWN' => $strMainID . '_quant_down',
    'QUANTITY_UP' => $strMainID . '_quant_up',
    'QUANTITY_MEASURE' => $strMainID . '_quant_measure',
    'QUANTITY_LIMIT' => $strMainID . '_quant_limit',
    'BASIS_PRICE' => $strMainID . '_basis_price',
    'BUY_LINK' => $strMainID . '_buy_link',
    'ADD_BASKET_LINK' => $strMainID . '_add_basket_link',
    'BASKET_ACTIONS' => $strMainID . '_basket_actions',
    'NOT_AVAILABLE_MESS' => $strMainID . '_not_avail',
    'COMPARE_LINK' => $strMainID . '_compare_link',
    'PROP' => $strMainID . '_prop_',
    'PROP_DIV' => $strMainID . '_skudiv',
    'DISPLAY_PROP_DIV' => $strMainID . '_sku_prop',
    'OFFER_GROUP' => $strMainID . '_set_group_',
    'BASKET_PROP_DIV' => $strMainID . '_basket_prop',
);
$strObName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);
$templateData['JS_OBJ'] = $strObName;

$strTitle = (
isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"] != ''
    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
    : $arResult['NAME']
);
$strAlt = (
isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"] != ''
    ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
    : $arResult['NAME']
);
$name = (!empty($arResult['PROPERTIES']['NAIMENOVANIE_MARKETING']['VALUE']))
    ? $arResult['PROPERTIES']['NAIMENOVANIE_MARKETING']['VALUE']
    : $arResult["NAME"];

//product id for favorite and cart
$productId = '';
if (!empty($arResult['OFFERS'])) {
    reset($arResult['OFFERS']);
    $first = current($arResult['OFFERS']);
    $productIdToFav = $first['ID'];
} else {
    $productId = $arResult['ID'];
    $productIdToFav = $arResult['ID'];
}

//favorite and buy icon
if (!empty($arResult['CATALOG_IMG']['PHOTO'])) {
    $productPhoto = $arResult['CATALOG_IMG']['PHOTO'][0]['BIG'];
} else {
    $productPhoto = $arResult['NOPHOTO'];
}
?>

<?
$frame = $this->createFrame()->begin('');

$setRRPrice = 0;
if (!empty($arResult['NEW_PRICE'])) {
    $setRRPrice = str_replace(" ", "", $arResult['NEW_PRICE']);
} elseif (!empty($arResult['OLD_PRICE'])) {
    $setRRPrice = str_replace(" ", "", $arResult['OLD_PRICE']);
}
$isAvailable = false;
if (!empty($arResult['OFFERS'])){
    $isAvailable = $arResult['OFFERS_AMOUNT'][$productIdToFav] > 0;
} else {
    $isAvailable = !empty($arResult['STORES_AMOUNT']);
} ?>
<script type="text/javascript">
    (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
        retailrocket.products.post({
            "id": <?=$arResult['ID']?>,
            "name": "<?=$arResult['NAME']?>",
            "price": <?= $setRRPrice; ?>,
            "pictureUrl": "<?=URL_FULL_VERSION.$productPhoto?>",
            "url": "<?=URL_FULL_VERSION.$arResult['DETAIL_PAGE_URL']?>",
            "isAvailable": <?if ($arResult['PROPERTIES']['OFFERS_AMOUNT']['VALUE'] > 0){echo 'true';}else{echo 'false';}?>,
            "description": "",
            "categoryPaths": [<?=$arResult['ELEMENT_ALL_GROUPS'];?>],
            <?if(!empty($arResult['PROPERTIES']['CML2_MANUFACTURER']['VALUE'])){?>
            "vendor": "<?=$arResult['PROPERTIES']['CML2_MANUFACTURER']['VALUE']?>",
            <?}?>
            <?if(!empty($arResult['PROPERTIES']['NAIMENOVANIE_MARKETING']['VALUE'])){?>
            "model": "<?=$arResult['PROPERTIES']['NAIMENOVANIE_MARKETING']['VALUE']?>",
            <?}?>
            <?if(!empty($arResult['PROPERTIES']['STIL']['VALUE'])){?>
            "typePrefix": "<?=$arResult['PROPERTIES']['STIL']['VALUE']?>",
            <?}?>
            <?if(!empty($arResult['NEW_PRICE'])){?>
            "oldPrice": <?=str_replace(" ","",substr($arResult['OLD_PRICE'],0))?>,
            <?}else{?>
            "oldPrice": 0,
            <?}?>
            <?if(!empty($arResult['OTHER_COLORS'][$arResult['ID']]['COLOR']['NAME'])){?>
            "color": "<?=$arResult['OTHER_COLORS'][$arResult['ID']]['COLOR']['NAME']?>"
            <?}?>

        })
        rrApi.view(<?=$arResult['ID']?>);
    });

</script>
<? $frame->end(); ?>

<div class="container">
    <div class="product" itemscope itemtype="http://schema.org/Product">
        <div class="product__inner product__inner--top">
            <div class="product-info">
                <h1 itemprop="name"><?= $arResult['NAME'] ?></h1>
                <meta itemprop="description" content="<?= $arResult['PREVIEW_TEXT'] ? $arResult['PREVIEW_TEXT'] : $arResult['NAME'];?>">
                <? if (!empty($arResult['REVIEWS'])) {
                    $summStarsValue = 0;
                    $maxCountStars = 5;
                    $countStars = count($arResult['REVIEWS']);
                    foreach ($arResult['REVIEWS'] as $reviewItem) {
                        $summStarsValue = $summStarsValue + intval($reviewItem['PROPERTY_STARS_VALUE']); ?>
                        <div style="display: none;" itemprop="review" itemscope itemtype="http://schema.org/Review">
                            <span class="review-name" itemprop="author" content="<?= $reviewItem['USERNAME'] ?>"></span>
                            <meta itemprop="datePublished" content="<?= $reviewItem['DATE_CREATE_META'] ?>">
                            <meta itemprop="description" content="<?= $reviewItem["PROPERTY_MESSAGE_VALUE"] ?>">
                            <div style="display: none;" itemprop="reviewRating" itemscope
                                 itemtype="http://schema.org/Rating">
                                <meta itemprop="worstRating" content="1"/>
                                <span itemprop="ratingValue"
                                      content="<?= $reviewItem['PROPERTY_STARS_VALUE']; ?>"></span>
                                <span itemprop="bestRating" content="<?= $maxCountStars; ?>"></span>
                            </div>
                        </div>
                    <? } ?>

                    <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                        <span itemprop="ratingValue" content="<?= round($summStarsValue / $countStars); ?>"></span>
                        <span itemprop="reviewCount" content="<?= $countStars; ?>"></span>
                    </div>
                <? } ?>

                <a href="#go_review_block" class="product-info__rating">
                    <div class="rating_stars">
                        <? for ($i = 1; $i <= 5; $i++) { ?>
                            <? $checked = ($i == $arResult['RATING']) ? 'checked' : ''; ?>
                            <input type="radio" title="<?= $i ?>" value="<?= $i ?>" class="star"
                                   disabled="disabled" <?= $checked ?>/>
                        <? } ?>
                    </div>

                    <span>Оставить отзыв</span>
                </a>

            </div>
        </div>

        <div class="product__inner item">

            <div class="product-slider" >
                <div class="tag">
                    <? if ($arResult['PROPERTIES']['HIT']['VALUE'] != '') { ?>
                        <span class="tag__item tag__item--hit">HIT</span>
                    <? } ?>
                    <? if ($arResult['PROPERTIES']['NEW']['VALUE'] != '') { ?>
                        <span class="tag__item tag__item--new">NEW</span>
                    <? } ?>
                    <? if (false && $arResult['PROPERTIES']['SALE']['VALUE'] != '') { ?>
                        <span class="tag__item tag__item--sell"><?= $arResult['PROPERTIES']['SALE']['VALUE'] ?>%</span>
                    <? } ?>
                </div>

                <div class="product-slider-small">
                    <div class="product-slider__arrow product-slider__arrow--prev" data-detail-preview-prev></div>
                    <div class="swiper-container" <?= $dir == "odezhda" ? "data-detail-preview-clothes" : "data-detail-preview" ?>>
                        <div class="swiper-wrapper">
                            <? $countPhotos = count($arResult['CATALOG_IMG']['PHOTO']) + count($arResult['CATALOG_IMG']['360']); ?>
                            <? if ((!empty($arResult['CATALOG_IMG']['PHOTO']) || !empty($arResult['CATALOG_IMG']['360'])) && $countPhotos > 1) { ?>
                                <? foreach ($arResult['CATALOG_IMG']['PHOTO'] as $photo) { ?>
                                    <div class="swiper-slide image" data-detail-preview-items>
                                        <img class="lazy" src="<?=IMAGE_PLACEHOLDER?>" data-src="<?= $photo['SMALL'] ?>" alt="<?= $arResult['NAME'] ?>">
                                    </div>
                                <? } ?>
                                <? if (!empty($arResult['CATALOG_IMG']['360'])) { ?>
                                    <div class="swiper-slide image" data-detail-preview-items>
                                        <img class="lazy" src="<?=IMAGE_PLACEHOLDER?>" data-src="/local/templates/paoloconte/images/3d.png"
                                             alt="<?= $arResult['NAME'] ?>">
                                    </div>
                                <? } ?>                            
                            <? } ?>
                        </div>
                    </div>

                    <div class="product-slider__arrow product-slider__arrow--next" data-detail-preview-next></div>
                </div>

                <div class="product-slider-big swiper-container" <?= $dir == "odezhda" ? "data-detail-main-clothes" : "data-detail-main" ?>>
                    <div class="swiper-wrapper">
                        <? if (!empty($arResult['CATALOG_IMG']['PHOTO'])) { ?>
                            <? foreach ($arResult['CATALOG_IMG']['PHOTO'] as $photo) { ?>
                                <div class="swiper-slide image">
                                    <img src="<?=IMAGE_PLACEHOLDER?>" data-src="<?= $photo['BIG'] ?>" alt="<?= $arResult['NAME'] ?>"
                                         class="silder_image elevate-zoom lazy" data-zoom-image="<?= $photo['BIGGEST'] ?>">
                                </div>
                            <? } ?>
                        <? if (!empty($arResult['CATALOG_IMG']['360'])) { ?>
                            <?
                            $images360 = '';
                            foreach ($arResult['CATALOG_IMG']['360'] as $photo) {
                                $images360 .= $photo['BIG'] . ',';
                            }
                            $images360 = substr($images360, 0, -1);
                            ?>
                            <div class="swiper-slide">
                                <div class="catalog_image_360" data-images="<?= $images360 ?>"></div>
                            </div>
                        <? } ?>                        
                        <? } else { ?>
                            <div class="swiper-slide image">
                                <img class="lazy" src="<?=IMAGE_PLACEHOLDER?>" data-src="<?= $arResult['NOPHOTO'] ?>" alt="Нет фото">
                            </div>
                        <? } ?>
                    </div>

                    <div class="swiper-pagination"></div>

                </div>

                <div class="product-links">
                        <span class="product-links__link label-icon to-favorite-new"
                              data-image="<?= $productPhoto ?>"
                              data-product-id="<?= $productIdToFav ?>"
                              data-text="Добавить в список желаний"
                              data-toggle="modal">
                            <svg class='i-icon'>
                                <use xlink:href='#heart'/>
                            </svg>
                        </span>

                    <div class="product-links__link product-links__link-2">
                        <script src="https://yastatic.net/share2/share.js" async="async"></script>
                        <div class="ya-share2"
                             data-services="vkontakte,facebook,telegram,viber,twitter,skype,whatsapp,odnoklassniki"
                             data-limit="0"
                             data-copy="hidden"></div>
                    </div>
                </div>


                <meta itemprop="image" content="<?= $productPhoto; ?>">
                <?
                $count_for_pointers = count($arResult['CATALOG_IMG']['PHOTO']);
                if (count($arResult['CATALOG_IMG']['360']) > 0) $count_for_pointers++; ?>
                <? if ($count_for_pointers < 6) { ?>
                    <style>
                        .owl-nav {
                            display: none;
                        }
                    </style>
                <? } ?>
            </div>

            <div class="product-desc">
                <div class="product-desc__item">
                    <div class="product-desc__img">
                        <svg class='i-icon'>
                            <use xlink:href='#fitting'/>
                        </svg>
                    </div>
                    <div class="product-desc__inner">
                        <div class="product-desc__title">Примерка</div>
                        <div class="product-desc__text">В вашем распоряжении
                            будет 15 минут на примерку</div>
                    </div>
                </div>
                <div class="product-desc__item">
                    <div class="product-desc__img">
                        <svg class='i-icon'>
                            <use xlink:href='#buyout'/>
                        </svg>
                    </div>
                    <div class="product-desc__inner">
                        <div class="product-desc__title">Частичный выкуп</div>
                        <div class="product-desc__text">Вы сможете выкупить только
                            понравившиеся вам модели</div>
                    </div>
                </div>
                <div class="product-desc__item">
                    <div class="product-desc__img">
                        <svg class='i-icon'>
                            <use xlink:href='#refund'/>
                        </svg>
                    </div>
                    <div class="product-desc__inner">
                        <div class="product-desc__title">Возврат</div>
                        <div class="product-desc__text">Возврат неношеной обуви возможен в течение 14 дней</div>
                    </div>
                </div>
                <div class="product-desc__item">
                    <div class="product-desc__img">
                        <svg class='i-icon'>
                            <use xlink:href='#present'/>
                        </svg>
                    </div>
                    <div class="product-desc__inner">
                        <div class="product-desc__title">Программа лояльности</div>
                        <div class="product-desc__text">За выкупленный товар вы получите баллы на следующие покупки!</div>
                    </div>
                </div>
            </div>

            <div class="product-info">
                <div class="product-info__inner">
                    <h1 itemprop="name"><?= $arResult['NAME'] ?></h1>

                    <? if (!empty($arResult['REVIEWS'])) {
                        $summStarsValue = 0;
                        $maxCountStars = 5;
                        $countStars = count($arResult['REVIEWS']);
                        foreach ($arResult['REVIEWS'] as $reviewItem) {
                            $summStarsValue = $summStarsValue + intval($reviewItem['PROPERTY_STARS_VALUE']); ?>
                            <div style="display: none;" itemprop="review" itemscope itemtype="http://schema.org/Review">
                                <span class="review-name" itemprop="author"
                                      content="<?= $reviewItem['USERNAME'] ?>"></span>
                                <meta itemprop="datePublished" content="<?= $reviewItem['DATE_CREATE_META'] ?>">
                                <meta itemprop="description" content="<?= $reviewItem["PROPERTY_MESSAGE_VALUE"] ?>">
                                <div style="display: none;" itemprop="reviewRating" itemscope
                                     itemtype="http://schema.org/Rating">
                                    <meta itemprop="worstRating" content="1"/>
                                    <span itemprop="ratingValue"
                                          content="<?= $reviewItem['PROPERTY_STARS_VALUE']; ?>"></span>
                                    <span itemprop="bestRating" content="<?= $maxCountStars; ?>"></span>
                                </div>
                            </div>
                        <? } ?>

                        <div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
                            <span itemprop="ratingValue" content="<?= round($summStarsValue / $countStars); ?>"></span>
                            <span itemprop="reviewCount" content="<?= $countStars; ?>"></span>
                        </div>
                    <? } ?>

                    <a href="#go_review_block" class="product-info__rating">
                        <div class="rating_stars <? /*indetail*/ ?>">
                            <? for ($i = 1; $i <= 5; $i++) { ?>
                                <? $checked = ($i == $arResult['RATING']) ? 'checked' : ''; ?>
                                <input type="radio" title="<?= $i ?>" value="<?= $i ?>" class="star"
                                       disabled="disabled" <?= $checked ?>/>
                            <? } ?>
                        </div>

                        <span>Оставить отзыв</span>
                    </a>

                </div>

                <? $frame = $this->createFrame()->begin(); ?>
                <div class="product-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    <span class="hidden" itemprop="priceCurrency">RUB</span>
                    <? if (!empty($arResult['NEW_PRICE'])) { ?>
                    <div class="product-price__top">
                        <div class="product-price__old rouble"><?= $arResult['OLD_PRICE'] ?></div>
                    </div>

                    <div class="product-price__bottom">
                        <? $newPriceValue = $arResult['NEW_PRICE'] ?>
                        <div class="product-price__current rouble" itemprop="price"
                             content="<?= str_replace(' ', '', $newPriceValue); ?>">
                            <?= $newPriceValue; ?>
                        </div>
                        <div class="tag">
                            <? if ($arResult['SALE_PERCENT'] > 0) { ?>
                                <span class="tag__item tag__item--sell"><?= $arResult['SALE_PERCENT'] ?>%</span>
                            <? } ?>
                        </div>
                        <? } else { ?>
                        <div class="product-price__bottom">
                            <? $oldPriceValue = $arResult['OLD_PRICE']; ?>
                            <div class="product-price__current rouble" itemprop="price"
                                 content="<?= str_replace(' ', '', $oldPriceValue); ?>">
                                <?= $oldPriceValue; ?>
                            </div>
                            <div class="tag">
                                <? if ($arResult['SALE_PERCENT'] > 0) { ?>
                                    <span class="tag__item tag__item--sell"><?= $arResult['SALE_PERCENT'] ?>%</span>
                                <? } ?>
                            </div>
                            <? } ?>
                            <a href="#"
                               data-target="#priceModal"
                               data-item-id="<?= $arResult['ID'] ?>">
                                Узнать о снижении цены
                            </a>
                        </div>
                        <? if ($arResult['PROPERTIES']['OFFERS_AMOUNT']['VALUE'] > 0) { ?>
                            <link itemprop="availability" href="http://schema.org/InStock">
                        <? } ?>
                        <link itemprop="itemCondition" href="http://schema.org/NewCondition">
                    </div>
                    <? $frame->end(); ?>

                    <? if (!empty($arResult['OTHER_COLORS'])) { ?>
                        <div class="product__color">
                            <? foreach ($arResult['OTHER_COLORS'] as $arColor) { ?>
                                <? $active = ($arColor['CODE'] == $arResult['CODE']) ? true : false; ?>
                                <a href="<? if ($active): ?>javascript:void(0)<? else: print $arColor['DETAIL_PAGE_URL']; endif ?>"
                                   <? if ($active): ?>class="active"<? endif ?> title="<?= $arColor['COLOR']['NAME'] ?>"
                                   style="background-image: url('<?= $arColor['COLOR']['FILE_PATH'] ?>')"></a>
                            <? } ?>
                        </div>
                    <? } ?>

                    <? if (!empty($arResult['OFFERS'])) { ?>
                        <? $frame = $this->createFrame()->begin(); ?>
                        <div class="product-size">
                            <div class="product-size__top">
                                <div class="product-size__title">Размер</div>
                                <a href="#"
                                   data-target="#sizeModal"
                                   data-color-id="<?= $arResult['PROPERTIES']['GRUPPIROVKA_PO_MODELYAM_SAYT_']['VALUE']; ?>"
                                   data-item-id="<?= $arResult['ID'] ?>">Нет вашего размера?</a>
                            </div>

                            <div class="product-size__inner" id="<? echo $arItemIDs['PROP_DIV']; ?>">
                                <?
                                $offerId = '';
                                $i = 0;
                                foreach ($arResult['OFFERS'] as $key => $arOffer) { ?>
                                    <?
                                    $str_price = 'CATALOG_PRICE_' . $_SESSION['GEO_PRICES']['PRICE_ID'];
                                    if ($arOffer[$str_price] > 0 && $arResult['OFFERS_AMOUNT'][$arOffer['ID']] > 0) { ?>
                                        <?
                                        $active = ($i == 0) ? true : false;
                                        if ($active) $offerId = $arOffer['ID'];
                                        ?>
                                        <label class="<?= $arOffer['CAN_BUY'] == '1' ? '' : 'lost' ?>">
                                            <?= $arOffer['PROPERTIES']['RAZMER']['VALUE'] ?>
                                            <input type="radio"
                                                   value="<?= $arOffer['ID'] ?>"
                                                   name="r<? echo $arResult['ID']; ?>"
                                                <?= $arOffer['CAN_BUY'] == '1' ? '' : 'disabled' ?>
                                                   class="radio-offer"
                                                   data-id="<?= $arResult['ID'] ?>"
                                                   data-name="<?= $arResult['NAME'] ?> (<?= $arOffer['PROPERTIES']['RAZMER']['VALUE'] ?>)"
                                            >
                                        </label>
                                        <? $i++; ?>
                                    <? } ?>
                                <? } ?>
                            </div>
                        </div>
                        <? $frame->end(); ?>
                    <? } ?>

                    <div class="product__btns">
                        <?
                        $productAvailabilityBuy = new ProductAvailabilityBuy();
                        $isSectionChildClothes = $productAvailabilityBuy->isSectionChildClothes($arResult['IBLOCK_SECTION_ID']);
                        if ((
                                !$isSectionChildClothes &&
                                $arResult['PROPERTIES']['OFFERS_AMOUNT']['VALUE'] > 0
                            ) ||
                            (
                                $isSectionChildClothes &&
                                $productAvailabilityBuy->getCountProductsForBuyClothes($arResult['ID']) > 0
                            )
                        ) { ?>
                            <a href="#"
                               onmousedown="try { rrApi.addToBasket(<?= $arResult['ID'] ?>) } catch(e) {}"
                               id='addBucket'
                               class="btn btn--black btn-tobasket"
                               data-product-id="<?= $productId ?>"
                               data-product-name="<?= $arResult['NAME'] ?>"
                               data-product-price="<?= $arResult['CATALOG_PRICE_5'] ? $arResult['CATALOG_PRICE_5'] : $arResult['CATALOG_PRICE_2'] ?>"
                               data-type="getMovedModalPanel"
                               data-target="#side-cart">
                                <span>В корзину</span>
                            </a>

                            <a href="#" class="btn btn--transparent fast-order"
                               onmousedown="try {rrApi.addToBasket('<?= $arResult['ID'] ?>')} catch(e) {}"
                               data-toggle="modal"
                               data-product-id="<?= $productId ?>"
                               data-target="#fastOrderModal_<?= $arResult['ID'] ?>">
                                <span>Быстрый заказ</span>
                            </a>
                            
                            <?if($isReserveModalShow){?>
                                <a href="#" class="btn btn--transparent set-aside" data-toggle="modal" data-target="#reserve">
                                    <span>Отложить в магазине</span>
                                </a>
                            <?}?>
                            <br/>
                        <? } else { ?>
                            <div class="error-txt">К сожалению, данная модель отсутствует на складе.<br/>
                                <a href="<?= $arResult['SECTION']['SECTION_PAGE_URL'] ?>">Посмотрите другие модели</a>
                            </div>
                        <? } ?>
                    </div>

                    <div class="product__characteristics">
                        <? $color = $arResult['DISPLAY_PROPERTIES']['TSVET_MARKETING']['DISPLAY_VALUE']; ?>
                        <? if ($color != '') { ?>
                            <div>
                                <span>Цвет:</span>
                                <span><?= $color ?></span>
                            </div>
                        <? } ?>

                        <? foreach ($arResult['DISPLAY_PROPERTIES'] as $key => $arProp) { ?>
                            <? if (($key != 'TSVET_MARKETING') && ($key != 'CML2_ARTICLE')) { ?>
                                <? if (($key == 'VYSOTA_KABLUKA' && $arProp['VALUE'] == 0) || ($key == 'VYSOTA_GOLENISHCHA_PROIZVODSTVO' && $arProp['VALUE'] == 0)) continue; ?>
                                <div>
                                    <span><?= $arProp['NAME'] ?>:</span>
                                    <span><?= \Citfact\Tools::my_mb_ucfirst($arProp['VALUE']) ?></span>
                                </div>

                                <? if ($key == "MATERIAL_VERKHA_MARKETING" && !empty($arProp['VALUE'])) { ?>
                                    <meta itemprop="material"
                                          content="<?= \Citfact\Tools::my_mb_ucfirst($arProp['VALUE']); ?>">
                                <? } ?>
                            <? } ?>
                        <? } ?>

                        <? if ($arResult['PROPERTIES']['RAZMER_AKSESSUARY']['VALUE'] != '') { ?>
                            <div>
                                <span>Размер:</span>
                                <span><? echo $arResult['PROPERTIES']['RAZMER_AKSESSUARY']['VALUE'] ?></span>
                            </div>
                        <? } ?>

                        <? if ($arResult['DISPLAY_PROPERTIES']['CML2_ARTICLE']['VALUE'] != '') { ?>
                            <div class="article">
                                <span>Артикул:</span>
                                <span><? echo $arResult['DISPLAY_PROPERTIES']['CML2_ARTICLE']['VALUE'] ?></span>
                            </div>
                        <? } ?>

                        <?if (!empty($arResult["DETAIL_TEXT"])){?>
                            <div class="article" itemprop="description">
                                <?=$arResult["DETAIL_TEXT"]?>
                            </div>
                        <?}else{?>
                            <meta itemprop="description" content="<?= $name; ?>">
                        <?}?>
                    </div>


                    <?
                    if ($isReserveModalShow){?>
                        <?$APPLICATION->IncludeComponent(
                            "citfact:reserve",
                            "",
                            array(
                                "RESULT" => $arResult,
                            ),
                            false
                        );?>
                    <?}?>
                </div>
            </div>
        </div>
        <?
        global $arrFilter, $arrFilterBigData;
        if(!empty($arrFilter)){
            $arrFilterBigData = array_merge($arrFilter, Tools::getFilterForBigData());
        } else {
            $arrFilterBigData =  Tools::getFilterForBigData();
        }
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
                "LABEL_PROP" => array(
                ),
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
                "PROPERTY_CODE_MOBILE" => array(
                ),
                "RCM_PROD_ID" => $arResult['ID'],
                "RCM_TYPE" => "similar",
                "SECTION_ID" => $arResult['IBLOCK_SECTION_ID'],
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
                "OFFER_TREE_PROPS" => array(
                ),
                "LABEL_PROP_MOBILE" => "",
                "LABEL_PROP_POSITION" => "top-left",
                "DISCOUNT_PERCENT_POSITION" => "bottom-right",
                'HIDE_SECTION_DESCRIPTION' => 'Y',
            ),
            false
        );?>

        <div class="product-feedback" id="go_review_block" data-toggle-wrap>
            <div class="product-feedback__top">
                <div class="product-feedback__placeholder"></div>

                <div class="product-feedback__title" data-toggle-btn-m>
                    <h3>Отзывы</h3>

                    <? if (!empty($arResult['REVIEWS'])) { ?>
                        <div class="rating_stars ">
                            <span class="star-rating-control">

                            <div class="rating-cancel" style="display: none;">
                                <a title="Cancel Rating"></a>
                            </div>

                                <div role="text" aria-label="1"
                                     class="star-rating rater-0 star star-rating-applied star-rating-readonly star-rating-on">
                                    <a title="1">1</a>
                                </div>
                                <div role="text" aria-label="2"
                                     class="star-rating rater-0 star star-rating-applied star-rating-readonly star-rating-on">
                                    <a title="2">2</a>
                                </div>
                                <div role="text" aria-label="3"
                                     class="star-rating rater-0 star star-rating-applied star-rating-readonly star-rating-on">
                                    <a title="3">3</a>
                                </div>
                                <div role="text" aria-label="4"
                                     class="star-rating rater-0 star star-rating-applied star-rating-readonly star-rating-on">
                                    <a title="4">4</a>
                                </div>
                                <div role="text" aria-label="5"
                                     class="star-rating rater-0 star star-rating-applied star-rating-readonly star-rating-on">
                                    <a title="5">5</a>
                                </div>
                            </span>

                            <input type="radio" title="1" value="1"
                                   class="star star-rating-applied star-rating-readonly" disabled="disabled"
                                   style="display: none;">
                            <input type="radio" title="2" value="2"
                                   class="star star-rating-applied star-rating-readonly" disabled="disabled"
                                   style="display: none;">
                            <input type="radio" title="3" value="3"
                                   class="star star-rating-applied star-rating-readonly" disabled="disabled"
                                   style="display: none;">
                            <input type="radio" title="4" value="4"
                                   class="star star-rating-applied star-rating-readonly" disabled="disabled"
                                   style="display: none;">
                            <input type="radio" title="5" value="5"
                                   class="star star-rating-applied star-rating-readonly" disabled="disabled" checked=""
                                   style="display: none;">
                        </div>
                    <? } ?>

                    <div class="plus"></div>
                </div>

                <div class="product-feedback__btn">
                    <a href="#" class="btn btn--transparent addProductReviewLink" data-toggle="modal"
                       data-target="#productReviewModal_<?= $arResult['ID'] ?>" data-item-id="<?= $arResult['ID'] ?>"
                       data-item-name="<?= urlencode($arResult['NAME']) ?>"
                       data-item-link="<?= urlencode($arResult['DETAIL_PAGE_URL']) ?>"
                       data-item-src="<?= urlencode($productPhoto) ?>">
                        <span>Написать отзыв</span>
                    </a>
                </div>
            </div>

            <div class="product-feedback__items" data-toggle-list>
                <div class="product-feedback__btn">
                    <a href="#" class="btn btn--transparent addProductReviewLink" data-toggle="modal"
                       data-target="#productReviewModal_<?= $arResult['ID'] ?>" data-item-id="<?= $arResult['ID'] ?>"
                       data-item-name="<?= urlencode($arResult['NAME']) ?>"
                       data-item-link="<?= urlencode($arResult['DETAIL_PAGE_URL']) ?>"
                       data-item-src="<?= urlencode($productPhoto) ?>">
                        <span>Написать отзыв</span>
                    </a>
                </div>

                <? if (!empty($arResult['REVIEWS'])) { ?>
                    <? foreach ($arResult['REVIEWS'] as $reviewItem) { ?>
                        <div class="product-feedback__item">
                            <div class="product-feedback__text">
                                <?= $reviewItem['PROPERTY_MESSAGE_VALUE'] ?>
                            </div>
                            <div class="product-feedback__info">
                                <div class="product-feedback__name">
                                    <?= $reviewItem['USERNAME'] ?>
                                </div>
                                <div class="product-feedback__date"><?= $reviewItem['DATE_CREATE'] ?></div>
                                <div class="stars-block">
                                    <? $class = 'star-el';
                                    for ($i = 1; $i <= $maxCountStars; $i++) {
                                        if (($i - 1) == $reviewItem['PROPERTY_STARS_VALUE']) {
                                            $class .= ' not-checked';
                                        } ?>
                                        <span class="<?= $class; ?>"><?= $i; ?></span>
                                    <? } ?>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                <? } else { ?>
                    <div class="product-feedback__empty">
                        Оставьте отзыв о купленном товаре и получите купон на 100 рублей!
                    </div>
                <? } ?>
            </div>
        </div>

        <? // Вы посмотрели?>
        <? $APPLICATION->IncludeComponent(
            "bitrix:catalog.viewed.products",
            "catalog",
            Array(
                "IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
                "IBLOCK_ID" => $arParams['IBLOCK_ID'],
                "SECTION_ID" => "",
                "SECTION_CODE" => "",
                "SECTION_ELEMENT_ID" => "",
                "SECTION_ELEMENT_CODE" => "",
                "DEPTH" => "",
                "DETAIL_URL" => "",
                "SHOW_FROM_SECTION" => "N",
                "HIDE_NOT_AVAILABLE" => "N",
                "SHOW_DISCOUNT_PERCENT" => "Y",
                "PRODUCT_SUBSCRIPTION" => "N",
                "SHOW_NAME" => "N",
                "SHOW_IMAGE" => "N",
                "MESS_BTN_BUY" => "Купить",
                "MESS_BTN_DETAIL" => "Подробнее",
                "MESS_BTN_SUBSCRIBE" => "Подписаться",
                "PAGE_ELEMENT_COUNT" => "",
                "LINE_ELEMENT_COUNT" => "",
                "TEMPLATE_THEME" => "blue",
                "CACHE_TYPE" => "N",
                "CACHE_TIME" => "36000000",
                "CACHE_GROUPS" => "N",
                "SHOW_OLD_PRICE" => "Y",
                "PRICE_CODE" => $arParams['PRICE_CODE'],
                "SHOW_PRICE_COUNT" => "1",
                "PRICE_VAT_INCLUDE" => "Y",
                "CONVERT_CURRENCY" => "N",
                "BASKET_URL" => $arParams['BASKET_URL'],
                "ACTION_VARIABLE" => "action",
                "PRODUCT_ID_VARIABLE" => "id",
                "ADD_PROPERTIES_TO_BASKET" => "Y",
                "PRODUCT_PROPS_VARIABLE" => "prop",
                "PARTIAL_PRODUCT_PROPERTIES" => "N",
                "USE_PRODUCT_QUANTITY" => "N",
                "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                "SHOW_PRODUCTS_10" => "Y",
                "PROPERTY_CODE_10" => array(0 => "CML2_ARTICLE"),
            )
        ); ?>

        <div class="modal fade fastOrderModal" id="fastOrderModal_<?= $arResult['ID'] ?>" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal"></button>
                    <div class="modal-body">
                        <div class="title-1">
                            Быстрый заказ
                        </div>
                        <div class="fast-order-wrap emulate-table full">
                            <? $APPLICATION->IncludeComponent("citfact:form.ajax", "fast_order", Array(
                                "IBLOCK_ID" => IBLOCK_FAST_ORDER,
                                "SHOW_PROPERTIES" => array(
                                    "USERNAME" => array(
                                        "type" => "text",
                                        "placeholder" => "Введите ваше имя",
                                        "required" => "Y",
                                        "class" => 'required'
                                    ),
                                    "USERPHONE" => array(
                                        "type" => "text",
                                        "placeholder" => "Введите ваш телефон",
                                        "required" => "Y",
                                        "class" => 'mask-phone required'
                                    ),
                                    "PRODUCT_NAME" => array(
                                        "type" => "hidden",
                                        "placeholder" => "Наименование товара",
                                        "required" => "Y",
                                        "value" => $arResult['NAME'],
                                        'id' => 'input_name_product_' . $arResult['ID']
                                    ),
                                    "PRODUCT_ID" => array(
                                        "type" => "hidden",
                                        "placeholder" => "Id товара",
                                        "required" => "Y",
                                        "value" => $arResult['ID'],
                                        'id' => 'input_id_product_' . $arResult['ID']
                                    ),
                                    "PRICE_CODES" => array(
                                        "type" => "hidden",
                                        "required" => "N",
                                        "value" => implode(', ', $_SESSION['GEO_PRICES']['TO_PARAMS'])
                                    ),
                                    "CURRENT_PRICE" => array(
                                        "type" => "hidden",
                                        "required" => "N",
                                        "value" => (!empty($arResult['NEW_PRICE']) ? $arResult['NEW_PRICE'] : $arResult['OLD_PRICE'])
                                    ),
                                    "PRICE" => array(
                                        "type" => "hidden",
                                        "required" => "N",
                                        "value" => (!empty($arResult['NEW_PRICE']) ? str_replace(" ", "", substr($arResult['NEW_PRICE'], 0, -5)) : str_replace(" ", "", substr($arResult['OLD_PRICE'], 0, -5)))
                                    ),
                                ),
                                "EVENT_NAME" => "FAST_ORDER_FORM",
                                "SUCCESS_MESSAGE" => "Ваша заявка принята. Мы перезвоним вам в ближайшее время.",
                                "ELEMENT_ACTIVE" => "Y",
                                "PRODUCT_IMAGE" => $productPhoto
                            ),
                                false
                            ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade productReviewModal <? if (!$USER->IsAuthorized()): ?>enterModal<? endif ?>"
             id="productReviewModal_<?= $arResult['ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <? // Грузим сюда аяксом /include/ajax_product_review_popup.php ?>
                </div>
            </div>
        </div>
    </div>

    <?
    $pat = "";
    foreach ($arResult["SECTION"]["PATH"] as $path) {
        $pat .= $path["NAME"] . "/";
    }
    $pat = substr($pat, 0, -1);
    ?>
    <script type="text/javascript">
        window.isInitScriptCatalogInTemplates = true;
      BX.message({
        ECONOMY_INFO_MESSAGE: '<? echo GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO'); ?>',
        BASIS_PRICE_MESSAGE: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_BASIS_PRICE') ?>',
        TITLE_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR') ?>',
        TITLE_BASKET_PROPS: '<? echo GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS') ?>',
        BASKET_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
        BTN_SEND_PROPS: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS'); ?>',
        BTN_MESSAGE_BASKET_REDIRECT: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT') ?>',
        BTN_MESSAGE_CLOSE: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE'); ?>',
        BTN_MESSAGE_CLOSE_POPUP: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP'); ?>',
        TITLE_SUCCESSFUL: '<? echo GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK'); ?>',
        COMPARE_MESSAGE_OK: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK') ?>',
        COMPARE_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR') ?>',
        COMPARE_TITLE: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE') ?>',
        BTN_MESSAGE_COMPARE_REDIRECT: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT') ?>',
        SITE_ID: '<? echo SITE_ID; ?>',

        BUY_URL: '<?=$arResult['ADD_URL_TEMPLATE']?>'
      });

      window.dataLayer = window.dataLayer || [];
      <?
      $pricePush = 0;
      if (!empty($arResult["CATALOG_PRICE_2"])) {
          $pricePush = $arResult["CATALOG_PRICE_2"];
      } elseif (!empty($arResult["NEW_PRICE"])) {
          $pricePush = str_replace(" ", "", substr($arResult["NEW_PRICE"], 0, -5));
      } elseif (!empty($arResult["OLD_PRICE"])) {
          $pricePush = str_replace(" ", "", substr($arResult["OLD_PRICE"], 0, -5));;
      }?>
      dataLayer.push({
        "event": "detail",
        "ecommerce": {
          "detail": {
            "products": [
              {
                "id": "<?=$arResult["ID"]?>",
                "name": "<?=$arResult["NAME"]?>",
                "price": <?=$pricePush;?>,
                "category": "<?=$pat?>"
              }
            ]
          }
        }
      });

      window.gtmRemarketingTag = {
          pagetype: 'product',
          prodid: '<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>',
          totalvalue: <?=$pricePush?>,
      };
    </script>

    <noindex>
        <? if (isset($arResult['navBar'][0]) && $arResult['navBar'][0]['USE'] == 'Y' && $arResult['navBar'][0]['CURRENT'] != 'current'): ?>
            <a href="<?= $arResult['navBar'][0]['URL'] ?>?<?= $sort_url ?>" class="prevItems">
                Предыдущий товар
            </a>
        <? endif; ?>
        <? if (isset($arResult['navBar'][2]) && $arResult['navBar'][2]['USE'] == 'Y' && $arResult['navBar'][2]['CURRENT'] != 'current'): ?>
            <a href="<?= $arResult['navBar'][2]['URL'] ?>?<?= $sort_url ?>" class="nextItems">
                Следующий товар
            </a>
        <? endif; ?>
    </noindex>

    <!-- Creates the bootstrap modal where the image will appear -->
    <div class="modal fade modalZoomImg" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="close" data-dismiss="modal"></button>
                <div class="modal-body">
                    <img src="" class="modal-img" id="imagepreview">
                </div>
            </div>
        </div>
    </div>
