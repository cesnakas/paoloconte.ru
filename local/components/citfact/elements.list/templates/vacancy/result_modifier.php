<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

foreach ($arResult['SECTIONS'] as $key => $arSection) {
	$arResult['SECTIONS'][$key]['COUNT'] = count($arResult['SECTIONS'][$key]['ITEMS']);
}

