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
/*перемещение ошибок*/
if(!empty($arResult['ERROR'])) {
    foreach($arResult['ERROR'] as $keyError => $arError):
        $pos1 = stripos($arError, 'Выберите пункт самовывоз');
        if ($pos1 !== false) {
            $arResult['ERROR_KEY']['PICKUP'] = $keyError;
        }
        foreach($arResult['ORDER_PROP']['PRINT'] as $keyProp => $arProperty) {
            $pos1 = stripos($arError, $arProperty['NAME']);
            if ($pos1 !== false) {
                $arResult['ERROR_KEY']['PRINT'][$keyProp] = $keyError;
            }
        }
        foreach($arResult['ORDER_PROP']['USER_PROPS_N'] as $keyProp => $arProperty) {
            $pos1 = stripos($arError, $arProperty['NAME']);
            if ($pos1 !== false) {
                $arResult['ERROR_KEY']['USER_PROPS_N'][$keyProp] = $keyError;
            }
        }
        foreach($arResult['ORDER_PROP']['USER_PROPS_Y'] as $keyProp => $arProperty) {
            $pos1 = stripos($arError, $arProperty['NAME']);
            if ($pos1 !== false) {
                $arResult['ERROR_KEY']['USER_PROPS_Y'][$keyProp] = $keyError;
            }
        }
    endforeach;
}