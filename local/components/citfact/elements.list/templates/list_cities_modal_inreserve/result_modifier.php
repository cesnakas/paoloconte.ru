<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arAlf = array();
$arResult['MAIN_CITIES'] = array();
$arResult['AUTOCOMPLETE_CITIES'] = array();
foreach ($arResult['ITEMS'] as $arItem) {
	if (strlen($arItem["NAME"]) > 0) {
		$first = strtoupper(substr($arItem["NAME"], 0, 1));
		$arAlf[$first][] = $arItem;
		if ($arItem['ID'] == $_SESSION['CITY_ID']){
			$arResult['ACTIVE_LETTER'] = $first;
			$arResult['CURRENT_CITY_NAME'] = $arItem['NAME'];
		}
		if($arItem['PROPERTY_MAIN_VALUE'] != ''){
			$arResult['MAIN_CITIES'][] = $arItem;
		}
	}

	$arTemp = array();
	$arTemp['label'] = $arItem['NAME'];
	$arTemp['value'] = $arItem['NAME'];
	$arResult['AUTOCOMPLETE_CITIES'][] = $arTemp;
	$arResult['AUTOCOMPLETE_IDS'][$arItem['NAME']] = $arItem['ID'];
}

ksort($arAlf);
//echo "<pre style=\"display:none;\">"; print_r($arResult['AUTOCOMPLETE_CITIES']); echo "</pre>";
$arResult["ITEMS"] = $arAlf;
