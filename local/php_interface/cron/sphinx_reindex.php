<?php
if(!is_file('/home/sphinxcontrol/sphinx/data/userdefined/sphinx_need_reindex')){
    die;
}

define("NO_KEEP_STATISTIC", true);
define('CHK_EVENT', true);

$_SERVER["DOCUMENT_ROOT"] = str_replace('/local/php_interface/cron', '', __DIR__);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . '/local/php_interface/include/lock.php');

if (lock('paoloconte_sphinx_reindex')) {
} else {
    die();
}

set_time_limit(0);

if(CModule::IncludeModule("search")) {

    $success_message  = '<div class="adm-info-message-wrap adm-info-message-green"><div class="adm-info-message"><div class="adm-info-message-title">Переиндексация закончена.</div>Проиндексировано документов: <b>#NUM#</b><div class="adm-info-message-icon"></div></div></div>';
    $proccess_message = '<div class="adm-info-message-wrap adm-info-message-gray"><div class="adm-info-message"><div class="adm-info-message-title">Переиндексация...</div>Проиндексировано документов: <b>#NUM#</b><br></div></div>';

    $reindex_log_file = __DIR__ . "/sphinx_reindex_status.log";

    $result = CSearch::ReIndexAll(true, 60);
    while(is_array($result)) {
        $result = CSearch::ReIndexAll(true, 60, $result);
        file_put_contents($reindex_log_file, print_r(str_replace("#NUM#", $result["CNT"], $proccess_message), true) . "\n");
    }
    file_put_contents($reindex_log_file, print_r(str_replace("#NUM#", $result, $success_message), true) . "\n");
    unlink("/home/sphinxcontrol/sphinx/data/userdefined/sphinx_need_reindex");
}