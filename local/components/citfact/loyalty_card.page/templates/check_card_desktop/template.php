<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$exist_card = false;
if (!empty($arResult["BARCODE"]) && empty($arResult["ERRORS"]))
    $exist_card = true;
?>

<div class="loyalty_card_wrapper">
    <div class="loyalty_card_img_wrapper">
        <img src="/images/loyalty_card.png">
    </div>
    <div class="loyalty_card_form_wrapper">
        <form id="loyalty_card_form" class="loyalty_card_form form">
            <? if (!$exist_card) { ?>
                <div class="card_info_text">
                    <?=GetMessage("CARD_INFO_TEXT")?>
                    <div id="back_card">
                        <img src="/images/loyalty_card_back.jpg">
                    </div>
                </div>
            <? } ?>
            <div>
                <?=GetMessage("CARD_NUMBER")?>:
                <? if ($exist_card) { ?>
                    <span><?=$arResult["BARCODE"]?></span>
                    <input type="text" name="barcode" placeholder="<?=GetMessage("BARCODE_INPUT_PLACEHOLDER")?>" value="<?=$arResult["BARCODE"]?>" style="display:none">
                <? } else { ?>
                    <div id="loyalty_card_barcode_input_wrapper" class="loyalty_card_barcode_input_wrapper">
                        <input type="text" name="barcode" placeholder="<?=GetMessage("BARCODE_INPUT_PLACEHOLDER")?>" value="<?=$arResult["BARCODE"]?>">
                        <div id="loyalty_card_error" class="loyalty_card_error">
                            <?
                            if (!empty($arResult["ERRORS"]) && $arResult["ERRORS"]["TYPE"] != "INCORRECT_CAPTCHA") {
                                echo GetMessage($arResult["ERRORS"]["TYPE"]);
                            }
                            ?>
                        </div>
                    </div>
                <? } ?>
                <input class="btn btn--black" type="submit" value="<?=GetMessage("SUBMIT_TEXT")?>">
            </div>
            <? if ($exist_card) { ?>
                <div class="loyalty_card_discount">
                    <?=GetMessage("DISCOUNT")?>: <?=$arResult["DISCOUNT"]?>%
                </div>
            <? } ?>
            <div class="captcha_wrapper">
                <input name="captcha_code" value="<?=$arResult["CAPTCHA_CODE_CRYPT"];?>" type="hidden">
                <div class="captcha_input_wrapper" id="captcha_input_wrapper">
                    <input id="captcha_word" name="captcha_word" type="text" placeholder="<?=GetMessage("CAPTCHA_PLACEHOLDER")?>">
                    <div id="captcha_error" class="captcha_error">
                        <?
                        if (!empty($arResult["ERRORS"]) && $arResult["ERRORS"]["TYPE"] == "INCORRECT_CAPTCHA") {
                            echo GetMessage($arResult["ERRORS"]["TYPE"]);
                        }
                        ?>
                    </div>
                </div>
                <img src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE_CRYPT"];?>">
            </div>
        </form>
        <div class="dotted_line"></div>
        <a target="_blank" href="/about/programma-loyalnosti/">
            <?=GetMessage("INFO_TEXT")?>
        </a>
    </div>
</div>

<script>
    var incorrect_barcode_text = '<?=GetMessage("INCORRECT_BARCODE")?>';
</script>