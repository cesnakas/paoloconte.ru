<?php

/*
 * This file is part of the Studio Fact package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$app = Application::getInstance();
$request = $app->getContext()->getRequest();
if (!empty($request['CODE'])) {
    $arParams['CITY_CODE'] = $request['CODE'];
}
if ($this->StartResultCache()) {
	$arRegions = \Citfact\Paolo::GetRegionSettings();
	$arShopsAll = \Citfact\Paolo::GetShops();
	$arShops = array();
	foreach ($arRegions as $arRegion){
		$arCities = unserialize($arRegion['~PROPERTY_CITIES_VALUE']['TEXT']);
		foreach ($arCities as $arCity){
			if (!empty($arShopsAll[$arCity['CITY_ID']])) {
				$arIds[] = $arCity['CITY_ID'];
				foreach ($arShopsAll[$arCity['CITY_ID']] as $arShop) {
					$arShops[$arCity['CITY_ID']][] = $arShop;
				}
			}
		}
	}
	$arResult['SHOPS'] = $arShops;
	//$arResult['SHOPS_JSON'] = json_encode($arShops);
	$arCities = \Citfact\Paolo::GetCitiesByFilter(array('ID' => $arIds));
	foreach ($arCities as &$arCity){
		$arCity['SHOPS_COUNT'] = count($arShops[$arCity['ID']]);
	}
	unset($arCity);
    /**
     * Поднял Москву и Питер вверх в выдаче
     */
    foreach ($arCities as $key => $arCity) {
        if ($arCity["CODE"] == "sankt-peterburg") {
            $arCities = array_merge(array($key => $arCity) + $arCities);
        }
    }
    foreach ($arCities as $key => $arCity) {
        if ($arCity["CODE"] == "moscow") {
            $arCities = array_merge(array($key => $arCity) + $arCities);
        }
    }

    if ($arCities[count($arCities)-1] == $arCities[count($arCities)-2]){
        unset($arCities[count($arCities)-1]); //Убирается дубль последней страницы
    }
	$arResult['CITIES'] = $arCities;

	//$APPLICATION->AddChainItem($arResult['SHOP']['CITY_NAME'], '/shops/'.$arResult['SHOP']['CITY_CODE'].'/');

	if ($arParams['CITY_CODE'] != ''){
		$arCityCurrent = \Citfact\Paolo::GetCitiesByFilter(array('CODE' => $arParams['CITY_CODE']));
		$arShopsCurrent = \Citfact\Paolo::GetShopsByFilter(array('PROPERTY_CITY.CODE' => $arCityCurrent[0]['CODE']));
		if (empty($arCityCurrent)){
			$this->AbortResultCache();
			@define("ERROR_404", "Y");
			CHTTP::SetStatus("404 Not Found");
			LocalRedirect("/shops/", false, "301 Moved permanently");
		}
		// Если в данном городе один магазин, то редиректим на страницу этого магазина
		if (count($arShopsCurrent) == 1 && $arParams['ONE_SHOP_REDIRECT'] != 'N'){
			LocalRedirect("/".$arParams['DIRECTORY_CODE'].'/'.$arShopsCurrent[0]['PROPERTY_CITY_CODE'].'/'.$arShopsCurrent[0]['CODE'].'/', false, "301 Moved permanently");
		}

		$arResult['CITY_CURRENT'] = $arCityCurrent;
		$arResult['SHOPS_CURRENT'] = $arShopsCurrent;
		$APPLICATION->SetTitle('Магазины в городе '.$arCityCurrent[0]['NAME']);
		$APPLICATION->AddChainItem($arCityCurrent[0]['NAME'], '#');

	}

	$this->IncludeComponentTemplate();
}

