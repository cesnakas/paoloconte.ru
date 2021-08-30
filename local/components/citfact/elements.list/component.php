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

Loader::includeModule('iblock');

Loc::loadMessages(__FILE__);

if (empty($arParams["CACHE_TYPE"]) || intval($arParams["CACHE_TIME"]) <= 0) {
    $arParams["CACHE_TIME"] = 3600;
}

if (empty($arParams["CACHE_TYPE"])) {
    $arParams["CACHE_TYPE"] = 'A';
}

$app = Application::getInstance();
$request = $app->getContext()->getRequest();

$iblock_id = (int)$arParams['IBLOCK_ID'];
if ($this->StartResultCache() && $iblock_id != '') {

    if (!empty($arParams['PROPERTY_CODES']))
    foreach($arParams['PROPERTY_CODES'] as &$propCode){
        $propCode = 'PROPERTY_'.$propCode;
    }

    $arOrder = array("SORT" => 'ASC', 'NAME'=>'ASC');
	if (!empty($arParams['SORT'])){
		$arOrder = $arParams['SORT'];
	}
    $arFilter = array(
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        "ACTIVE"=>"Y",
		// Фильтр по дате: с пустой датой завершения активности или с датой завершения больше текущей даты
//		array(
//			"LOGIC" => "OR",
//			array("DATE_ACTIVE_TO"=>false),
//			array(">DATE_ACTIVE_TO"=>ConvertTimeStamp(time(),"FULL"))
//		)
	);
	if (!empty($arParams['FILTER'])){
		$arFilter = array_merge($arFilter, $arParams['FILTER']);
	}

    $arSelectFields = array("ID", "ACTIVE", "NAME", "IBLOCK_SECTION_ID");
    if (!empty($arParams['FIELDS']))
		$arSelectFields = array_merge($arSelectFields, $arParams['FIELDS']);
    $arSelectFields = array_merge($arSelectFields, $arParams['PROPERTY_CODES']);

	$arNavParams = false;
	if ((int)$arParams['ELEMENTS_COUNT'] > 0){
		$arNavParams = array('nTopCount' => (int)$arParams['ELEMENTS_COUNT']);
	}

	$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, $arNavParams, $arSelectFields);

	$arResult['ITEMS'] = array();
    $arSectionIds = array();
    while($arElement = $rsElements->GetNext()){
        if ($arElement['IBLOCK_SECTION_ID']){
            $arSectionIds[] = $arElement['IBLOCK_SECTION_ID'];
        }
        $arResult['ITEMS'][] = $arElement;
    }

    if (!empty($arSectionIds)) {
        $res = CIBlockSection::GetList(
            Array("SORT" => "ASC", 'NAME'=>'ASC'),    // Order
            Array("ID" => $arSectionIds, "IBLOCK_ID" => $arParams['IBLOCK_ID'], "GLOBAL_ACTIVE" => "Y"),    // Filter
            false,    // IncCount
            Array("ID", 'NAME', 'UF_IMAGES')    // Select Fields
        );
        $arSections = array();
        while ($arRes = $res->GetNext()) {
            $arSections[$arRes['ID']]['NAME'] = $arRes['NAME'];
            $arSections[$arRes['ID']]['UF_IMAGES'] = $arRes['UF_IMAGES'];
        }

        foreach($arResult['ITEMS'] as $arItem){
            $arSections[$arItem['IBLOCK_SECTION_ID']]['ITEMS'][] = $arItem;
        }
        $arResult['SECTIONS'] = $arSections;
    }

    $this->IncludeComponentTemplate();
}

