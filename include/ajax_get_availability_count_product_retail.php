<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;

Loader::includeModule('citfact.tools');

if (CModule::IncludeModule("sale")) {
    $result = \Citfact\Tools::getAvailabilityCountByProductRetail($_REQUEST['idShop'], $_REQUEST['products']);
    echo json_encode($result);
    die();
}
