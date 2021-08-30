<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>

<script type="text/javascript">
    window.idMySelfDelivery = '<?=ID_DELIVERY_STORE?>';
    function fShowStore(id, showImages, formWidth, siteId)
    {
        var strUrl = '<?=$templateFolder?>' + '/map.php';
        var strUrlPost = 'delivery=' + id + '&showImages=' + showImages + '&siteId=' + siteId;

        var storeForm = new BX.CDialog({
            'title': '<?=GetMessage('SOA_ORDER_GIVE')?>',
            head: '',
            'content_url': strUrl,
            'content_post': strUrlPost,
            'width': formWidth,
            'height':450,
            'resizable':false,
            'draggable':false
        });

        var button = [
            {
                title: '<?=GetMessage('SOA_POPUP_SAVE')?>',
                id: 'crmOk',
                'action': function ()
                {
                    GetBuyerStore();
                    BX.WindowManager.Get().Close();
                }
            },
            BX.CDialog.btnCancel
        ];
        storeForm.ClearButtons();
        storeForm.SetButtons(button);
        storeForm.Show();
    }

    function GetBuyerStore()
    {
        BX('BUYER_STORE').value = BX('POPUP_STORE_ID').value;
        //BX('ORDER_DESCRIPTION').value = '<?=GetMessage("SOA_ORDER_GIVE_TITLE")?>: '+BX('POPUP_STORE_NAME').value;
        BX('store_desc').innerHTML = BX('POPUP_STORE_NAME').value;
        BX.show(BX('select_store'));
    }

    function showExtraParamsDialog(deliveryId)
    {
        var strUrl = '<?=$templateFolder?>' + '/delivery_extra_params.php';
        var formName = 'extra_params_form';
        var strUrlPost = 'deliveryId=' + deliveryId + '&formName=' + formName;

        if(window.BX.SaleDeliveryExtraParams)
        {
            for(var i in window.BX.SaleDeliveryExtraParams)
            {
                strUrlPost += '&'+encodeURI(i)+'='+encodeURI(window.BX.SaleDeliveryExtraParams[i]);
            }
        }

        var paramsDialog = new BX.CDialog({
            'title': '<?=GetMessage('SOA_ORDER_DELIVERY_EXTRA_PARAMS')?>',
            head: '',
            'content_url': strUrl,
            'content_post': strUrlPost,
            'width': 500,
            'height':200,
            'resizable':true,
            'draggable':false
        });

        var button = [
            {
                title: '<?=GetMessage('SOA_POPUP_SAVE')?>',
                id: 'saleDeliveryExtraParamsOk',
                'action': function ()
                {
                    insertParamsToForm(deliveryId, formName);
                    BX.WindowManager.Get().Close();
                }
            },
            BX.CDialog.btnCancel
        ];

        paramsDialog.ClearButtons();
        paramsDialog.SetButtons(button);
        //paramsDialog.adjustSizeEx();
        paramsDialog.Show();
    }

    function insertParamsToForm(deliveryId, paramsFormName)
    {
        var orderForm = BX("ORDER_FORM"),
            paramsForm = BX(paramsFormName);
        wrapDivId = deliveryId + "_extra_params";

        var wrapDiv = BX(wrapDivId);
        window.BX.SaleDeliveryExtraParams = {};

        if(wrapDiv)
            wrapDiv.parentNode.removeChild(wrapDiv);

        wrapDiv = BX.create('div', {props: { id: wrapDivId}});

        for(var i = paramsForm.elements.length-1; i >= 0; i--)
        {
            var input = BX.create('input', {
                    props: {
                        type: 'hidden',
                        name: 'DELIVERY_EXTRA['+deliveryId+']['+paramsForm.elements[i].name+']',
                        value: paramsForm.elements[i].value
                    }
                }
            );

            window.BX.SaleDeliveryExtraParams[paramsForm.elements[i].name] = paramsForm.elements[i].value;

            wrapDiv.appendChild(input);
        }

        orderForm.appendChild(wrapDiv);

        BX.onCustomEvent('onSaleDeliveryGetExtraParams',[window.BX.SaleDeliveryExtraParams]);
    }
</script>

<input type="hidden" name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult["BUYER_STORE"]?>" />



<div class="basket-params" id="delivery-params">
    <?
    $countShowDelivery = 0;
    if(!empty($arResult["DELIVERY"]))
    {
        $width = ($arParams["SHOW_STORES_IMAGES"] == "Y") ? 850 : 700;
        ?>
        <span class="basket-params__title"><?=GetMessage("SOA_TEMPL_DELIVERY")?></span>
        <div class="basket-params__inner">
            <?
            global $BLACK_LIST_DELIVERY_ID;
            $checkedDeliveryId = 0;
            foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery)
            {
                if (checkUserInBlackList($USER)) {
                    if (in_array($arDelivery['ID'], $BLACK_LIST_DELIVERY_ID)){
                        continue;
                    }
                }

                ?>
                <div class="basket-params__item">
                    <input
                        type="radio"
                        class="basket-params__input"
                        id="ID_DELIVERY_<?=$delivery_id?>"
                        name="<?=htmlspecialcharsbx($arDelivery["FIELD_NAME"])?>"
                        value="<?=$delivery_id?>"
                        <?=$arDelivery["CHECKED"] == "Y" ? "checked=\"checked\"" : "";?>
                        onclick="submitForm();"
                    />
                    <label class="basket-params__label" for="ID_DELIVERY_<?=$delivery_id?>">

                        <?
                        if (count($arDelivery["LOGOTIP"]) > 0):

                            $arFileTmp = CFile::ResizeImageGet(
                                $arDelivery["LOGOTIP"]["ID"],
                                array("width" => "95", "height" =>"55"),
                                BX_RESIZE_IMAGE_PROPORTIONAL,
                                true
                            );

                            $deliveryImgURL = $arFileTmp["src"];
                        else:
                            $deliveryImgURL = $templateFolder."/images/logo-default-d.gif";
                        endif;

                        ?>
                        <div class="bx_logotype" onclick="BX('ID_DELIVERY_<?=$delivery_id?>').checked=true;submitForm();">
                            <span style='background-image:url(<?=$deliveryImgURL?>);'></span>
                        </div>

                        <? if ($arDelivery["CHECKED"] == "Y") {
                            $checkedDeliveryId = $arDelivery['ID']
                            ?>
                            <div class="select-item selected"></div>
                        <? } else { ?>
                            <div class="select-item"></div>
                        <? } ?>

                        <span class="basket__title" onclick="BX('ID_DELIVERY_<?=$delivery_id?>').checked=true;submitForm();" data-deliveryId = "<?=$delivery_id?>">
                            <?=(!empty($arDelivery["OWN_NAME"]))?htmlspecialcharsbx($arDelivery["OWN_NAME"]):htmlspecialcharsbx($arDelivery["NAME"]);?>
                        </span>

                        <? if ($arDelivery["CHECKED"] == "Y") { ?>
                            <div class="basket-params__text">
                                <p onclick="BX('ID_DELIVERY_<?=$delivery_id?>').checked=true;submitForm();">
                                    <?=nl2br($arDelivery["DESCRIPTION"])?>
                                </p>

                                <? if (in_array($delivery_id, array(SDEK_PROFILE_PICKUP, SDEK_PROFILE_PICKUP_ONLINE))
                                    && $arDelivery["CHECKED"] == "Y" && $arDelivery["PRICE"]) { ?>
                                    <div id="choose_pickup" class=""></div> <br>
                                <? } ?>

                                <? if (in_array($delivery_id, array(50)) && $arDelivery["CHECKED"] == "Y"  && $arDelivery["PRICE"]) { ?>
                                    <div id="choose_pickup_iml"></div>
                                <? } ?>

                                <? if ($arDelivery["ID"] != ID_DELIVERY_STORE): //если не самовывоз из магазина?>

                                <span class="bx_result_price"><? /* click on this should not cause form submit */
                                    if (!in_array($delivery_id, array(SDEK_PROFILE_COURIER_FREE, SDEK_PROFILE_PICKUP_ONLINE))):
                                        $APPLICATION->IncludeComponent('bitrix:sale.ajax.delivery.calculator', '', array(
                                            "NO_AJAX" => $arParams["DELIVERY_NO_AJAX"],
                                            "DELIVERY" => $delivery_id,
                                            "DELIVERY_ID" => $delivery_id,
                                            "ORDER_WEIGHT" => $arResult["ORDER_WEIGHT"],
                                            "ORDER_PRICE" => $arResult["ORDER_PRICE"],
                                            "LOCATION_TO" => $arResult["USER_VALS"]["DELIVERY_LOCATION"],
                                            "LOCATION_ZIP" => $arResult["USER_VALS"]["DELIVERY_LOCATION_ZIP"],
                                            "CURRENCY" => $arResult["BASE_LANG_CURRENCY"],
                                            "ITEMS" => $arResult["BASKET_ITEMS"],
                                        ), null, array('HIDE_ICONS' => 'Y'));
                                    endif;
                                    ?></span>
                                <? else:?>
                                    <?if (!empty($arDelivery["STORE"])): ?>
                                        <select name="delivery_store" id="delivery_store" onchange='selfDeliveryManager.check();'>
                                            <?foreach ($arDelivery["STORE"] as $store): ?>
                                                <? if (array_key_exists($store,$arResult["DELIVERY_STORE_LIST"])):?>
                                                    <option value="<?=$arResult["DELIVERY_STORE_LIST"][$store]["SHOPS"][0]["ID"]?>"
                                                            data-id="<?=$store?>"
                                                            data-address="<?=$arResult["DELIVERY_STORE_LIST"][$store]["SHOPS"][0]["PROP"]['ADDRESS']['VALUE']?>">
                                                        <?=$arResult["DELIVERY_STORE_LIST"][$store]["SHOPS"][0]["NAME"]?>
                                                    </option>
                                                <?endif;?>
                                            <? endforeach;?>
                                        </select>
                                    <? endif;?>
                                <? endif;?>
                            </div>
                        <? } ?>
                    </label>
                </div>
                <?
                $countShowDelivery++;
            } ?>
        </div>
    <?
    }
    ?>
    <? if($countShowDelivery <= 0) { ?>
        <span style="color: #d60000" id="no_deliveries">
            Сумма размеров или веса заказа превышает ограничения доставок! <br>
            Пожалуйста, уменьшите позиции вашего заказа.
        </span>
    <? } ?>
</div>
<?php
$deliveryIdForShowAddressFields = \Citfact\Core::getDeliveryIdForShowAddressFields();
if (empty($deliveryIdForShowAddressFields)) {
    \Citfact\Core::setDeliveryIdForShowAddressFields([18, 51, 37]);
}
if (in_array($checkedDeliveryId, $deliveryIdForShowAddressFields)) { ?>
    <div class="order-form__address">
        <?php foreach ($arResult["ORDER_PROP"]['HOME_PROPS'] as $prop) { ?>
            <div class="form__item form__item--<?= $prop['CODE'] ?>">
                <div class="bx_block r3x1">
                    <label for="ORDER_PROP_<?= $prop['ID'] ?>" class="form__label"><?= $prop['NAME'] ?></label>
                    <input type="text" maxlength="250" size="" value="<?= $prop['VALUE'] ?>" name="ORDER_PROP_<?= $prop['ID'] ?>" id="ORDER_PROP_<?= $prop['ID'] ?>"
                           class="property-<?= $prop['CODE'] ?>">
                </div>
            </div>
        <?php } ?>
    </div>
<? } ?>

<div class="basket-bottom__comment">
    <div class="form__item">
        <label for="ORDER_DESCRIPTION" class="form__label">Комментарий</label>
        <textarea name="ORDER_DESCRIPTION"
                  id="ORDER_DESCRIPTION"><?=$arResult["USER_VALS"]["ORDER_DESCRIPTION"]?></textarea>
    </div>
</div>