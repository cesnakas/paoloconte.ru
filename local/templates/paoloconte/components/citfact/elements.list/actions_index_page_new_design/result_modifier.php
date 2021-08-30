<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$curdate = date('Y-m-d');
foreach ($arResult['ITEMS'] as &$arItem) {
	if ($arItem['PROPERTY_DATE_END_VALUE'] != '') {
		$arItem['DATE_DIFF'] = \Citfact\Tools::datediff($curdate, $arItem['PROPERTY_DATE_END_VALUE']);
	}
}
