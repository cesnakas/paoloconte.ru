<?php

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('BX_CAT_CRON', true);
define('NO_AGENT_CHECK', true);

$_SERVER["DOCUMENT_ROOT"] = str_replace('/local/migrations', '', __DIR__);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Application;


$blockId = 10;

$list = ElementTable::getList(['filter' => ['IBLOCK_ID' => $blockId], 'select' => ['ID']]);
$items =  $list->fetchAll();

$propertyValues = ['HIT' => false];

$connection = Application::getConnection();

try {
    $connection->startTransaction();

    foreach ($items as $item) {
        $r = CIBlockElement::SetPropertyValuesEx($item['ID'], $blockId, $propertyValues);
    }

    $connection->commitTransaction();

    echo 'Success';
} catch (\Exception $e) {
    $connection->rollbackTransaction();
    echo 'Failure: ' . $e->getMessage();
}


