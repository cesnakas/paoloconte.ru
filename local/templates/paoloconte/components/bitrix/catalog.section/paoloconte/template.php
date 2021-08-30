<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
$frame = $this->createFrame()->begin();

global $sotbitSeoMetaH1;
if (trim($sotbitSeoMetaH1)) {
    $sectionTitle = $sotbitSeoMetaH1;
}else {
    $sectionTitle = (trim($arResult["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"])) ?: $arResult["NAME"];
}
?>

<? $this->SetViewTarget('catalog-section-page-top'); ?>
<? if (count($arResult['ITEMS']) > 0 && $APPLICATION->GetCurDir() != '/search/') { ?>
    <? $APPLICATION->ShowViewContent('showRRscript'); ?>
    <div class="page-top-sort">
        <div class="page-top-sort__inner" data-s-toggle-wrap>
            <?
            $updown = '';
            if ($_REQUEST["order"] == 'asc') $updown = 'up';
            if ($_REQUEST["order"] == 'desc') $updown = 'down';
            ?>

            <div class="page-top-sort__top" data-s-toggle-btn>
                <div class="page-top-sort__title">
                    Сортировка<span> по</span>
                </div>

                <div class="page-top-sort__text">
                    <? if ($arResult['SORT']['PRICE']['ACTIVE']) { ?>
                        ценe
                    <? } else if ($arResult['SORT']['RATE']['ACTIVE']) { ?>
                        рейтингу
                    <? } else if ($arResult['SORT']['POPULAR']['ACTIVE']) { ?>
                        популярности
                    <? } else { ?>
                        <span class="page-top-sort__arrow"></span>
                    <? } ?>
                </div>
            </div>

            <? if ($arResult['SORT']['PRICE']['ACTIVE']) { ?>
                <a href="<?= $arResult['SORT']['PRICE']['URL'] ?>">
                    <div class="page-top-sort__arrow<?= $updown == 'up' ? ' active' : '' ?>"></div>
                </a>
            <? } else if ($arResult['SORT']['RATE']['ACTIVE']) { ?>
                <a href="<?= $arResult['SORT']['RATE']['URL'] ?>">
                    <div class="page-top-sort__arrow<?= $updown == 'up' ? ' active' : '' ?>"></div>
                </a>
            <? } else if ($arResult['SORT']['POPULAR']['ACTIVE']) { ?>
                <a href="<?= $arResult['SORT']['POPULAR']['URL'] ?>">
                    <div class="page-top-sort__arrow<?= $updown == 'up' ? ' active' : '' ?>"></div>
                </a>
            <? } else { ?>
            <? } ?>

            <div class="page-top-sort__items" data-s-toggle-list>
                <a href="<?= $arResult['SORT']['PRICE']['URL'] ?>"
                   class="<?= $arResult['SORT']['PRICE']['ACTIVE'] != '' ? 'active' : '' ?>">
                    ЦЕНЕ
                </a>

                <a href="<?= $arResult['SORT']['RATE']['URL'] ?>"
                   class="<?= $arResult['SORT']['RATE']['ACTIVE'] != '' ? 'active' : '' ?>">
                    РЕЙТИНГУ
                </a>

                <a href="<?= $arResult['SORT']['POPULAR']['URL'] ?>"
                   class="<?= $arResult['SORT']['POPULAR']['ACTIVE'] != '' ? 'active' : '' ?>">
                    ПОПУЛЯРНОСТИ
                </a>
            </div>
        </div>
    </div>
<? } ?>
<? $this->EndViewTarget(); ?>

<? $this->SetViewTarget("catalog-section-rr-block"); ?>
<? if (!isset($_GET['set_filter'])) { ?>
    <? $section_url = trim($arResult['SECTION_PAGE_URL'], '/'); ?>
    <div data-retailrocket-markup-block="567292709872e52a3cbd9a51" data-category-name="<?= $arResult["NAME"]; ?>"
         data-category-path="<?= $section_url; ?>"></div>
<? } ?>
<? $this->EndViewTarget(); ?>

<script>
  if (window.frameCacheVars !== undefined) {
    BX.addCustomEvent("onFrameDataReceived", function (json) {
      window.Application.Components.Main.labelcheck();
    });
  } else {
    BX.ready(function () {

    });
  }
</script>

<div class="aside__main">
	<? if (count($arResult['ITEMS']) != 0) { ?>
    
<? if (!isset($_GET['set_filter'])) { ?>
    <? $section_url = trim($arResult['SECTION_PAGE_URL'], '/'); ?>
    <div data-retailrocket-markup-block="567292709872e52a3cbd9a51" data-category-name="<?= $arResult["NAME"]; ?>"
         data-category-path="<?= $section_url; ?>"></div>
<? } ?>
    
<? $this->SetViewTarget("catalog-section-h1-title"); ?>
        <div class="title-page">
            <div class="title-1">
                <?if ($_REQUEST['PAGEN_3']) {
                    echo (!empty($sectionTitle)) ?  ' <h1>' . $sectionTitle . ' стр.' . $_REQUEST['PAGEN_3'].'</h1>' : GetMessage('CT_BCS_CATALOG_H1_ALL') . ' стр.' . $_REQUEST['PAGEN_3'] ;

                } else{
                    echo (!empty($sectionTitle)) ? ' <h1>' . $sectionTitle . '</h1>' : GetMessage('CT_BCS_CATALOG_H1_ALL');
                }?>
            </div>
        </div>
<? $this->EndViewTarget(); ?>
    <? } else {
		 CHTTP::SetStatus("404 Not Found");
		@define("ERROR_404","Y");
	?>
        <div class="error-404-wrap">
            <div class="top-text align-center">
                К сожалению, в данном разделе товаров пока нет.</br>Попробуйте вернуться на главную или
                воспользуйтесь поиском, если ищете что-то конкретное.
            </div>

            <div class="search-wrap">
                <?
                $page_url = $APPLICATION->GetCurPage();
                $page_url = explode("/", $page_url);
                if ($page_url[1] != 'search'):
                    $APPLICATION->IncludeComponent(
                        "bitrix:search.form",
                        "404",
                        array(
                            "PAGE" => "#SITE_DIR#search/"
                        ),
                        false
                    );
                endif; ?>
            </div>

            <div class="btn-box align-center">
                <a href="/" class="btn btn-gray-dark mode2 icon-arrow-right">Вернуться на главную</a>
            </div>
        </div>
    <? } ?>

    <div class="c-g<?=($arParams['COOKIE_FILTER_HIDE'] ? " c-g--small" : "")?>">
        <? foreach ($arResult['ITEMS'] as $key => $arItem) {
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
            $strMainID = $this->GetEditAreaId($arItem['ID']);

            //product id for favorite and add cart
            $productId = '';
            if (!empty($arItem['OFFERS'])) {
                reset($arItem['OFFERS']);
                $first = current($arItem['OFFERS']);
                $productIdToFav = $first['ID'];
            } else {
                $productId = $arItem['ID'];
                $productIdToFav = $arItem['ID'];
            }

            //favoriteIcon
            if (!empty($arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']))
                $favoritePhoto = $arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL'];
            else
                $favoritePhoto = $arResult['NOPHOTO'];
            ?>

            <div class="c-g-item item" id="<? echo $strMainID; ?>" itemscope
                 itemtype="http://schema.org/Product">
                <meta itemprop="name" content="<?= $arItem["NAME"]; ?>">
                <meta itemprop="description" content="<?= $arItem['PREVIEW_TEXT'] ? $arItem['PREVIEW_TEXT'] : $arItem['NAME'];?>">

                <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="c-g-item__img image">
                    <? if ($arItem['CATALOG_PHOTO']): ?>
                        <img itemprop="image" class="lazy" src="<?=IMAGE_PLACEHOLDER?>" data-src="<?= $arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL'] ?>"
                             alt="<?= $arItem['NAME'] ?>">
                    <? else: ?>
                        <img class="lazy" src="<?=IMAGE_PLACEHOLDER?>" data-src="<?= $arResult['NOPHOTO'] ?>" alt="нет фото">
                    <? endif; ?>
                </a>

                <a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="c-g-item__title">
                    <?= (!empty($arItem["PROPERTY_NAIMENOVANIE_MARKETING_VALUE"])) ? $arItem["PROPERTY_NAIMENOVANIE_MARKETING_VALUE"] : $arItem["NAME"]; ?>
                </a>

                <div class="c-g-item-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    <meta itemprop="priceCurrency" content="RUB">
                    <div class="c-g-item-price__old <?= $arItem['OLD_PRICE'] != '' ? 'rouble' : '' ?>"><?= $arItem['OLD_PRICE']; ?>

                    </div>
                    <div class="c-g-item-price__current rouble" itemprop="price"
                         content="<?= str_replace(' ', '', $arItem['NEW_PRICE']); ?>"><?= $arItem['NEW_PRICE'] ?></div>
                    <div class="c-g-item-discount">
                        <? if (false && $arItem['PROPERTIES']['SALE']['VALUE'] != ''): ?><span
                                class="tag__item tag__item--sell"><?= $arItem['PROPERTIES']['SALE']['VALUE'] ?>
                            %</span><? endif ?>
                        <? if ($arItem['SALE_PERCENT'] > 0): ?><span
                                class="tag__item tag__item--sell"><?= $arItem['SALE_PERCENT'] ?>%</span><? endif ?>
                    </div>
                    <link itemprop="availability" href="http://schema.org/InStock">
                    <link itemprop="itemCondition" href="http://schema.org/NewCondition">
                </div>

                <div class="c-g-item__overlay">
                    <form action="#">
                        <? if (!empty($arItem['OFFERS'])): ?>
                            <div class="c-g-item__color"> <? // TODO ?>
                                <div></div>
                                <div></div>
                            </div>

                            <div class="c-g-item-size">
                                <div class="c-g-item-size__title">
                                    Размеры
                                </div>

                                <div class="c-g-item-size__inner">
                                    <?
                                    $offerId = '';
                                    $i = 0;
                                    foreach ($arItem['OFFERS'] as $key => $arOffer):?>
                                        <? $str_price = 'CATALOG_PRICE_' . $_SESSION['GEO_PRICES']['PRICE_ID'];
                                        if ($arOffer[$str_price] > 0 && $arResult['OFFERS_AMOUNT'][$arOffer['ID']] > 0):?>
                                            <?
                                            $active = ($i == 0) ? true : false;
                                            if ($active) $offerId = $arOffer['ID'];
                                            ?>
                                            <label class="<?= $arOffer['CAN_BUY'] == '1' ? '' : 'lost' ?> <? //if($active) print 'active'
                                            ?>">
                                                <?= $arOffer['PROPERTIES']['RAZMER']['VALUE'] ?>
                                                <input
                                                        type="radio"
                                                        value="<?= $arOffer['ID'] ?>"
                                                        name="r<? echo $arItem['ID']; ?>"
                                                    <?= $arOffer['CAN_BUY'] == '1' ? '' : 'disabled' ?>
                                                        data-item-id="<?= $arItem['ID'] ?>"
                                                    <? //if($active) print 'checked'
                                                    ?>
                                                        class="radio-offer"
                                                >
                                            </label>
                                            <? $i++; ?>
                                        <? endif; ?>
                                    <? endforeach ?>
                                </div>
                            </div>
                        <? endif ?>

                        <div class="c-g-item__btns">
                            <a href="#"
                               onmousedown="try { rrApi.addToBasket(<?= $arItem['ID'] ?>) } catch(e) {}"
                               class="btn btn--black btn-tobasket"
                               data-product-id="<?= $productId ?>"
                               data-item-id="<?= $arItem['ID'] ?>"
                               data-product-name="<?= $arItem['NAME'] ?>"
                               data-product-price="<?= $arItem['CATALOG_PRICE_5'] ? $arItem['CATALOG_PRICE_5'] : $arItem['CATALOG_PRICE_2'] ?>"
                               data-type="getMovedModalPanel"
                               data-target="#side-cart">
                                <span>В корзину</span>
                            </a>

                            <a href="#"
                               class="btn btn--transparent fastViewButton"
                               data-item-id="<?= $arItem['ID'] ?>"
                               data-toggle="modal"
                               data-target="#fastViewModal">
                                <span>Быстрый просмотр</span>
                            </a>
                        </div>
                    </form>
                </div>

                <div class="tag">
                    <? if ($arItem['PROPERTIES']['HIT']['VALUE'] != ''): ?><span
                            class="tag__item tag__item--hit">HIT</span><? endif ?>
                    <? if ($arItem['PROPERTIES']['NEW']['VALUE'] != ''): ?><span
                            class="tag__item tag__item--new">NEW</span><? endif ?>

                </div>

                <div class="c-g-item__shadow"></div>
            </div>
        <? } ?>
        <? if (!empty($arResult["UF_IMAGE"])) { ?>
            <div class="c-g-banner">
                <a href="<?= $arResult["UF_BANNER_LINK"] ?>" class="c-g-banner__img">
                    <img src="<?= $arResult["UF_IMAGE"] ?>" alt="<?= $arResult["UF_DESCRIPTION"] ?>"
                         title="<?= $arResult["UF_DESCRIPTION"] ?>">
                </a>
                <a href="<?= $arResult["UF_BANNER_LINK"] ?>" class="c-g-banner__link">
                    <?= $arResult["UF_DESCRIPTION"] ?>
                </a>
            </div>
        <? } ?>
    </div>


</div>

<script type="text/javascript">
    if(!window.isInitScriptCatalogInTemplates) {
        window.isInitScriptCatalogInTemplates = true;
        BX.message({
            BTN_MESSAGE_BASKET_REDIRECT: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_BASKET_REDIRECT'); ?>',
            BASKET_URL: '<? echo $arParams["BASKET_URL"]; ?>',
            ADD_TO_BASKET_OK: '<? echo GetMessageJS('ADD_TO_BASKET_OK'); ?>',
            TITLE_ERROR: '<? echo GetMessageJS('CT_BCS_CATALOG_TITLE_ERROR') ?>',
            TITLE_BASKET_PROPS: '<? echo GetMessageJS('CT_BCS_CATALOG_TITLE_BASKET_PROPS') ?>',
            TITLE_SUCCESSFUL: '<? echo GetMessageJS('ADD_TO_BASKET_OK'); ?>',
            BASKET_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCS_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
            BTN_MESSAGE_SEND_PROPS: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_SEND_PROPS'); ?>',
            BTN_MESSAGE_CLOSE: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE') ?>',
            BTN_MESSAGE_CLOSE_POPUP: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE_POPUP'); ?>',
            COMPARE_MESSAGE_OK: '<? echo GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_OK') ?>',
            COMPARE_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_UNKNOWN_ERROR') ?>',
            COMPARE_TITLE: '<? echo GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_TITLE') ?>',
            BTN_MESSAGE_COMPARE_REDIRECT: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT') ?>',
            SITE_ID: '<? echo SITE_ID; ?>',
            BUY_URL: '<?=$arResult['~ADD_URL_TEMPLATE']?>'
        });
    }
    if (window.frameCacheVars !== undefined) {
        BX.addCustomEvent("onFrameDataReceived" , function(json) {
            new Blazy({
                src: 'data-src',
                selector: '.lazy',
                successClass: 'init',
                offset: 100,
            });
        });
    } else {
        BX.ready(function() {
            new Blazy({
                src: 'data-src',
                selector: '.lazy',
                successClass: 'init',
                offset: 100,
            });
        });
    }
</script>

<? $this->SetViewTarget("catalog-section-pager"); ?>
<div class="pagination">
    <? if ($arParams["DISPLAY_BOTTOM_PAGER"]) {
        echo $arResult["NAV_STRING"];
    } ?>
</div>
<? $this->EndViewTarget(); ?>

<? if ($arResult['SECTION_SEO_TEXT'] && !isset($_REQUEST['PAGEN_3'])) { ?>
    <? $this->SetViewTarget("catalog_section_seotext"); ?>
    <? //данный код будет перемещен в контейнер "catalog_section_seotext" в footer.php?>
    <?= $arResult['SECTION_SEO_TEXT'] ?>
    <? $this->EndViewTarget(); ?>
<? } ?>

<? $frame->end(); ?>
