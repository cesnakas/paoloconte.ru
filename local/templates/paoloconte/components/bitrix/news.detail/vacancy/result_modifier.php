<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if($arParams['ADD_SECTIONS_CHAIN'] && !empty($arResult['NAME']))
{
	$arResult['SECTION']['PATH'][] = array(
		'NAME' => $arResult['NAME'],
		'PATH' => ' ');
	$component = $this->__component;
	$component->arResult = $arResult;
}

$curdate = date('Y-m-d');
if ($arResult['PROPERTIES']['DATE_END']['VALUE'] != '') {
	$arResult['DATE_DIFF'] = \Citfact\Tools::datediff($curdate, $arResult['PROPERTIES']['DATE_END']['VALUE']);
}

// Список городов
$arCities = $arResult['DISPLAY_PROPERTIES']['CITY']['LINK_ELEMENT_VALUE'];
if (!empty($arCities)){
	foreach ($arCities as $arCity) {
		$arTemp = array(
			'NAME' => $arCity['NAME'],
			'CODE' => $arCity['CODE']
		);
		$arResult['CITIES_LIST'][] = $arTemp;
	}
}