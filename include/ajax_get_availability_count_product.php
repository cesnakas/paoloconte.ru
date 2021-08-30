<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

Loader::includeModule('citfact.tools');

if (CModule::IncludeModule("sale")) {

    $count = \Citfact\Tools::getAvailabilityCountByProduct($_REQUEST['idProduct']);
    $result = [
        'idProduct' => $_REQUEST['idProduct'],
        'count' => $count
    ];
    echo json_encode($result);
    die();
}
