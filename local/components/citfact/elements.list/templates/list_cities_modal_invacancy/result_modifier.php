<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arShopsAll = \Citfact\Paolo::GetShops();

foreach ($arResult['ITEMS'] as $key => $arItem) {
	if (!array_key_exists($arItem['ID'], $arShopsAll)){
		unset($arResult['ITEMS'][$key]);
	}
	/*if (strlen($arItem["NAME"]) > 0) {
		$first = strtoupper(substr($arItem["NAME"], 0, 1));
		$arAlf[$first][] = $arItem;
		if ($arItem['ID'] == $_SESSION['CITY_ID']){
			$arResult['ACTIVE_LETTER'] = $first;
			$arResult['CURRENT_CITY_NAME'] = $arItem['NAME'];
		}
	}*/
}

//echo "<pre style=\"display:none;\">"; print_r($arResult['ITEMS']); echo "</pre>";
//echo "<pre style=\"display:none;\">"; print_r($arShopsAll); echo "</pre>";