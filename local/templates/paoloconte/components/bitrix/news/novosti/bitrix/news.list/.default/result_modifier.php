<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$curdate = date('Y-m-d');
foreach ($arResult['ITEMS'] as &$arItem) {
	if ($arItem['PROPERTIES']['DATE_END']['VALUE'] != '') {
		$arItem['DATE_DIFF'] = \Citfact\Tools::datediff($curdate, $arItem['PROPERTIES']['DATE_END']['VALUE']);
	}
}
