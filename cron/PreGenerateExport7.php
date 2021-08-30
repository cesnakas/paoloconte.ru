<?
$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

file_put_contents($_SERVER["DOCUMENT_ROOT"]."/local/var/logs/cron/PreGenerateExport7.log", print_r('START --- -- -- - '.date('Y.m.d H:i:s').'--- -- -- - ', true));

CModule::IncludeModule("main");
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");

CCatalogExport::PreGenerateExport(7);

file_put_contents($_SERVER["DOCUMENT_ROOT"]."/local/var/logs/cron/PreGenerateExport7.log", print_r('--- -- -- - '.date('Y.m.d H:i:s').'--- -- -- - END', true), FILE_APPEND);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");