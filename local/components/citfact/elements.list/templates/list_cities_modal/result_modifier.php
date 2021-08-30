<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arAlf = array();
$arResult['MAIN_CITIES'] = array();
$arResult['AUTOCOMPLETE_CITIES'] = array();

foreach ($arResult['ITEMS'] as $arItem) {
	if (strlen($arItem["NAME"]) > 0) {
	    //берем всегда только первую большую заглавную букву, для случая с поселками (пос. Вяземский, В)
        $substrName = explode(".",$arItem["NAME"]);
        if (count($substrName) > 1){
            $first = strtoupper(mb_substr(trim($substrName[1]), 0, 1));
        }
        else{
            $first = strtoupper(mb_substr(trim($substrName[0]), 0, 1));
        }

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


