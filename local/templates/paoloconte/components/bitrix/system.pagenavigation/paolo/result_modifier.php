<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");

$arRels = array(
    "PREV" => "",
    "NEXT" => ""
);

if ($arResult["NavPageNomer"] > 1){
    $arRels["PREV"] =  '<link rel="prev" href="'.$arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]-1).'">';
}

if($arResult["NavPageNomer"] < $arResult["NavPageCount"]){
    $arRels["NEXT"] = '<link rel="next" href="'.$arResult["sUrlPath"].'?'.$strNavQueryString.'PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]+1).'">';
}
$arResult['RELS'] = $arRels;

$this->__component->setResultCacheKeys(['RELS']);