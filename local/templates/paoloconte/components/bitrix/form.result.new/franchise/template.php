<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? if ($arResult["isFormErrors"] == "Y") { ?>
    <?= $arResult["FORM_ERRORS_TEXT"]; ?>
<? } ?>

<?= $arResult["FORM_NOTE"] ?>

<? if ($arResult["isFormNote"] != "Y") { ?>
    <?= $arResult["FORM_HEADER"] ?>
    <table>
        <? if ($arResult["isFormDescription"] == "Y" || $arResult["isFormTitle"] == "Y" || $arResult["isFormImage"] == "Y") { ?>
            <tr>
                <td>
                    <? if ($arResult["isFormImage"] == "Y") {
                        ?>
                        <a href="<?= $arResult["FORM_IMAGE"]["URL"] ?>" target="_blank">
                            <img src="<?= $arResult["FORM_IMAGE"]["URL"] ?>"
                                 <? if ($arResult["FORM_IMAGE"]["WIDTH"] > 300): ?>width="300"
                                 <? elseif ($arResult["FORM_IMAGE"]["HEIGHT"] > 200): ?>height="200"
                                <? else: ?><?= $arResult["FORM_IMAGE"]["ATTR"] ?><? endif; ?>
                                 hspace="3" vscape="3" border="0"/></a>
                    <? } ?>
                </td>
            </tr>
        <? } ?>
    </table>

    <table class="form-table data-table">
        <tbody>
        <? foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) { ?>
            <? if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden') {
                echo $arQuestion["HTML_CODE"];
            } else {
                if (strpos($arQuestion['CAPTION'], 'телефон') !== false) {
                    $arQuestion['HTML_CODE'] = str_replace('class="inputtext"', 'class="inputtext mask-phone"', $arQuestion['HTML_CODE']);
                } ?>
                <tr>
                    <td>
                        <? if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])) { ?>
                            <span class="error-fld" title="<?= $arResult["FORM_ERRORS"][$FIELD_SID] ?>"></span>
                        <? } ?>

                        <?= $arQuestion["CAPTION"] ?>

                        <? if ($arQuestion["REQUIRED"] == "Y") { ?>
                            <?= $arResult["REQUIRED_SIGN"]; ?>
                        <? } ?>

                        <?= $arQuestion["IS_INPUT_CAPTION_IMAGE"] == "Y" ? "<br />" . $arQuestion["IMAGE"]["HTML_CODE"] : "" ?>
                    </td>

                    <td>
                        <?= $arQuestion["HTML_CODE"] ?>
                    </td>
                </tr>
            <? } ?>
        <? } ?>

        <? if ($arResult["isUseCaptcha"] == "Y") { ?>
            <? $frame = $this->createFrame()->begin(); ?>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <input type="hidden" name="captcha_sid"
                           value="<?= htmlspecialcharsbx($arResult["CAPTCHACode"]); ?>"/><img
                            src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialcharsbx($arResult["CAPTCHACode"]); ?>"
                            width="180" height="40"/>
                </td>
            </tr>

            <tr>
                <td><?= GetMessage("FORM_CAPTCHA_FIELD_TITLE") ?><?= $arResult["REQUIRED_SIGN"]; ?></td>
                <td><input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext"/></td>
            </tr>
            <? $frame->end(); ?>
        <? } ?>
        </tbody>

        <tfoot>
        <tr>
            <th colspan="2">
                <input onclick="yaCounter209275.reachGoal('About_franchise');" <?= (intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : ""); ?>
                       type="submit" name="web_form_submit"
                       value="<?= htmlspecialcharsbx(strlen(trim($arResult["arForm"]["BUTTON"])) <= 0 ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]); ?>"
                       class="btn btn--black"/>
            </th>
        </tr>
        </tfoot>
    </table>
    <? //для задействования модуля рекапчи добавляет скрытое поле (https://marketplace.1c-bitrix.ru/solutions/b01110011.recaptcha/#tab-install-link) ?>
    <input type="hidden" name="recaptcha_token" value="">
    <p><?= $arResult["REQUIRED_SIGN"]; ?> - <?= GetMessage("FORM_REQUIRED_FIELDS") ?></p>

    <?= $arResult["FORM_FOOTER"] ?>
<? } ?>