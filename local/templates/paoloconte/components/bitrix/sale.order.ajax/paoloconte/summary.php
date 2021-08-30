<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

use Citfact\CloudLoyalty\DataLoyalty;
use Citfact\CloudLoyalty\Events;
use Citfact\CloudLoyalty\OperationManager;

$bDefaultColumns = $arResult["GRID"]["DEFAULT_COLUMNS"];
$colspan = ($bDefaultColumns) ? count($arResult["GRID"]["HEADERS"]) : count($arResult["GRID"]["HEADERS"]) - 1;
$bPropsColumn = false;
$bUseDiscount = false;
$bPriceType = false;
$bShowNameWithPicture = ($bDefaultColumns) ? true : false; // flat to show name and picture column in one column

$balance = Events::getBalanceForPage($USER->GetID());

$isNeedInnerPayment = false;
if (DataLoyalty::getInstance()->getUseCloudScore() == 'Y') {
    $isNeedInnerPayment = true;
}
$promoCodeDiscount = \Citfact\CloudLoyalty\OperationManager::getPromoCodeDiscount();
if ($promoCodeDiscount) {
    $arResult['ORDER_TOTAL_PRICE'] -= $promoCodeDiscount;
    $arResult['JS_DATA']['TOTAL']['ORDER_PRICE'] -= $promoCodeDiscount;
}
if (DataLoyalty::getInstance()->getUseCloudScore() == "Y")
{
    $arResult['ORDER_TOTAL_PRICE'] -= $bonusData['maxToApplyForThisOrder'];
    $arResult['JS_DATA']['TOTAL']['ORDER_PRICE'] -= $bonusData['maxToApplyForThisOrder'];
}
?>
<? //pre($arResult);?>
<div class="basket-bottom" data-fix-item="20">
    <div class="basket-bottom__top">
        <div class="basket-bottom__info">
            <div class="no-auth desktop">
                <? if (!$USER->IsAuthorized()) { ?>
                    <div><a href="#" data-toggle="modal" data-target="#enterModal">Войти в личный кабинет</a></div>
                <? } else { ?>
                    <div class="basket-bottom__info-name"><a href="/cabinet/">
                            <div><?=$USER->GetFullName();?></div>
                            <svg class='i-icon'>
                                <use xlink:href='#lk'/>
                            </svg>
                        </a></div>
                <? } ?>
                <div><a href="/help/oplata-i-dostavka/" target="_blank">Доставка и оплата</a></div>
                <div><a href="/help/vozvrat/" target="_blank">Обмен и возврат</a></div>
            </div>
            <div>
                <span><?=GetMessage("SOA_TEMPL_SUM_WEIGHT_SUM")?></span>
                <span class="weight_formated"><?=$arResult["ORDER_WEIGHT_FORMATED"]?></span>
            </div>
            <div>
                <span>Доставка</span>
                <span><?=$arResult["DELIVERY_PRICE"]?>  ₽</span>
            </div>
            <? if ($USER->IsAuthorized()) { ?>
                <div>
                    <span>Доступно Бонусов</span>
                    <span><?= number_format($balance, 0, '', ' ') ?> Б</span>
                </div>
                <? if (OperationManager::getPromoCodeDiscount()) { ?>
                    <div>
                        <span class="">Промокод</span>
                        <span>
                            <? if (OperationManager::getPromoCodeDiscount() != 0) {
                                echo '-';
                            } ?><?= number_format(OperationManager::getPromoCodeDiscount(),
                                0, '', ' ') ?> Б</span>
                    </div>
                <? } ?>
                <?
                if (array_key_exists('maxToApplyForThisOrder', $bonusData)) { ?>
                    <div>
                        <span>Списано Бонусов</span>
                        <span><?
                            if ($isNeedInnerPayment) {
                                if ($bonusData['maxToApplyForThisOrder'] > 0) {
                                    echo '-';
                                }
                                echo number_format($bonusData['maxToApplyForThisOrder'], 0,
                                    '', ' ');
                            } else {
                                echo 0;
                            }
                            ?> Б</span>
                    </div>
                    <?
                }
            }
            ?>
            <div>
                <span>Начислено Бонусов</span>
                <span><?= number_format($bonusData['collected'], 0, '', ' ') ?> Б</span>
            </div>
            <div class="basket-bottom__total">
                <span>Итог</span>
                <span class="price_formated"><?= $isNeedInnerPayment ?  number_format(($arResult["ORDER_TOTAL_PRICE"]), 0, '', ' ') . ' ₽' : number_format(($arResult["ORDER_TOTAL_PRICE"]), 0, '', ' '). ' ₽'?></span>
            </div>
        </div>
    </div>

    <a href="javascript:void();"
       onclick="submitForm('Y', true); return false;"
       id="ORDER_CONFIRM_BUTTON"
       class="btn btn--black">
        Оформить заказ
    </a>
    
    <div class="oferta">
        <?$APPLICATION->IncludeFile(
            SITE_DIR."/include/oferta_order.php",
            Array(),
            Array("MODE"=>"text")
        );?>
    </div>
    
</div>