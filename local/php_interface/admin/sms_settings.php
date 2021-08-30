<?php

use Bitrix\Main\Config\Option;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


$APPLICATION->SetTitle("Настройка отправки SMS покупателю при оформлении заказа");


if ($_POST['save']) {
    if ($_POST['enable-sms-activity']) {
        Option::set('sale', 'ORDER_ON_SMS_ACTIVITY', 'Y');
    } else {
        Option::set('sale', 'ORDER_ON_SMS_ACTIVITY', 'N');
    }

    if ($_POST['enable-sms-activity-auto']) {
        Option::set('sale', 'ORDER_ON_SMS_ACTIVITY_AUTO', 'Y');
    } else {
        Option::set('sale', 'ORDER_ON_SMS_ACTIVITY_AUTO', 'N');
    }

    if ($_POST['SMS_MESSAGE_1']) { Option::set('sale', 'ORDER_SMS_MESSAGE_1',  htmlspecialchars($_POST['SMS_MESSAGE_1'])); }
    if ($_POST['SMS_MESSAGE_2']) { Option::set('sale', 'ORDER_SMS_MESSAGE_2',  htmlspecialchars($_POST['SMS_MESSAGE_2'])); }
    if ($_POST['SMS_MESSAGE_3']) { Option::set('sale', 'ORDER_SMS_MESSAGE_3',  htmlspecialchars($_POST['SMS_MESSAGE_3'])); }
    if ($_POST['SMS_MESSAGE_4']) { Option::set('sale', 'ORDER_SMS_MESSAGE_4',  htmlspecialchars($_POST['SMS_MESSAGE_4'])); }
    if ($_POST['SMS_MESSAGE_5']) { Option::set('sale', 'ORDER_SMS_MESSAGE_5',  htmlspecialchars($_POST['SMS_MESSAGE_5'])); }
    if ($_POST['SMS_TIMEOUT']) { Option::set('sale', 'TIMEOUT_SMS',  htmlspecialchars($_POST['SMS_TIMEOUT'])); }
}

$enableSmsActivity = Option::get('sale', 'ORDER_ON_SMS_ACTIVITY');
$enableSmsActivityAuto = Option::get('sale', 'ORDER_ON_SMS_ACTIVITY_AUTO');

$strSmsMessage1 = Option::get('sale', 'ORDER_SMS_MESSAGE_1');
$strSmsMessage2 = Option::get('sale', 'ORDER_SMS_MESSAGE_2');
$strSmsMessage3 = Option::get('sale', 'ORDER_SMS_MESSAGE_3');
$strSmsMessage4 = Option::get('sale', 'ORDER_SMS_MESSAGE_4');
$strSmsMessage5 = Option::get('sale', 'ORDER_SMS_MESSAGE_5');
$timeout = Option::get('sale', 'TIMEOUT_SMS');
?>

    <form method="POST" action="<?= $APPLICATION->GetCurPage() ?>" name="settings">
        <table class="adm-detail-content-table edit-table" id="edit1_edit_table">
            <tbody>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="enable-sms-activity">Отправлять SMS сообщения автоматически: </label>
                    <input <?= $enableSmsActivityAuto == 'Y' ? 'checked' : '' ?> type="checkbox" id="enable-sms-activity-auto" name="enable-sms-activity-auto"
                                                                                value="<?= $enableSmsActivity ?>" class="adm-designed-checkbox">
                    <label class="adm-designed-checkbox-label" for="enable-sms-activity-auto" title=""></label>
                </td>
            </tr>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="enable-sms-activity">Отправлять SMS сообщения при оформлении заказа: </label>
                    <input <?= $enableSmsActivity == 'Y' ? 'checked' : '' ?> type="checkbox" id="enable-sms-activity" name="enable-sms-activity"
                                                                             value="<?= $enableSmsActivity ?>" class="adm-designed-checkbox">
                    <label class="adm-designed-checkbox-label" for="enable-sms-activity" title=""></label>
                </td>
            </tr>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="SMS_MESSAGE_1">Текст отправляемого SMS сообщения: </label>
                    <input type="text" name="SMS_MESSAGE_1" value="<?= $strSmsMessage1 ?>" size="100" maxlength="85">
                </td>
            </tr>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="SMS_MESSAGE_2">Текст отправляемого SMS сообщения: </label>
                    <input type="text" name="SMS_MESSAGE_2" value="<?= $strSmsMessage2 ?>" size="100" maxlength="85">
                </td>
            </tr>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="SMS_MESSAGE_3">Текст отправляемого SMS сообщения: </label>
                    <input type="text" name="SMS_MESSAGE_3" value="<?= $strSmsMessage3 ?>" size="100" maxlength="85">
                </td>
            </tr>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="SMS_MESSAGE_4">Текст отправляемого SMS сообщения: </label>
                    <input type="text" name="SMS_MESSAGE_4" value="<?= $strSmsMessage4 ?>" size="100" maxlength="85">
                </td>
            </tr>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="SMS_MESSAGE_5">Текст отправляемого SMS сообщения: </label>
                    <input type="text" name="SMS_MESSAGE_5" value="<?= $strSmsMessage5 ?>" size="100" maxlength="85">
                </td>
            </tr>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="SMS_TIMEOUT">Перерыв между сообщениями для одного заказа (в секундах)</label>
                    <input type="text" name="SMS_TIMEOUT" value="<?= $timeout ?>" size="10" maxlength="6">
                </td>
            </tr>
            </tbody>
        </table>

        <br/>
        <input type="submit" value="Сохранить" name="save"/>
    </form>

<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_before.php");
