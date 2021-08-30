<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if (strlen($arResult["ERROR_MESSAGE"])): ?>
    <?= ShowError($arResult["ERROR_MESSAGE"]); ?>
<? else: ?>
    <? if ($arResult['STATUS_ID'] == 'S') { ?>
        <? foreach ($arResult['PAYMENT'] as $payment) {
            if ($payment["CAN_REPAY"] == "Y" && $payment["PAY_SYSTEM"]["PSA_NEW_WINDOW"] != "Y"):?>
                <?
                $ORDER_ID = $ID;

                try {
                    include($payment["PAY_SYSTEM"]["PSA_ACTION_FILE"]);
                } catch (\Bitrix\Main\SystemException $e) {
                    if ($e->getCode() == CSalePaySystemAction::GET_PARAM_VALUE)
                        $message = GetMessage("SOA_TEMPL_ORDER_PS_ERROR");
                    else
                        $message = $e->getMessage();

                    ShowError($message);
                }

                ?>
            <? elseif ($payment["CAN_REPAY"] == "Y" && $payment["PAY_SYSTEM"]["PSA_NEW_WINDOW"] == "Y"):?>
                <a href="<?= $payment["PAY_SYSTEM"]["PSA_ACTION_FILE"] ?>" target="_blank" class="btn btn-green">Оплатить</a>
            <? endif; ?>
        <? } ?>
    <? } ?>
<? endif; ?>