<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if ($arResult["nStartPage"] != '1') {
    $arResult["nStartPage"] = $arResult["nStartPage"] + 1;
}

if ($arResult["nEndPage"] != strval($arResult["NavPageCount"])) {
    $arResult["nEndPage"] = $arResult["nEndPage"] - 1;
}
?>