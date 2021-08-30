<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$exist_card = false;
if (!empty($arResult["BARCODE"]) && empty($arResult["ERRORS"]))
    $exist_card = true;
?>

<div class="loyalty_card_wrapper">
    <? if (!$exist_card) { ?>
        <div class="card_info_text">
            Здесь вы можете привязать <a target="_blank" href="/about/programma-loyalnosti/">Карту Постоянного Покупателя</a>, чтобы:
            <ul>
                <li>узнать процент скидки по ней и сумму покупок, которая позволит его увеличить;</li>
                <li>использовать карту в качестве промо-кода при оформлении заказа.</li>
            </ul>
            После ввода номера карты наш консультант проверит и активирует ее.
            <br><br>
            Введите 13 цифр с оборотной стороны карты:
        </div>
    <? } elseif (!$arResult["ACTIVE"]) { ?>
        <div class="important-text">
            Поздравляем, Вы стали участником клуба Постоянных покупателей Paolo Conte!<br>
            Сейчас Вашей дисконтной карте нужна активация.<br>
            Пожалуйста, дождитесь когда наш менеджер сделает это или позвоните по телефону
            <?$APPLICATION->IncludeComponent("articul.geolocation.city_current", "phone", array(), false);?>.
        </div>
    <? } ?>
    <div class="loyalty_card_img_wrapper">
        <img
            <? if (!$exist_card) { ?>
                src="/images/loyalty_card_back.png"
            <? } else { ?>
                src="/images/loyalty_card.png"
            <? } ?>
        >
    </div>
    <div class="loyalty_card_form_wrapper">
        <form id="loyalty_card_form" class="loyalty_card_form">
            <div>
                <? if ($exist_card) { ?>
                    <div class="card_number">
                        <?=GetMessage("CARD_NUMBER")?>:
                        <span><?=$arResult["BARCODE"]?></span>
                        <br>
                        <?=GetMessage("DISCOUNT")?> <span><?=$arResult["DISCOUNT"]?>%</span>, <?=$arResult["INFO_TEXT"]?>
                        <? if (!empty($arResult["LAST_UPDATE"])) { ?>
                            <br>
                            (по состоянию на <?=$arResult["LAST_UPDATE"]?>)
                        <? } ?>
                        <div class="loyalty_card_submit_button_wrapper">
                            <i class="fa fa-repeat"></i>
                            <?=GetMessage("SUBMIT_TEXT_CARD_EXISTS")?>
                        </div>
                    </div>
                    <? if ($arResult["ACTIVE"]) { ?>
                        <br>
                        Чтобы получить скидку по карте, введите ее номер при оформлении заказа или сообщите его консультанту магазина.
                        <br><br>
                        При действующей акции размер скидки по дисконтной карте устанавливают правила акции.
                        <br>
                    <? } ?>

                    <div class="about_link">
                        <a target="_blank" href="/about/programma-loyalnosti/"><?=GetMessage("INFO_TEXT")?></a>
                    </div>
                    <input type="text" name="barcode" value="<?=$arResult["BARCODE"]?>" style="display:none">
                <? } else { ?>
                    <div id="loyalty_card_barcode_input_wrapper" class="loyalty_card_barcode_input_wrapper">
                        <input type="text" name="barcode" placeholder="<?=GetMessage("BARCODE_INPUT_PLACEHOLDER")?>" value="<?=$arResult["BARCODE"]?>">
                        <div id="loyalty_card_error" class="loyalty_card_error">
                            <?
                            if (!empty($arResult["ERRORS"])) {
                                echo GetMessage($arResult["ERRORS"]["TYPE"]);
                            }
                            ?>
                        </div>
                    </div>
                    <input class="loyalty_card_submit_button" type="submit" value="<?=GetMessage("SUBMIT_TEXT")?>">
                <? } ?>
            </div>
        </form>
    </div>
    <? if (!$exist_card) { ?>
        <div class="about_link">
            <a target="_blank" href="/about/programma-loyalnosti/"><?=GetMessage("INFO_TEXT")?></a>
        </div>
    <? } ?>
</div>

<script>
    var incorrect_barcode_text = '<?=GetMessage("INCORRECT_BARCODE")?>';
</script>