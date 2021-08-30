<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
foreach ($arResult['ITEMS'] as $key => $arItem){
	if ($arItem['IBLOCK_SECTION_ID'] != 96){
		unset($arResult['ITEMS'][$key]);
	}
}