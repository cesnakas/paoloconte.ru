<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;
global $APPLICATION;
$asset = Asset::getInstance();

if ($arResult['RELS']["PREV"] != "") {
    $asset->addString($arResult['RELS']["PREV"], true);
}
if ($arResult['RELS']["NEXT"] != "") {
    $asset->addString($arResult['RELS']["NEXT"], true);
}