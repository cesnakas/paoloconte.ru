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

if ($this->StartResultCache()) {
	$arShop = \Citfact\Paolo::GetShopsByFilter(array('CODE' => $arParams['SHOP_CODE']));

	if (empty($arShop)){
		$this->AbortResultCache();
		include($_SERVER['DOCUMENT_ROOT'].'/404.php');
		exit;
	}

	$arResult['SHOP'] = array(
		'ID' => $arShop[0]['ID'],
		'NAME' => $arShop[0]['NAME'],
		'CODE' => $arShop[0]['CODE'],
		'CITY_ID' => $arShop[0]['PROPERTY_CITY_VALUE'],
		'CITY_CODE' => $arShop[0]['PROPERTY_CITY_CODE'],
		'CITY_NAME' => $arShop[0]['PROPERTY_CITY_NAME'],
		'SITE' => $arShop[0]['PROPERTY_SITE_VALUE'],
		'ADDRESS' => $arShop[0]['PROPERTY_ADDRESS_VALUE'],
		'PHONE' => $arShop[0]['PROPERTY_PHONE_VALUE'],
		'GRAPHICK' => $arShop[0]['~PROPERTY_GRAPHICK_VALUE']['TEXT'],
		'IMAGES' => $arShop[0]['PROPERTY_IMAGES_VALUE'],
		'COORDS' => $arShop[0]['PROPERTY_COORDS_VALUE'],
		'IBLOCK_ID' => $arShop[0]['IBLOCK_ID'],
	);

	// Все магазины
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

	$arCityCurrent = \Citfact\Paolo::GetCitiesByFilter(array('CODE' => $arResult['SHOP']['CITY_CODE']));
	$arResult['CITY_CURRENT'] = $arCityCurrent;

	$arResult['REVIEWS'] = \Citfact\Paolo::GetReviewsShopsByFilter(array('PROPERTY_SHOP_ID' => $arResult['SHOP']['ID']));

	$arResult['BACK_URL'] = count($arResult['SHOPS'][$arResult['SHOP']['CITY_ID']]) == 1? '':$arResult['SHOP']['CITY_CODE'].'/';

	$APPLICATION->SetTitle($arResult['SHOP']['NAME'], array());
	$APPLICATION->AddChainItem($arResult['SHOP']['CITY_NAME'], '/shops/'.$arResult['SHOP']['CITY_CODE'].'/');
	$APPLICATION->AddChainItem($arResult['SHOP']['NAME'], '#');

	$currentUrl = $APPLICATION->GetCurDir();
	$canonicalUrl = '/shops/'.$arResult['SHOP']['CITY_CODE'].'/'.$arResult['SHOP']['CODE']. '/';

	if ($currentUrl !== $canonicalUrl){
		// При отсутсвии $APPLICATION->AddBufferContent([$APPLICATION, 'GetLink'], 'canonical'); не работает
		$APPLICATION->SetPageProperty('canonical', 'https://' . $_SERVER['SERVER_NAME'] . $canonicalUrl);
	}

    $this->IncludeComponentTemplate();
}