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
use Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);
Loader::includeModule('iblock');
Loader::includeModule('sale');

$CSaleBasket = new CSaleBasket();
$cntBasketItems = $CSaleBasket->GetList(
    array(),
    array(
        "FUSER_ID" => $CSaleBasket->GetBasketUserID(),
        "LID" => SITE_ID,
        "ORDER_ID" => "NULL",
        "DELAY" => "N",
        "CAN_BUY" => "Y"
    ),
    array(),
    false,
    array("ID")
);

$app = Application::getInstance();
$request = $app->getContext()->getRequest();

//CJSCore::Init();
if ($arParams['IBLOCK_ID'] != '' && !empty($arParams['SHOW_PROPERTIES']) && $cntBasketItems > 0) {
	$IBLOCK_ID = (int)$arParams['IBLOCK_ID'];

	$arPropCodes = array_keys($arParams['SHOW_PROPERTIES']);

	$properties = CIBlockProperty::GetList(Array('SORT' => 'ASC'), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID));
	$arResult['SHOW_PROPERTIES'] = array();
	while ($arResProp = $properties->GetNext()){
		if (in_array($arResProp['CODE'], $arPropCodes)){
			$arResProp['PARAMS_TYPE'] = $arParams['SHOW_PROPERTIES'][$arResProp['CODE']]['type'];
			$arResProp['PLACEHOLDER'] = $arParams['SHOW_PROPERTIES'][$arResProp['CODE']]['placeholder'];
			$arResProp['REQUIRED'] = $arParams['SHOW_PROPERTIES'][$arResProp['CODE']]['required'];
			$arResProp['VALUE'] = $arParams['SHOW_PROPERTIES'][$arResProp['CODE']]['value'];
			$arResProp['CLASS'] = $arParams['SHOW_PROPERTIES'][$arResProp['CODE']]['class'];
			$arResProp['ERROR'] = $arParams['SHOW_PROPERTIES'][$arResProp['CODE']]['error'];
//			$arResProp['ID'] = $arParams['SHOW_PROPERTIES'][$arResProp['CODE']]['id'];
			$arResProp['NAME'] = $arParams['SHOW_PROPERTIES'][$arResProp['CODE']]['name'];
			$arResult['SHOW_PROPERTIES'][] = $arResProp;
		}
	}

    // Cancel cache data
    /*if ($arParams['ID'] < 10) {
        $this->AbortResultCache();
    }*/

    $this->IncludeComponentTemplate();
}

