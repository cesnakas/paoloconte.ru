<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<div id="delivery_info_<?=$arParams["DELIVERY_ID"]?>" class="btn btn--transparent">
    <a href="javascript:void(0)"
       onClick="deliveryCalcProceed(<?=htmlspecialcharsbx($arResult["JS_PARAMS"])?>)">
        <?=GetMessage('SADC_DOCALC')?></a>
</div>
<div id="wait_container_<?=$arParams["DELIVERY_ID"]?>" class="btn btn--transparent" style="margin-left:10px;display: none;"></div>