<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arResult['ADDRESSES'] = array();
if ($USER->IsAuthorized()) {
	$arResult['ADDRESSES'] = \Citfact\Paolo::GetUserAddresses($USER->GetID());
}

$zipSelected = '';
foreach ($arResult['ADDRESSES'] as $arAddress){
	if ($arAddress['SELECTED'] == 1){
		$zipSelected = $arAddress['ZIP'];
	}
}

foreach ($arResult["ORDER_PROP"]["USER_PROPS_N"] as &$arProp) {
	if ($arProp['CODE'] == 'ZIP' && $arProp['VALUE'] == '') {
		$arProp['VALUE'] = $zipSelected;
	}
}