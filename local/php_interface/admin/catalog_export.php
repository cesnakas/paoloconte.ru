<?php

use Bitrix\Main\Config\Option;
use Citfact\Core;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$APPLICATION->SetTitle("Настройки экспорта каталога");

if (isset($_POST['save'])) {
    if (isset($_POST['EXPORT_MIN_COUNT_OFFER_FOR_SEND'])) {
        $value = (int)$_POST['EXPORT_MIN_COUNT_OFFER_FOR_SEND'];
        if ($value < 0) {
            $value = 0;
        }
        Core::getInstance()->setExportMinCountOfferForSend($value);
    }
}

$export_min_count_offer_for_send = Core::getInstance()->getExportMinCountOfferForSend();
?>

    <form method="POST" action="<?= $APPLICATION->GetCurPage() ?>" name="settings">
        <table class="adm-detail-content-table edit-table" id="edit1_edit_table">
            <tbody>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="EXPORT_MIN_COUNT_OFFER_FOR_SEND">Минимальное кол-во ТП для экспорта: </label>
                    <input type="number" name="EXPORT_MIN_COUNT_OFFER_FOR_SEND"
                           value="<?= $export_min_count_offer_for_send ?>">
                </td>
            </tr>
            </tbody>
        </table>

        <br/>
        <input type="submit" value="Сохранить" name="save"/>
    </form>

<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_before.php");
