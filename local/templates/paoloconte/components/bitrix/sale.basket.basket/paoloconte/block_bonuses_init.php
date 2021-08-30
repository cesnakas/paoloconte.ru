<?php

use Citfact\CloudLoyalty\DataLoyalty;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$msgCountValue = 'Можно списать';
if (array_key_exists('maxToApplyForThisOrder', $bonusData) && $bonusData['maxToApplyForThisOrder'] > 0 || !$USER->IsAuthorized()) {
    $checked = '';
    $disabled = '';
    $pointer = '';
    $arResult["allSumCloud"] = $arResult["allSum"];
    if (DataLoyalty::getInstance()->getUseCloudScore() == "Y") {
        $checked = ' checked';
        $pointer = ' style="cursor:default"';
        $discounts = ($arResult["PROMOCODE_DISCOUNT"] > 0)
            ? $arResult["PROMOCODE_DISCOUNT"] + $bonusData['maxToApplyForThisOrder']
            : $bonusData['maxToApplyForThisOrder'];
        $totalSumBlock = '<span class="rouble">' . number_format($arResult["allSumCloud"] - $discounts, 0, '', ' ') . '</span>';
        $priceWrapperStart = '<div class="basket-item-price__old">';
        $priceWrapperEnd = '</div>';
        $msgCountValue = 'Будет списано';
    }
    if (!$USER->IsAuthorized()) {
        $disabled = ' disabled';
    }
}
