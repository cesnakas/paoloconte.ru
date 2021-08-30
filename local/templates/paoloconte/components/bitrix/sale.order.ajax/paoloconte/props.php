<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/props_format.php");

use Citfact\CloudLoyalty\DataLoyalty;
use Citfact\CloudLoyalty\Events;
$bonusData = DataLoyalty::getInstance()->getBonusData();
global $USER;
?>
<? // Чтобы не дублировались профили покупателя, выводим скрытое поле с ID профиля
if (count($arResult["ORDER_PROP"]["USER_PROFILES"]) == 1) {
    foreach ($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles) {
        //echo "<strong>".$arUserProfiles["NAME"]."</strong>";
        ?>
        <input type="hidden" name="PROFILE_ID" id="ID_PROFILE_ID" value="<?= $arUserProfiles["ID"] ?>"/>
        <?
    }
} else {
    ?>
    <select name="PROFILE_ID" id="ID_PROFILE_ID" onChange="SetContact(this.value)" style="display: none;">
        <?
        foreach ($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles) {
            ?>
            <option value="<?= $arUserProfiles["ID"] ?>"<?
            if ($arUserProfiles["CHECKED"] == "Y") echo " selected"; ?>><?= $arUserProfiles["NAME"] ?></option>
            <?
        }
        ?>
    </select>
    <?
}
?>
<?
global $USER;
$rsUser = CUser::GetByID($USER->GetID());
$arUser = $rsUser->Fetch();
?>
<? if ($bonusData['maxToApplyForThisOrder']) { ?>
    <input id="need_bonus_real"
           type="hidden"
           name="<?= Events::PROPERTY_NEED_INNER_PAYMENT_CODE ?>"
           value="<?= $_POST[Events::PROPERTY_NEED_INNER_PAYMENT_CODE] == 'Y' ? 'Y' : '' ?>">
<? } ?>
    <div class="bx_section personal-box <? if ($USER->IsAuthorized() === true
        && ((!empty($arUser['NAME']) && !empty($arUser['SECOND_NAME']) && !empty($arUser['LAST_NAME']))
            && (!empty($arUser['PERSONAL_PHONE']) && !empty($arUser['UF_LOYALTY_CARD'])))) {
//        echo 'hidden';
    } ?>">
        <div class="box-title" id="order_errors_cont_personal_title"></div>
        <div id="sale_order_props" <?= ($bHideProps && $_POST["showProps"] != "Y") ? "style='display:none;'" : '' ?>>
            <?
            if (!empty($arResult['ERROR_KEY']['USER_PROPS_N']) && $USER->IsAuthorized() !== true) { ?>
                <div class="order-errors-cont form__item form__item--error" id="order_errors_cont_personal" style="padding: 0px;">
                    <?
                    foreach ($arResult['ERROR_KEY']['USER_PROPS_N'] as $idProp => $v) {
                        if (in_array(getCodePropByID($arResult["ORDER_PROP"]["USER_PROPS_N"], $idProp), ['EMAIL']))
                            continue;
                        echo ShowError($arResult["ERROR"][$v]);
                    }
                    ?>
                </div>
            <? } ?>
            <?
            // USER_PROPS_N - не входят в профиль
            // USER_PROPS_Y - входят в профиль
            $arResult['ERROR_KEY']['MESSAGES'] = $arResult["ERROR"];
            PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_N"], $arParams["TEMPLATE_LOCATION"], $arUser, $arResult['ERROR_KEY']);
            PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_Y"], $arParams["TEMPLATE_LOCATION"], $arUser, $arResult['ERROR_KEY']);
            ?>
        </div>
    </div>

    <div class="personal-box">
        <div class="box-title" id="order_errors_cont_delivery_title"></div>
        <? // Если нашли сохраненные адреса, показываем селект для выбора, адреса достаются в result_modifier.php?>
        <? if (!empty($arResult['ERROR_KEY']['USER_PROPS_Y']) || !empty($arResult['ERROR_KEY']['RELATED'])) { ?>
            <div class="order-errors-cont" id="order_errors_cont_delivery" style="padding: 0px;">
                <? foreach ($arResult['ERROR_KEY']['USER_PROPS_Y'] as $key => $v)
                    echo ShowError($arResult["ERROR"][$v]); ?>
                <? foreach ($arResult['ERROR_KEY']['RELATED'] as $key => $v)
                    echo ShowError($arResult["ERROR"][$v]); ?>
            </div>
        <? } ?>
        <?
        $has_addresses = false;
        if (!empty($arResult['ADDRESSES'])) {
            $has_addresses = true;
        }
        $saved_location = '';
        $saved_address = '';
        ?>

        <? if ($has_addresses === true): ?>
            <?
            if ($_POST['ADDRESS_MY'] == 'new') {
                $saved_location = '';
                $saved_address = '';
            } else {
                $selected_key = $_POST['ADDRESS_MY'];
                $saved_location = $arResult['ADDRESSES'][$selected_key]['LOCATION_ID'];
                $saved_address = $arResult['ADDRESSES'][$selected_key]['ADDRESS'];
            }
            if (
                $_SERVER['REQUEST_METHOD'] === 'POST'
                && in_array($_REQUEST['DELIVERY_ID'], array(SDEK_PROFILE_PICKUP_ONLINE, SDEK_PROFILE_PICKUP, 50))
            ) {
                $saved_address = $_REQUEST['PICKUP_ADDRESS'];
            }
            ?>
            <div class="form__item">
                <div class="form__label">Ваш город</div>

                <select name="ADDRESS_MY">
                    <? foreach ($arResult['ADDRESSES'] as $key => $arAddress): ?>
                        <? $isSelected = false;
                        if ($_POST['ADDRESS_MY'] == $key || ($_POST['ADDRESS_MY'] == '' && $arAddress['SELECTED'] == 1)) {
                            $isSelected = true;
                        }
                        ?>
                        <option value="<?= $key ?>" <?= $isSelected === true ? 'selected' : '' ?>><?= $arAddress['LOCATION_NAME'] ?>
                            , <?= $arAddress['ADDRESS'] ?></option>
                    <? endforeach ?>
                    <option value="new" <?= $_POST['ADDRESS_MY'] == 'new' ? 'selected' : '' ?>>Другой адрес</option>
                </select>

                <? foreach ($arResult['ADDRESSES'] as $key => $arAddress): ?>
                    <div id="address_<?= $key ?>" class="hidden"
                         data-location-id="<?= $arAddress['LOCATION_ID'] ?>"><?= $arAddress['ADDRESS'] ?></div><? endforeach ?>
            </div>
        <? endif ?>

        <? // Отдельно выводим местоположение и адрес?>
        <div class="my-addresses <? if ($has_addresses === true && $_POST['ADDRESS_MY'] != 'new'): ?>hidden<? endif; ?>"
             style="margin: 20px 0 0 0;">
            <? $geo_location = \Citfact\Paolo::GetBitrixLocation($_SESSION['CITY_ID']);
            if ($_POST['DELIVERY_ID'] == '' && $saved_location == '' && $geo_location != '' && $geo_location != 0) {
                $saved_location = $geo_location;
            }

            foreach ($arResult['DELIVERY'] as $delivery) {
                if ($delivery['CHECKED'] == 'Y') {
                    $checkedDelivery = $delivery['ID'];
                }
            }

            foreach ($arResult['PAY_SYSTEM'] as $paySystem) {
                if ($paySystem['CHECKED'] == 'Y') {
                    $checkedPaySystem = $paySystem['ID'];
                }
            }
            ?>

            <? PrintLocationAndAddress($arResult["ORDER_PROP"]["USER_PROPS_Y"], $arParams["TEMPLATE_LOCATION"], $saved_location, $saved_address, $arResult['ERROR_KEY']); ?>
            <? PrintLocationAndAddress($arResult["ORDER_PROP"]["RELATED"], $arParams["TEMPLATE_LOCATION"], $saved_location, $saved_address, $arResult['ERROR_KEY'], $checkedDelivery, $checkedPaySystem); ?>
        </div>
    </div>


    <script type="text/javascript">
      function fGetBuyerProps(el) {
        var show = '<?=GetMessageJS('SOA_TEMPL_BUYER_SHOW')?>';
        var hide = '<?=GetMessageJS('SOA_TEMPL_BUYER_HIDE')?>';
        var status = BX('sale_order_props').style.display;
        var startVal = 0;
        var startHeight = 0;
        var endVal = 0;
        var endHeight = 0;
        var pFormCont = BX('sale_order_props');
        pFormCont.style.display = "block";
        pFormCont.style.overflow = "hidden";
        pFormCont.style.height = 0;
        var display = "";

        if (status == 'none') {
          el.text = '<?=GetMessageJS('SOA_TEMPL_BUYER_HIDE');?>';

          startVal = 0;
          startHeight = 0;
          endVal = 100;
          endHeight = pFormCont.scrollHeight;
          display = 'block';
          BX('showProps').value = "Y";
          el.innerHTML = hide;
        }
        else {
          el.text = '<?=GetMessageJS('SOA_TEMPL_BUYER_SHOW');?>';

          startVal = 100;
          startHeight = pFormCont.scrollHeight;
          endVal = 0;
          endHeight = 0;
          display = 'none';
          BX('showProps').value = "N";
          pFormCont.style.height = startHeight + 'px';
          el.innerHTML = show;
        }

        (new BX.easing({
          duration: 700,
          start: {opacity: startVal, height: startHeight},
          finish: {opacity: endVal, height: endHeight},
          transition: BX.easing.makeEaseOut(BX.easing.transitions.quart),
          step: function (state) {
            pFormCont.style.height = state.height + "px";
            pFormCont.style.opacity = state.opacity / 100;
          },
          complete: function () {
            BX('sale_order_props').style.display = display;
            BX('sale_order_props').style.height = '';

            pFormCont.style.overflow = "visible";
          }
        })).animate();
      }
    </script>
<? if (isset($arResult['ERROR_KEY']['PICKUP'])) { ?>
    <div class="order-errors-cont form__item form__item--error" id="order_errors_cont_address">
        <? echo ShowError($arResult["ERROR"][$arResult['ERROR_KEY']['PICKUP']]); ?>
    </div>
<? } ?>
<? if (!CSaleLocation::isLocationProEnabled()): ?>
    <div style="display:none;">

        <? $APPLICATION->IncludeComponent(
            "bitrix:sale.ajax.locations",
            $arParams["TEMPLATE_LOCATION"],
            array(
                "AJAX_CALL" => "N",
                "COUNTRY_INPUT_NAME" => "COUNTRY_tmp",
                "REGION_INPUT_NAME" => "REGION_tmp",
                "CITY_INPUT_NAME" => "tmp",
                "CITY_OUT_LOCATION" => "Y",
                "LOCATION_VALUE" => "",
                "ONCITYCHANGE" => "submitForm()",
            ),
            null,
            array('HIDE_ICONS' => 'Y')
        ); ?>

    </div>
<? endif ?>