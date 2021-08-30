<?
define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);

use Bitrix\Main\Loader;


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if ($_SERVER["REQUEST_METHOD"] != "POST")
    return;

CBitrixComponent::includeComponentClass("citfact:loyalty_card.page");

$page = new CBitrixLoyaltyCardComponent();

$arResult = $page->doAll();

$APPLICATION->RestartBuffer();
header('Content-Type: application/json; charset='.LANG_CHARSET);
echo json_encode($arResult);
die();