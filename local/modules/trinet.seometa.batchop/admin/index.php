<?php
namespace Trinet\Seometa\Batchop;

use Bitrix\Main\Loader;
use RuntimeException;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

/* @var $APPLICATION \CMain  */
$APPLICATION->SetTitle("Импорт из xlsx");
// не забудем разделить подготовку данных и вывод
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if (!Loader::includeModule( basename( dirname(__DIR__) ) ) ) {
    return false;
}

$sheetNumber =1;

$options = Options::getInstance();
try {
    if ( !$options->isValid() )
    {
        throw new RuntimeException("Модуль не настроен!");
    }

    if ( $_POST['run'] ) {
        // сначала разберемся с настройками

        set_time_limit(0);
        $importer = new Importer();
        try {

            $sheetNumber = isset($_POST['sheetNumber']) ? intval($_POST['sheetNumber']) : 1;
            if ( $sheetNumber < 1 ) {
                throw new \Exception("Неверно задан номер листа в Excel");
            }

            /*
             * сразу парсим эксельку, чтоб проверить на ошибки
             */
            $importer->loadXlsx( $_FILES['xlsx']['tmp_name'], $_POST['skipRows'], $sheetNumber );
            unlink($_FILES['xlsx']['tmp_name']);

            $startTime = microtime(true);
            $rows = $importer->processXlsx();
            $importer->destroyXlsx();
            $report = $importer->getReport();
            ?><p>Импорт закончен.</p>
            <p>Прочитано строк из файла: <?=$rows?></p>
            <?
            if ( $_POST['extendedMode'] )
            {
                $cnt = count($report->skipped);
                if ( $cnt) {
                    ?><p>Пропущено строк: <?=$cnt?>, номера:<br><?=implode(', ', $report->skipped)?></p><?php
                }
                $cnt = count($report->updated);
                if ( $cnt) {
                    ?><p>Обновлено записей: <?=$cnt?>, номера строк:<br><?=implode(', ', $report->updated)?></p><?php
                }
                $cnt = count($report->added);
                if ( $cnt) {
                    ?><p>Добавлено записей: <?=$cnt?>, номера строк:<br><?=implode(', ', $report->added)?></p><?php
                }
            } else {
                ?>
                <p>Из них пропущено строк: <?=count($report->skipped)?></p>
                <p>Обновлено записей: <?=count($report->updated)?></p>
                <p>Добавлено записей: <?=count($report->added)?></p>
                <?php
            }
            ?>
            <p>Затрачено времени: <?=sprintf('%.3f секунд', microtime(true)-$startTime)?></p>
            <?
        } catch ( \Exception $e ) {
            ?><p class="error">Ошибка: <?=$e->getMessage()?></p><?
        }
        $report = $importer->getReport();
        if ( !empty($report->errors) ) {
            ?><p>Ошибки:</p><?
            $message = new \CAdminMessage( implode('<br>', $report->errors) );
            echo $message->Show();
        }

    }
} catch ( \Exception $e ) {
    $message = new \CAdminMessage("Ошибка! ".$e->getMessage());
    echo $message->Show();
}
$tmp = [];
foreach ($options->propertyMap as $name => $id) {
    $tmp[] = htmlspecialchars($name).": {$id}";
}
$propMap = implode(', ', $tmp);
?>
<p>Текущие настройки:</p>
<ul>
    <li>ID инфоблока: <?=$options->iblockId?></li>
    <li>№ столбца с Title: <?=$options->colTitle?></li>
    <li>№ столбца с H1: <?=$options->colHeader?></li>
    <li>№ столбца с Description: <?=$options->colDescription?></li>
    <li>№ столбца с хлебной крошкой: <?=$options->colCrumb?></li>
    <li>№ столбца с ЧПУ url: <?=$options->colChpuUrl?></li>
    <li>№ столбца с url фильтра (старый url): <?=$options->colRealUrl?></li>
    <li>№ столбца с url раздела: <?=$options->colSectionUrl?></li>
    <li>№ столбца с названием свойства: <?=$options->colPropertyName?></li>
    <li>№ столбца со значением свойства: <?=$options->colPropertyValue?></li>
    <li>Добавлять ЧПУ? <? echo $options->addChpu ? "Да" : "Нет"; ?></li>
    <li>Карта свойств: <?=$propMap?></li>
</ul>
<form method="post"  action="<?=$APPLICATION->GetCurPage()?>" style="margin-top: 2em"  name="post_form" enctype="multipart/form-data">
    <?=bitrix_sessid_post();?>
    <input type="hidden" name="lang" value="<?=LANG?>" />
    <table class="adm-detail-content-table edit-table">
        <tbody>
        <tr>
            <td class="adm-detail-content-cell-l"><label for="trinet_form_xlsx">Excel-файл:</label></td>
            <td class="adm-detail-content-cell-r"><input type="file" name="xlsx" id="trinet_form_xlsx"></td>
        </tr>
        <tr>
            <td class="adm-detail-content-cell-l"><label for="trinet_form_skipRows">пропустить строк с начала листа:</label></td>
            <td class="adm-detail-content-cell-r"><input style="width: 2.5em;" type="number" name="skipRows" id="trinet_form_skipRows" value="1" min="0" step="1"></td>
        </tr>
        <tr>
            <?php
            ?>
            <td class="adm-detail-content-cell-l"><label for="trinet_form_sheetNumber">Номер листа Excel:</label></td>
            <td class="adm-detail-content-cell-r"><input style="width: 2.5em;" type="number" name="sheetNumber" id="trinet_form_sheetNumber" value="<?=$sheetNumber?>" min="1" step="1"></td>
        </tr>
        <?php
        if ( $_POST['extendedMode'] ) {
            $checked = 'checked="checked"';
        } else {
            $checked = '';
        }
        ?>
        <tr>
            <td class="adm-detail-content-cell-l"><label for="trinet_form_extendedMode">Расширенный отчет?</label></td>
            <td class="adm-detail-content-cell-r"><input type="checkbox" name="extendedMode" id="trinet_form_extendedMode" <?=$checked?>></td>
        </tr>

        </tbody>
        <tfoot>
        <tr><td colspan="2" style="text-align:center; padding-top: 2em"><input name="run" value="Запустить импорт" type="submit" class="adm-btn-save"></td></tr>
        </tfoot>
    </table>
</form>
