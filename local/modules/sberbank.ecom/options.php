<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/options.php');
Loc::loadMessages(__FILE__);

if (!$USER->IsAdmin() || !Loader::includeModule('sale') || !Loader::includeModule('catalog')) {
    return;
}

require __DIR__ . '/config.php';

$arStatuses = array();
$dbStatus = CSaleStatus::GetList(Array("SORT" => "ASC"), Array("LID" => LANGUAGE_ID), false, false, Array("ID", "NAME", "SORT"));
while ($arStatus = $dbStatus->GetNext()) {
    $arStatuses[$arStatus["ID"]] = "[" . $arStatus["ID"] . "] " . $arStatus["NAME"];
}

$status = COption::GetOptionString("rbs.payment", "result_order_status", "N");
$iso = COption::GetOptionString("rbs.payment", "iso", serialize(array()));
$iso = unserialize($iso);
$FISCALIZATION = COption::GetOptionString("sberbank.ecom", "FISCALIZATION", serialize(array()));
$FISCALIZATION = unserialize($FISCALIZATION);

if ($REQUEST_METHOD == 'POST' && strlen($Update . $Apply) > 0 && check_bitrix_sessid()) {

    $status = $_POST['RESULT_ORDER_STATUS'];
    COption::SetOptionString("rbs.payment", "result_order_status", $status);

    $iso = $_POST['iso'];
    if (!is_array($iso))
        $iso = array();
    COption::SetOptionString("rbs.payment", "iso", serialize($iso));
    COption::SetOptionString("sberbank.ecom", "FISCALIZATION", serialize($_REQUEST['FISCALIZATION']));
    COption::SetOptionString("sberbank.ecom", "VAT_LIST", serialize($_REQUEST['VAT_LIST']));
    COption::SetOptionString("sberbank.ecom", "VAT_DELIVERY_LIST", serialize($_REQUEST['VAT_DELIVERY_LIST']));
}

$iso = array_filter($iso);
$arDefaultIso = unserialize(DEFAULT_ISO);
if (is_array($arDefaultIso))
    $iso = array_merge($arDefaultIso, $iso);


$VAT_LIST_SAVED = unserialize(COption::GetOptionString("sberbank.ecom", "VAT_LIST", serialize(array())));
$VAT_LIST_DELIVERY_SAVED = unserialize(COption::GetOptionString("sberbank.ecom", "VAT_DELIVERY_LIST", serialize(array())));

$arPaysystemVat = array(
    0 => Loc::getMessage('TAB1_VAT_LIST_VALUE_0'),
    1 => Loc::getMessage('TAB1_VAT_LIST_VALUE_1'),
    2 => Loc::getMessage('TAB1_VAT_LIST_VALUE_2'),
    3 => Loc::getMessage('TAB1_VAT_LIST_VALUE_3'),
);
$arVatList = array(
    0 => Loc::getMessage('TAB1_VAT_NOT_SET'),
);
$dbRes = CCatalogVat::GetList();
while ($arRes = $dbRes->Fetch()) {
    $arVatList[$arRes['ID']] = $arRes['NAME'];
}
// VIEW

$tabControl = new CAdminTabControl('tabControl', array(
    array('DIV' => 'edit1', 'TAB' => Loc::getMessage('MAIN_TAB_SET'), 'ICON' => 'ib_settings', 'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET'))
));

$tabControl->Begin();

?>
<form method="post"
      action="<?= $APPLICATION->GetCurPage() ?>?mid=<?= urlencode($mid) ?>&amp;lang=<? echo LANGUAGE_ID ?>">
    <?= bitrix_sessid_post() ?>

    <? $tabControl->BeginNextTab() ?>

    <tr>
        <td width="40%"><?= Loc::getMessage('RESULT_ORDER_STATUS'); ?>:</td>
        <td width="60%">
            <select name="RESULT_ORDER_STATUS">
                <?

                foreach ($arStatuses as $key => $name) {
                    ?>
                    <option value="<?= $key ?>"<?= $key == $status ? ' selected' : '' ?>><?= htmlspecialcharsex($name) ?></option><?
                }

                ?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="40%"></td>
        <td width="60%">
            <input type="button" id="check-https" value="<?= Loc::getMessage('CHECK_HTTPS'); ?>">
            <p id="result-check-https"></p>
        </td>
    </tr>
    <tr class="heading">
        <td colspan="2"><?= Loc::getMessage('TAB1_CURRENCY_TITLE') ?></td>
    </tr>
    <tr>
        <td width="40%"><?= Loc::getMessage('CURRENCY_CHOISE'); ?></td>
        <td width="60%">
            <table>
                <thead>
                <th><?= Loc::getMessage('CC_HEAD_CURRENCY'); ?></th>
                <th><?= Loc::getMessage('CC_HEAD_CODE'); ?></th>
                <th><?= Loc::getMessage('CC_HEAD_ISO'); ?></th>
                </thead>
                <tbody>
                <? $dbRes = CCurrency::GetList(($by = 'id'), ($order = 'asc'));
                while ($arItem = $dbRes->GetNext()):
                    ?>
                    <tr>
                        <td><?= $arItem["FULL_NAME"] ?></td>
                        <td><?= $arItem["CURRENCY"] ?></td>
                        <td><input name="iso[<?= $arItem["~CURRENCY"] ?>]" type="text"
                                   value="<? echo $iso[$arItem["~CURRENCY"]] ? $iso[$arItem["~CURRENCY"]] : $arItem["NUMCODE"] ?>">
                        </td>
                    </tr>
                <? endwhile; ?>
                </tbody>
            </table>
        </td>
    </tr>
    <tr class="heading">
        <td colspan="2"><?= Loc::getMessage('TAB1_FISCALIZATION_TITLE') ?></td>
    </tr>
    <tr>
        <td width="40%"><?= GetMessage('BANK_ISSUED_CHECK') ?></td>
        <td width="60%"><input type="checkbox" value="Y"
                               name="FISCALIZATION[ENABLE]" <? if ($FISCALIZATION['ENABLE'] == 'Y') echo 'checked="checked"' ?>>
        </td>
    </tr>
    <tr>
        <td width="40%"><?= GetMessage('TAX_SYSTEM') ?></td>
        <td width="60%">
            <select name="FISCALIZATION[VAT_DELIVERY_LIST]">
                <option value="0" <? if ($FISCALIZATION['TAX_SYSTEM'] == 0) echo 'selected' ?>><?= GetMessage('TAX_SYSTEM_GENERAL') ?></option>
                <option value="1" <? if ($FISCALIZATION['TAX_SYSTEM'] == 1) echo 'selected' ?>><?= GetMessage('TAX_SYSTEM_SIMPLIFIED_INCOME') ?></option>
                <option value="2" <? if ($FISCALIZATION['TAX_SYSTEM'] == 2) echo 'selected' ?>><?= GetMessage('TAX_SYSTEM_SIMPLIFIED_REVENUE_MINUS_CONSUMPTION') ?></option>
                <option value="3" <? if ($FISCALIZATION['TAX_SYSTEM'] == 3) echo 'selected' ?>><?= GetMessage('TAX_SYSTEM_SINGLE_TAX_ON_IMPUTED_INCOME') ?></option>
                <option value="4" <? if ($FISCALIZATION['TAX_SYSTEM'] == 4) echo 'selected' ?>><?= GetMessage('TAX_SYSTEM_UNIFIED_AGRICULTURAL_TAX') ?></option>
                <option value="5" <? if ($FISCALIZATION['TAX_SYSTEM'] == 5) echo 'selected' ?>><?= GetMessage('TAX_SYSTEM_PATENT_SYSTEM_OF_TAXATION') ?></option>
            </select>
        </td>
    </tr>
    <tr class="heading">
        <td colspan="2"><?= Loc::getMessage('TAB1_VAT_TITLE') ?></td>
    </tr>
    <? foreach ($arPaysystemVat as $vatId => $vatName): ?>
        <tr>
            <td><?= $vatName ?>:</td>
            <td>
                <?
                $selected = isset($VAT_LIST_SAVED[$vatId]) ? $VAT_LIST_SAVED[$vatId] : "";
                echo SelectBoxMFromArray(
                    "VAT_LIST[$vatId][]",
                    array('REFERENCE' => array_values($arVatList), 'REFERENCE_ID' => array_keys($arVatList)),
                    $VAT_LIST_SAVED[$vatId]
                );
                ?>
            </td>
        </tr>
    <? endforeach; ?>
    <tr class="heading">
        <td colspan="2"><?= Loc::getMessage('TAB1_VAT_DELIVERY_TITLE') ?></td>
    </tr>
    <? foreach ($arPaysystemVat as $vatId => $vatName): ?>
        <tr>
            <td><?= $vatName ?>:</td>
            <td>
                <?
                $selected = isset($VAT_LIST_DELIVERY_SAVED[$vatId]) ? $VAT_LIST_DELIVERY_SAVED[$vatId] : "";
                echo SelectBoxMFromArray(
                    "VAT_DELIVERY_LIST[$vatId][]",
                    array('REFERENCE' => array_values($arVatList), 'REFERENCE_ID' => array_keys($arVatList)),
                    $VAT_LIST_DELIVERY_SAVED[$vatId]
                );
                ?>
            </td>
        </tr>
    <? endforeach; ?>

    <script type="text/javascript">
        BX.ready(function () {
            var oButtonCheck = document.getElementById('check-https');
            if (oButtonCheck) {
                oButtonCheck.onclick = function () {
                    BX.ajax.loadJSON('/sberbank.ecom/ajax.php',
                        '<?echo CUtil::JSEscape(bitrix_sessid_get())?>&check_https=Y',
                        function (result) {
                            var oResultCH = document.getElementById('result-check-https');
                            if (oResultCH) {
                                if (result.SUCCESS === 'Y') {
                                    oResultCH.innerHTML = '<span style="color: #00f;"><?=Loc::getMessage('CHECK_HTTPS_SUCCESS');?></span>';
                                }
                                else {
                                    oResultCH.innerHTML = '<span style="color: #f00;"><?=Loc::getMessage('CHECK_HTTPS_FAIL');?></span>';
                                }
                            }

                        });
                    return false;
                }
            }
        });
    </script>
    <? $tabControl->Buttons() ?>

    <input type="submit" name="Update" value="<?= GetMessage("MAIN_SAVE") ?>"
           title="<?= GetMessage("MAIN_OPT_SAVE_TITLE") ?>" class="adm-btn-save">
    <input type="submit" name="Apply" value="<?= GetMessage("MAIN_OPT_APPLY") ?>"
           title="<?= GetMessage("MAIN_OPT_APPLY_TITLE") ?>">
    <? if (strlen($_REQUEST["back_url_settings"]) > 0): ?>
        <input type="button" name="Cancel" value="<?= GetMessage("MAIN_OPT_CANCEL") ?>"
               title="<?= GetMessage("MAIN_OPT_CANCEL_TITLE") ?>"
               onclick="window.location='<? echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"])) ?>'">
        <input type="hidden" name="back_url_settings" value="<?= htmlspecialcharsbx($_REQUEST["back_url_settings"]) ?>">
    <? endif ?>

    <? $tabControl->End() ?>
</form>
