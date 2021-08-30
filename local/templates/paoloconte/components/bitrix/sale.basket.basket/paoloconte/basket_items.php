<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?global $USER;
use Bitrix\Sale\DiscountCouponsManager;
use Citfact\CloudLoyalty\DataLoyalty;
use Citfact\CloudLoyalty\Events;
use Citfact\CloudLoyalty\OperationManager;
use Citfact\UserBasket\UserBasketHelper;

echo ShowError($arResult["ERROR_MESSAGE"]);

$bDelayColumn  = false;
$bDeleteColumn = false;
$bWeightColumn = false;
$bPropsColumn  = false;
$bPriceType    = false;
if (!$USER->IsAuthorized()){
    DataLoyalty::getInstance()->setUseCloudScore("N");
}

if ($normalCount > 0):
$ids = [];
foreach ($arResult["GRID"]["ROWS"] as $arItem)
{
    $ids[$arItem['CATALOG']['PROPERTY_CML2_LINK_VALUE']] = $arItem['CATALOG']['PROPERTY_CML2_LINK_VALUE'];
}

$userBasketHelper = new UserBasketHelper();
$itemsInBaskets = $userBasketHelper->getCountUserByBasketProductIds($ids);

if ($_SESSION[ 'CATALOG_USER_COUPONS' ])
{
    if (count($_SESSION[ 'CATALOG_USER_COUPONS' ])>1)
    {
        $_SESSION[ 'CATALOG_USER_COUPONS' ]['0'] = $_SESSION[ 'CATALOG_USER_COUPONS' ]['1'];
    }
}elseif ($_SESSION['CLOUD_LOYALTY_OPERATION_MANAGER']['promoCodes'])
{
    if (count($_SESSION['CLOUD_LOYALTY_OPERATION_MANAGER']['promoCodes'])>1)
    {
        $_SESSION[ 'CATALOG_USER_COUPONS' ]['0'] = $_SESSION['CLOUD_LOYALTY_OPERATION_MANAGER']['promoCodes']['1'];
    }else
    {
        $_SESSION[ 'CATALOG_USER_COUPONS' ]['0'] = $_SESSION['CLOUD_LOYALTY_OPERATION_MANAGER']['promoCodes']['0'];
    }
}

if (empty($arResult['COUPON_LIST']))
{
    $arResult['COUPON_LIST'] = $_SESSION[ 'CATALOG_USER_COUPONS' ];
}
if (!empty($arResult['COUPON_LIST'])) {
    foreach ($arResult['COUPON_LIST'] as $oneCoupon) {
        $couponClass = 'disabled';
        switch ($oneCoupon['STATUS']) {
            case DiscountCouponsManager::STATUS_NOT_FOUND:
            case DiscountCouponsManager::STATUS_FREEZE:
                $couponClass = 'bad';
                break;
            case DiscountCouponsManager::STATUS_APPLYED:
                $couponClass = 'good';
                break;
        }
        if ($oneCoupon['COUPON'] == OperationManager::getLastAppliedPromoCode()) {
            $couponClass = 'good';
        };
        if ($couponClass != disabled)
        {
            $marker = 0;
            if (isset($_SESSION[ 'CATALOG_USED_COUPONS' ]))
            {
                foreach ($_SESSION[ 'CATALOG_USED_COUPONS' ] as $coupon){
                    if ($coupon['COUPON'] == $oneCoupon['COUPON'])
                    {
                        $marker = 1;
                        break;
                    }
                }
                if ($marker=='0')
                {
                    $oneCoupon['COUPON_CLASS'] = $couponClass;
                    $_SESSION[ 'CATALOG_USED_COUPONS' ]['0'] = $oneCoupon;
                }
            }
            else
            {
                $oneCoupon['COUPON_CLASS'] = $couponClass;
                $_SESSION[ 'CATALOG_USED_COUPONS' ]['0'] = $oneCoupon;
            }

        }
    }
}
else
{
    $couponClass = "";
}

?>
<div class="basket-item-wrap" id="basket_items">
    <?
    foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):
        if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y"){

            //favoriteIcon
            if(!empty($arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']))
                $favoritePhoto = $arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL'];
            else
                $favoritePhoto = $arResult['NOPHOTO'];
            ?>

            <div class="basket-item" id="<?=$arItem["ID"]?>" data-product-id="<?=$arItem["PRODUCT_ID"]?>">
                <div class="basket-item__cell basket-item__cell--img">
                    <?if($arItem['CATALOG_PHOTO']):?>
                        <img src="<?=$arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>">
                    <?else:?>
                        <img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
                    <?endif;?>
                </div>
                <div class="basket-item__cell basket-item__cell--info">
                    <? if (isset($arItem['CATALOG']['PROPERTY_CML2_ARTICLE_VALUE']) &&
                        !empty($arItem['CATALOG']['PROPERTY_CML2_ARTICLE_VALUE'])) { ?>
                        <div class="basket-item__code">
                            Артикул:
                            <span id="bx_article_<?= $arItem['ID'] ?>"><?= $arItem['CATALOG']['PROPERTY_CML2_ARTICLE_VALUE'] ?></span>
                        </div>
                        <?
                        $nameFormatted = \Citfact\Tools::clearNameProd($arItem["NAME"], $arItem['CATALOG']['PROPERTY_CML2_ARTICLE_VALUE']);
                    } else {
                        $nameFormatted = \Citfact\Tools::clearNameProd($arItem["NAME"], '');
                    }
                    ?>
                    <?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?>
                    <a href="<?=$arItem["DETAIL_PAGE_URL"] ?>" target="_blank"><?endif;?>
                        <span class="basket-item__title" id="bx_name_<?= $arItem['ID'] ?>">
                            <?= $nameFormatted ?>
                        </span>
                        <?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
                    <? if ($itemsInBaskets[ $arItem['CATALOG']['PROPERTY_CML2_LINK_VALUE']] > 0) {
                    ?>
                        <div class="basket-item__abandoned">
                            <? echo 'Этот товар в корзине ещё у ' . $itemsInBaskets[$arItem['CATALOG']['PROPERTY_CML2_LINK_VALUE']] . ' человек(а)'; ?>
                        </div>
                    <?}?>
                    <div class="basket-item__not-avail-delivery" data-toggle-wrap>
                        <span>*Недоступно для выбранного типа доставки.</span><br>
                        <span data-toggle-btn>Подробнее</span>
                        <span data-toggle-list style="display: none">Пожалуйста, выберите другой тип доставки или удалите товар из корзины и оформите его отдельным заказом.</span>
                    </div>

                    <div class="basket-item-size" data-toggle-wrap style="display:none;">
                    <?
                    foreach ($arItem["PROPS"] as $val):
                        $id = '';
                        if ($val['CODE'] == 'RAZMER')
                            $id = 'bx_razmer_'.$arItem['ID'];
                        echo '<div class="basket-item-size__current">'.$val["NAME"].': <span id="'.$id.'">'.$val["VALUE"].'</span></div>';
                    endforeach;
                    ?>


                        <?if (is_array($arItem["SKU_DATA"]) && !empty($arItem["SKU_DATA"])):?>
                        <?
                        foreach ($arItem["SKU_DATA"] as $propId => $arProp):?>
                            <?foreach ($arProp["VALUES"] as $valueId => $arSkuValue){
                                if (array_key_exists($arSkuValue["NAME"], $arResult['SIZES_AMOUNT'][$arItem['CATALOG']['PROPERTY_CML2_LINK_VALUE']])){
                                    echo '<div class="basket-item-size__link" data-toggle-btn>изменить</div>';
                                    break;
                                }
                            } ?>
                        <?endforeach;?>
                        <?
                        foreach ($arItem["SKU_DATA"] as $propId => $arProp):
                            // if property contains images or values
                            $isImgProperty = false;
                            if (array_key_exists('VALUES', $arProp) && is_array($arProp["VALUES"]) && !empty($arProp["VALUES"]))
                            {
                                foreach ($arProp["VALUES"] as $id => $arVal)
                                {
                                    if (isset($arVal["PICT"]) && !empty($arVal["PICT"]) && is_array($arVal["PICT"])
                                        && isset($arVal["PICT"]['SRC']) && !empty($arVal["PICT"]['SRC']))
                                    {
                                        $isImgProperty = true;
                                        break;
                                    }
                                }
                            }
                            $countValues = count($arProp["VALUES"]);
                            $full = ($countValues > 5) ? "full" : "";

                            if (!$isImgProperty): // iblock element relation property
                                ?>

                                <div class="basket-item-size__list hide"
                                     id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>"
                                     data-toggle-list>

                                    <div class="basket-item-size__items">
                                        <?
                                        foreach ($arProp["VALUES"] as $valueId => $arSkuValue):
                                            // Не показываем предложения, которых нет на складе интернет-магазина (см. result_modifier.php)
                                            if (!array_key_exists($arSkuValue["NAME"], $arResult['SIZES_AMOUNT'][$arItem['CATALOG']['PROPERTY_CML2_LINK_VALUE']])){
                                                continue;
                                            }
                                            $selected = "";
                                            foreach ($arItem["PROPS"] as $arItemProp):
                                                if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
                                                {
                                                    if ($arItemProp["VALUE"] == $arSkuValue["NAME"])
                                                        //$selected = "bx_active active";
                                                        $selected = "active";
                                                }
                                            endforeach;
                                            ?>
                                            <label class="sku_prop <?=$selected?>"
                                                   data-value-id="<?=$arSkuValue["NAME"]?>"
                                                   data-element="<?=$arItem["ID"]?>"
                                                   data-property="<?=$arProp["CODE"]?>"
                                            >
                                                <?=$arSkuValue["NAME"]?>
                                                <input type="radio" value="" name="r<?=$arItem['ID']?>">
                                            </label>
                                            <?
                                        endforeach;
                                        ?>
                                    </div>
                                </div>
                                <?
                            endif;
                        endforeach;?>
                    <?endif;?>

                    </div>
                </div>
                <div class="basket-item__cell basket-item__cell--size">
                    <span class="basket-item__title-cell">Размер</span>
                        <?
                        foreach ($arItem["SKU_DATA"] as $propId => $arProp) {
                            $countSizeForBuy = 0;
                            foreach ($arProp["VALUES"] as $valueId => $arSkuValue) {
                                if (!array_key_exists($arSkuValue["NAME"], $arResult['SIZES_AMOUNT'][$arItem['CATALOG']['PROPERTY_CML2_LINK_VALUE']])) {
                                    continue;
                                }
                                $countSizeForBuy++;
                            }
                            if ($countSizeForBuy == 0) {?>
                                <select class="select-size">
                                    <?
                                    foreach ($arProp["VALUES"] as $valueId => $arSkuValue) {
                                        $selected = "";
                                        foreach ($arItem["PROPS"] as $arItemProp):
                                            if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"]) {
                                                if ($arItemProp["VALUE"] == $arSkuValue["NAME"])
                                                    //$selected = "bx_active active";
                                                    $selected = "selected";
                                            }
                                        endforeach;
                                        if ($selected == "selected") {
                                            ?>
                                            <option value="<?= $arSkuValue["NAME"] ?>" <?= $selected ?>
                                                    data-element="<?= $arItem["ID"] ?>"
                                                    data-product-id="<?= $arItem["PRODUCT_ID"] ?>"
                                                    data-property="<?= $arProp["CODE"] ?>"><?= $arSkuValue["NAME"] ?></option>
                                            <?
                                        }
                                    }
                                    ?>
                                </select>
                                <?
                                continue;
                            } else { ?>
                                <select class="select-size">
                                    <?
                                    foreach ($arProp["VALUES"] as $valueId => $arSkuValue) {
                                        // Не показываем предложения, которых нет на складе интернет-магазина (см. result_modifier.php)
                                        if (!array_key_exists($arSkuValue["NAME"], $arResult['SIZES_AMOUNT'][$arItem['CATALOG']['PROPERTY_CML2_LINK_VALUE']])) {
                                            continue;
                                        }
                                        $selected = "";
                                        foreach ($arItem["PROPS"] as $arItemProp):
                                            if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"]) {
                                                if ($arItemProp["VALUE"] == $arSkuValue["NAME"])
                                                    //$selected = "bx_active active";
                                                    $selected = "selected";
                                            }
                                        endforeach;
                                        ?>
                                        <option value="<?= $arSkuValue["NAME"] ?>" <?= $selected ?>
                                                data-element="<?= $arItem["ID"] ?>"
                                                data-product-id="<?= $arItem["PRODUCT_ID"] ?>"
                                                data-property="<?= $arProp["CODE"] ?>"><?= $arSkuValue["NAME"] ?></option>
                                        <?
                                    }
                                    ?>
                                </select>
                                <?
                            }
                        }
                        ?>
                </div>
                <div class="basket-item__cell basket-item__cell--amount" data-id="<?=$arItem["ID"]?>" data-product-id="<?=$arItem['PRODUCT_ID']?>">
                    <span class="basket-item__title-cell">Количество</span>
                    <div class="basket-amount">
                        <div class="btn-amount btn-minus <?= ($arItem['QUANTITY'] <= 1) ? 'disabled' : '' ?>"></div><?/*класс disabled для недоступного количества*/?>
                        <input type="text" class="quantityInputs" data-id="<?=$arItem["ID"]?>" data-product-id="<?=$arItem['PRODUCT_ID']?>"
                               id="QUANTITY_<?=$arItem["ID"]?>" value="<?=$arItem['QUANTITY']?>"><?/*класс disabled для недоступного количества*/?>
                        <div class="btn-amount btn-plus"></div><?/*класс disabled для недоступного количества*/?>
                        <span class="tooltip"><?/*класс show для отображения подсказки*/?>
                            <span class="tooltip__inner">
                                Максимальное количество товара в корзине 3
                            </span>
                        </span>
                    </div>
                    <input type="hidden" class="quantityInputsHidden" data-id="<?=$arItem["ID"]?>" data-product-id="<?=$arItem['PRODUCT_ID']?>"
                           id="QUANTITY_INPUT_<?=$arItem["ID"]?>" value="<?=$arItem['QUANTITY']?>">
                </div>
                <?
                $priceOld = '';
                $priceNew = '';
                if ($arResult["PROMOCODE_DISCOUNT"] > 0) {
                    $discountValue = 0;
                    foreach ($arResult["BASKET_CLOUD_PROMO_DATA"]["PRODUCTS"] as $product)
                    {
                        if ($product["BASKET_ITEM_ID"] == $arItem["ID"]){
                            $discountValue = $product["CLOUD_PROMO"];
                        }
                    }
                    if ($discountValue > 0)
                    {
                        $priceOld = ' basket-item-price__old';
                        $priceNew = '<div class="rouble">'.number_format(($arItem['SUM_VALUE']-$discountValue), 0, '', ' ').'</div>';
                    }
                }
                if (DataLoyalty::getInstance()->getUseCloudScore() == "Y") {
                    $discountValue = 0;
                    foreach ($arResult["BASKET_CLOUD_DATA"]["PRODUCTS"] as $product) {
                        if ($product["BASKET_ITEM_ID"] == $arItem["ID"]) {
                            $discountValue = ($arResult["PROMOCODE_DISCOUNT"] > 0)
                                ? $arResult["BASKET_CLOUD_PROMO_DATA"]["PRODUCTS"][$arItem["PRODUCT_ID"]]["CLOUD_PROMO"] + $product["CLOUD_DISCOUNT"]
                                : $product["CLOUD_DISCOUNT"];
                        }
                    }
                    if ($discountValue > 0) {
                        $priceOld = ' basket-item-price__old';
                        $priceNew = '<div class="rouble">' . number_format(($arItem['SUM_VALUE'] - $discountValue), 0, '', ' ') . '</div>';
                    }
                }
                ?>
                <div class="basket-item__cell basket-item__cell--total">
                    <div class="basket-item__cell basket-item__cell--discount">
                        <span class="basket-item__title-cell">Скидка</span>
                        -<?=number_format($arItem["DISCOUNT_PRICE_PERCENT_FORMATED"], 0, '', ' ')?>%
                    </div>
                    <div class="basket-item__cell basket-item__cell--price">
                        <span class="basket-item__title-cell">Цена </span>
                        <div class="basket-item-price">
                            <div class="basket-item-price__current rouble">
                                <?=number_format($arItem["PRICE"], 0, '', ' ')?>
                            </div>
                            <?if (floatval($arItem["DISCOUNT_PRICE_PERCENT"] && $arItem["FULL_PRICE"] != $arItem["PRICE"]) > 0):?>
                                <div class="basket-item-price__old rouble">
                                    <?=number_format($arItem["FULL_PRICE"], 0, '', ' ')?>
                                </div>
                            <?endif;?>
                        </div>
                    </div>
                </div>
                <div class="basket-item__cell basket-item__cell--del">
                    <a href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delete"])?>"
                       onclick="del_el_ecommerce('<?=$arItem["PRODUCT_ID"]?>','<?=$arItem["NAME"]?>')">
                        <span class="plus plus--cross">
                            <svg class="i-icon" width="14" height="14" viewBox="0 0 14 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
<path d="M12.6129 1L1 13" stroke="black" stroke-width="0.7"/>
<path d="M1.3871 1L13 13" stroke="black" stroke-width="0.7"/>
</svg>
                        </span>
                    </a>
                </div>
                <? if ($itemsInBaskets[ $arItem['CATALOG']['PROPERTY_CML2_LINK_VALUE']] > 0) {
                    ?>
                    <div class="basket-item__abandoned mobile">
                        <? echo 'Этот товар в корзине ещё у ' . $itemsInBaskets[$arItem['CATALOG']['PROPERTY_CML2_LINK_VALUE']] . ' человек(а)'; ?>
                    </div>
                <?}?>
                <div class="basket-item__not-avail-delivery mobile" data-toggle-wrap>
                    <span>*Недоступно для выбранного типа доставки.</span>
                    <span data-toggle-btn>Подробнее</span>
                    <span data-toggle-list style="display: none">
                        Пожалуйста выберите другой тип доставки или удалите товар из корзины и оформите его отдельным заказом.
                    </span>
                </div>
            </div>
            <?
        }else{
            echo '<input type="hidden" class="DELAY" name="DELAY_'.$arItem["ID"].'" value="Y"/>';
        }
    endforeach;
    ?>
</div>
<?
if ($arParams["HIDE_COUPON"] != "Y"):
?>
<?
$countCoupons = count($_SESSION[ 'CATALOG_USED_COUPONS' ]);
?>
<div class="basket-coupon" id="coupons_block">

    <div class="basket-coupon__inner">
        <div>
            <?if (!empty($_SESSION['CATALOG_USER_COUPONS'])){?>
            <div class="basket-coupon__code">
                <div class="basket-coupon__form <?=($_SESSION['CATALOG_USED_COUPONS'][$countCoupons-1]['COUPON_CLASS'] == 'good') ? 'hidden' : ''?><?=($_SESSION['CATALOG_USED_COUPONS'][$countCoupons-1]['COUPON_CLASS'] == 'bad') ? 'error' : ''?><?=($_SESSION['CATALOG_USED_COUPONS'][$countCoupons-1]['COUPON_CLASS'] == '') ? '' : ''?>" id=""><?/*TODO класс error для недействительного промокода при вводе*/?>
                    <input type="text"
                           id="coupon"
                           name="COUPON"
                           size="21"
                           placeholder="Введите промокод"
                           value="<?=$_SESSION['CATALOG_USED_COUPONS'][$countCoupons-1]['COUPON']?>"
                           onblur="enterCoupon(); return false;">
                    <a href="#"
                       class="basket-coupon__submit"
                       onclick="enterCoupon(); <?/*window.location.reload(); overlay.show();*/?> return false;">
                    </a>
                    <span>*Промокод недействителен</span>
                </div>
            </div>
            <?}else{?>
                <div class="basket-coupon__code">
                    <div class="basket-coupon__form " id=""><?/*TODO класс error для недействительного промокода при вводе*/?>
                        <input type="text"
                               id="coupon"
                               name="COUPON"
                               size="21"
                               placeholder="Введите промокод"
                               onblur="enterCoupon(); return false;">
                        <a href="#"
                           class="basket-coupon__submit"
                           onclick="enterCoupon(); <?/*window.location.reload(); overlay.show();*/?> return false;">
                        </a>
                        <span>*Промокод недействителен</span>
                    </div>
                </div>
            <?}?>
            <?//endif;?>
            <div class="basket-coupon__inner bx_ordercart">
                    <?
                if (!empty($_SESSION['CATALOG_USED_COUPONS']))
                {?>
                <?if(!empty($_SESSION['CATALOG_USER_COUPONS'])){?>
                <div class="bx_ordercart_coupon basket-coupon__item <?=($_SESSION['CATALOG_USED_COUPONS'][$countCoupons-1]['COUPON_CLASS'] == 'good') ? '' : ' basket-coupon__item--error hidden'?>">
                    Промокод: <span data-promocode="<?=htmlspecialcharsbx($_SESSION['CATALOG_USED_COUPONS'][$countCoupons-1]['COUPON']);?>"><?=htmlspecialcharsbx($_SESSION['CATALOG_USED_COUPONS'][$countCoupons-1]['COUPON']);?></span>
                    <input disabled readonly type="text" name="OLD_COUPON[]"
                           value="<?=htmlspecialcharsbx($_SESSION['CATALOG_USED_COUPONS'][$countCoupons-1]['COUPON']);?>"
                           class="<? echo 'good' ?> hidden">
                    <span class="basket-coupon__remove"
                          data-coupon="<? echo htmlspecialcharsbx($_SESSION['CATALOG_USED_COUPONS'][$countCoupons-1]['COUPON']); ?>">
                        <span class="plus"></span>
                    </span>
                    <!--<div class="bx_ordercart_coupon_notes">
                        <?/*
                        if($oneCoupon['COUPON'] == OperationManager::getLastAppliedPromoCode()) {
                            echo 'Применен';
                        } else
                        if (isset($oneCoupon['CHECK_CODE_TEXT']))
                        {
                            echo (is_array($oneCoupon['CHECK_CODE_TEXT']) ? implode('<br>', $oneCoupon['CHECK_CODE_TEXT']) : $oneCoupon['CHECK_CODE_TEXT']);
                        }
                        */?>
                    </div>-->
                </div>
                <?
                //}
                if ($couponClass != 'good') {
                    echo "<div class='last-promocode-error' style='display: none;'>error</div>";
                }
                unset($couponClass, $oneCoupon);
                }
                /*elseif(!empty($_SESSION[ 'CATALOG_USER_COUPONS' ]['0'])){*/?><!--
                    <div class="bx_ordercart_coupon basket-coupon__item <?/*=($couponClass == 'good') ? '' : ' basket-coupon__item--error hidden'*/?>">
                        Промокод: <span data-promocode="<?/*=htmlspecialcharsbx($oneCoupon['COUPON']);*/?>"><?/*=htmlspecialcharsbx($oneCoupon['COUPON']);*/?></span>
                        <input disabled readonly type="text" name="OLD_COUPON[]"
                               value="<?/*=htmlspecialcharsbx($oneCoupon['COUPON']);*/?>"
                               class="<?/* echo $couponClass; */?> hidden">
                        <span class="basket-coupon__remove"
                              data-coupon="<?/* echo htmlspecialcharsbx($oneCoupon['COUPON']); */?>">
                        <span class="plus"></span>
                    </span>
                    </div>
                --><?/*}*/?>
                <?}?>
                <?foreach($arResult['COUPON_LIST'] as $coupon){
                if ($coupon['DISCOUNT_ACTIVE'] == 'Y') {
                $arResult["COUPON"] = $coupon['COUPON'];
                //if ($USER->IsAdmin()) {
                ?>
                    <div class="modal fade coupon_popup" id="offerInbasketModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 99999;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <button type="button" class="close" data-dismiss="modal"></button>
                                <div class="modal-body">
                                    Был применен промо-код <span class="coupon_this"><?=$arResult["COUPON"];?></span>.<br />
                                    Общая сумма заказа составила <span class="price_this"></span><br />
                                    Для совершения покупки нажмите кнопку "Оформить заказ".
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        BX.ready(function(){
                            var summ_korzin = <?=$arResult['allSum'];?>;
                            var summ_order = $('#order_price_items').data('price');
                            if (!!summ_korzin && !!summ_order && summ_order < summ_korzin && summ_order != 0 && summ_korzin != 0) {
                                $('.coupon_popup .price_this').html($('#order_price_items').data('price_formatted'));
                                $('input[value="<?=$arResult["COUPON"];?>"]').removeClass();
                                $('input[value="<?=$arResult["COUPON"];?>"] + span').removeClass();
                                $('input[value="<?=$arResult["COUPON"];?>"]').addClass('good');
                                $('input[value="<?=$arResult["COUPON"];?>"] + span').addClass('good');
                                $('span[data-coupon="<?=$arResult["COUPON"];?>"] + .bx_ordercart_coupon_notes').html('применен');
                                setInputCoupon();
                                $('.coupon_popup').modal('show');
                            }
                        });
                    </script>
                    <?
                    $arItemIds = [];
                    foreach($arResult["ITEMS"]["AnDelCanBuy"] as $item){
                        $arItemIds[] = $item["PRODUCT_ID"];
                    }?>
                    <?
                    //}
                }
                }
                    ?>
                <?endif;?>
                <?
                require('block_bonuses_init.php');
                $this->SetViewTarget('block_bonuses');
                require('block_bonuses.php');
                $this->EndViewTarget();
                ?>
            </div>
        </div>
    </div>

    <?
    foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):
        $arHeader["name"] = (isset($arHeader["name"]) ? (string)$arHeader["name"] : '');
        if ($arHeader["name"] == '')
            $arHeader["name"] = GetMessage("SALE_".$arHeader["id"]);
        $arHeaders[] = $arHeader["id"];
    endforeach;?>

    <input type="hidden" id="column_headers" value="<?=CUtil::JSEscape(implode($arHeaders, ","))?>" />
    <input type="hidden" id="offers_props" value="<?=CUtil::JSEscape(implode($arParams["OFFERS_PROPS"], ","))?>" />
    <input type="hidden" id="action_var" value="<?=CUtil::JSEscape($arParams["ACTION_VARIABLE"])?>" />
    <input type="hidden" id="quantity_float" value="<?=$arParams["QUANTITY_FLOAT"]?>" />
    <input type="hidden" id="count_discount_4_all_quantity" value="<?=($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N"?>" />
    <input type="hidden" id="price_vat_show_value" value="<?=($arParams["PRICE_VAT_SHOW_VALUE"] == "Y") ? "Y" : "N"?>" />
    <input type="hidden" id="hide_coupon" value="<?=($arParams["HIDE_COUPON"] == "Y") ? "Y" : "N"?>" />
    <input type="hidden" id="coupon_approved" value="N" />
    <input type="hidden" id="use_prepayment" value="<?=($arParams["USE_PREPAYMENT"] == "Y") ? "Y" : "N"?>" />


    <?
    else:
        ?>
        <div id="basket_items_list">
            <table>
                <tbody>
                <tr>
                    <td colspan="<?=$numCells?>" style="text-align:center">
                        <div class=""><?=GetMessage("SALE_NO_ITEMS");?></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <?
    endif;
    ?>
    <script>
        function del_el_ecommerce(id,name) {
            console.log("del "+id);
            window.dataLayer = window.dataLayer || [];
            dataLayer.push({
                "event": "removeFromCart",
                "ecommerce": {
                    "remove": {
                        "products": [
                            {
                                "id": id,
                                "name": name
                            }
                        ]
                    }
                }
            });
        }
        $(window).load(function() {
            $('[data-onload-content]').removeClass('hidden');
            $(document).on('change', '#need_bonus_false', function(event){
                var val = '';
                val = this.checked ? 'Y':'';
                $('#need_bonus_real').val(val);
                submitFormProxy();
            });
        });

        $(document).on('click', '.basket-item__cell--del a', function (e) {
            e.preventDefault();
            var a = $(this);
            BX.showWait();
            $.ajax({
                url: a.attr('href'),
                data: {},
                dataType: "html",
                async: true,
                success: function (data) {
                    a.closest('.basket-item').remove();
                    if ($('.basket-item-wrap .basket-item').length <= 0) {
                        document.location.reload();
                    } else {
                        submitForm();
                    }
                    BX.closeWait();
                },
                error: function () {
                    BX.closeWait();
                }
            })
        });
    </script>

    <?
    $arItemIds = [];
    foreach ($arResult["ITEMS"]["AnDelCanBuy"] as $item) {
        $arGtmItemIds[] = $arResult['ARTICLES'][$item["PRODUCT_ID"]];
    } ?>
    <? if ($arGtmItemIds) { ?>
        <script>
            window.gtmRemarketingTag = {
                pagetype: 'cart',
                prodid: <?=json_encode($arGtmItemIds)?>,
                totalvalue: <?=$arResult['allSum']?>,
            };
        </script>
    <? } ?>
</div>


    
    

    