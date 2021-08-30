<?php

use Bitrix\Main\Config\Option;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


$APPLICATION->SetTitle("Настройки обмена с 1с");

if ($_POST['save']) {
    if ($_POST['lock-sections-activity']) {
        Option::set('sale', '1C_LOCK_SECTIONS_ACTIVITY', 'Y');
    } else {
        Option::set('sale', '1C_LOCK_SECTIONS_ACTIVITY', 'N');
    }

    if ($_POST['lock-sections-sections']) {
        Option::set('sale', '1C_LOCK_SECTIONS_SECTIONS', 'Y');
    } else {
        Option::set('sale', '1C_LOCK_SECTIONS_SECTIONS', 'N');
    }

    if ($_POST['lock-element-activity']) {
        Option::set('sale', '1C_LOCK_ELEMENT_ACTIVITY', 'Y');
    } else {
        Option::set('sale', '1C_LOCK_ELEMENT_ACTIVITY', 'N');
    }

    if ($_POST['lock-element-sections']) {
        Option::set('sale', '1C_LOCK_ELEMENT_SECTIONS', 'Y');
    } else {
        Option::set('sale', '1C_LOCK_ELEMENT_SECTIONS', 'N');
    }
}

$lockSectionsActivity = Option::get('sale', '1C_LOCK_SECTIONS_ACTIVITY');
$lockSectionsSections = Option::get('sale', '1C_LOCK_SECTIONS_SECTIONS');
$lockElementActivity = Option::get('sale', '1C_LOCK_ELEMENT_ACTIVITY');
$lockElementSections = Option::get('sale', '1C_LOCK_ELEMENT_SECTIONS');

?>

    <form method="POST" action="<?= $APPLICATION->GetCurPage() ?>" name="settings">
        <table class="adm-detail-content-table edit-table" id="edit1_edit_table">
            <tbody>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="lock-sections-activity">Отключить изменение активности разделов во время обмена: </label>
                    <input <?= $lockSectionsActivity == 'Y' ? 'checked' : '' ?> disabled 
                            type="checkbox" id="lock-sections-activity" name="lock-sections-activity"
                            value="<?= $lockSectionsActivity ?>" class="adm-designed-checkbox">
                    <label class="adm-designed-checkbox-label" for="lock-sections-activity" title=""></label>
                </td>
            </tr>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="lock-sections-sections">Отключить изменение привязок разделов к разделам во время обмена: </label>
                    <input <?= $lockSectionsSections == 'Y' ? 'checked' : '' ?> disabled
                            type="checkbox" id="lock-sections-sections" name="lock-sections-sections"
                            value="<?= $lockSectionsSections ?>" class="adm-designed-checkbox">
                    <label class="adm-designed-checkbox-label" for="lock-sections-sections" title=""></label>
                </td>
            </tr>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="lock-element-activity">Отключить изменение активности элементов во время обмена: </label>
                    <input <?= $lockElementActivity == 'Y' ? 'checked' : '' ?> disabled
                            type="checkbox" id="lock-element-activity" name="lock-element-activity"
                            value="<?= $lockElementActivity ?>" class="adm-designed-checkbox">
                    <label class="adm-designed-checkbox-label" for="lock-element-activity" title=""></label>
                </td>
            </tr>
            <tr>
                <td class="adm-detail-content-cell-r">
                    <label for="lock-element-sections">Отключить изменение привязок элементов к разделам во время обмена: </label>
                    <input <?= $lockElementSections == 'Y' ? 'checked' : '' ?> disabled
                            type="checkbox" id="lock-element-sections" name="lock-element-sections"
                            value="<?= $lockElementSections ?>" class="adm-designed-checkbox">
                    <label class="adm-designed-checkbox-label" for="lock-element-sections" title=""></label>
                </td>
            </tr>
            </tbody>
        </table>

        <br/>
        <?/*<input type="submit" value="Сохранить" name="save"/>*/?>
    </form>

<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin_before.php");
