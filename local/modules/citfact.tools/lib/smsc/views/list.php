<?php

use Bitrix\Main\Config\Option;
use Citfact\Entity\Sms\OrderSmsRepository;

$strSmsMessage1 = str_replace('#ORDER_ID#', $orderId, Option::get('sale', 'ORDER_SMS_MESSAGE_1'));
$strSmsMessage2 = str_replace('#ORDER_ID#', $orderId, Option::get('sale', 'ORDER_SMS_MESSAGE_2'));
$strSmsMessage3 = str_replace('#ORDER_ID#', $orderId, Option::get('sale', 'ORDER_SMS_MESSAGE_3'));
$strSmsMessage4 = str_replace('#ORDER_ID#', $orderId, Option::get('sale', 'ORDER_SMS_MESSAGE_4'));
$strSmsMessage5 = str_replace('#ORDER_ID#', $orderId, Option::get('sale', 'ORDER_SMS_MESSAGE_5'));
if (!empty ($arResult)) {
    $usersId = array_values(array_unique(array_column($arResult, 'USER_ID')));
    $users = [];
    $filter = Array("ID" => implode('|', $usersId));
    $rsUsers = \CUser::GetList(($by = "ID"), ($order = "desc"), $filter);

    while($user = $rsUsers->Fetch()) {
        $users[$user['ID']] = $user['NAME'] . ' ' . $user['LAST_NAME'] . ' (' . $user['ID'] . ')';
    }
}

if (isset($resultSending['error'])) {
    ShowError($resultSending['error']);
}
?>

<form method="post" action="#">
    <b>Шаблоны</b>
    <?php if (!empty($strSmsMessage1)) :?>
        <p><?=$strSmsMessage1?></p>
    <?php endif;?>
    <?php if (!empty($strSmsMessage2)) :?>
        <p><?=$strSmsMessage2?></p>
    <?php endif;?>
    <?php if (!empty($strSmsMessage3)) :?>
        <p><?=$strSmsMessage3?></p>
    <?php endif;?>
    <?php if (!empty($strSmsMessage4)) :?>
        <p><?=$strSmsMessage4?></p>
    <?php endif;?>
    <?php if (!empty($strSmsMessage5)) :?>
        <p><?=$strSmsMessage5?></p>
    <?php endif;?>
    <br>
    <label for="message">Введите текст СМС сообщения на номер <b><?=$userPhone?></b>:</label>
    <br><br>
    <input type="text" size="50" maxlength="255" id="message" name="smsMessage">
    <br><br>
    <input type="submit" value="Отправить">
</form>
<br><br>
<form method="post" action="#">
    <input type="hidden" name="updateSms" value="Y">
    <input type="submit" value="Обновить статусы">
</form>
<br>
<br>
<div class="adm-list-table-layout">
    <div class="adm-list-table-wrap adm-list-table-without-header adm-list-table-without-footer">
        <table class="adm-list-table">
            <thead>
                <tr class="adm-list-table-header">
                    <td class="adm-list-table-cell">ID</td>
                    <td class="adm-list-table-cell">Текст</td>
                    <td class="adm-list-table-cell">Статус</td>
                    <td class="adm-list-table-cell">Автор</td>
                    <td class="adm-list-table-cell">Дата</td>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($arResult)) :?>
                <?php foreach ($arResult as $sms) :?>
                    <tr class="adm-list-table-row">
                        <td class="adm-list-table-cell"><?=$sms['MESSAGE_ID']?></td>
                        <td class="adm-list-table-cell"><?=$sms['MESSAGE']?></td>
                        <td class="adm-list-table-cell"><?=OrderSmsRepository::STATUSES[$sms['STATUS']]?></td>
                        <td class="adm-list-table-cell"><?=$users[$sms['USER_ID']]?></td>
                        <td class="adm-list-table-cell adm-list-table-cell-last"><?=$sms['DATE_UPDATE']?></td>
                    </tr>
                <?php endforeach;?>
            <?php else:?>
            <tr>
                <td class="adm-list-table-cell adm-list-table-empty" colspan="5"></td>
            </tr>
            <?php endif;?>
            </tbody>
        </table>
    </div>
</div>
<?php ?>
<?php if (isset($backToSMSTab) && $backToSMSTab) :?>
<script>
    document.addEventListener('DOMContentLoaded', function (e) {
        sale_order_view.SelectTab('tab_sms');
    });</script>
<?php endif;?>
