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

$app = Application::getInstance();
$request = $app->getContext()->getRequest();

$arParams["REVIEWS_COUNT"] = intval($arParams["REVIEWS_COUNT"]);
if($arParams["REVIEWS_COUNT"]<=0)
	$arParams["REVIEWS_COUNT"] = 10;

$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]=="Y";
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]=="Y";
$arNavParams = array(
	"nPageSize" => $arParams["REVIEWS_COUNT"],
	"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	"bShowAll" => $arParams["PAGER_SHOW_ALL"],
);
$arNavigation = CDBResult::GetNavParams($arNavParams);
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);

$isIblockReviews = (defined('IBLOCK_PRODUCT_REVIEW') && IBLOCK_PRODUCT_REVIEW !== false);
$isIblockCatalog = (defined('IBLOCK_CATALOG') && IBLOCK_CATALOG !== false);

if ($this->StartResultCache(false, $arNavigation) && $isIblockReviews) {
	$arResult['ITEMS'] = array();
	global $USER;
	$idUser = $USER->GetID();
	$arProduscIds = array();
	$rsElement = CIBlockElement::GetList(Array("SORT"=>"ASC", "ID"=>"DESC"), Array("IBLOCK_ID"=>IBLOCK_PRODUCT_REVIEW, "ACTIVE"=>"Y", "PROPERTY_USER_ID_VALUE" => $idUser), false, $arNavParams, Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_USER_ID", "PROPERTY_USER_NAME", "PROPERTY_STARS", "PROPERTY_PRODUCT_ID", "PROPERTY_MESSAGE", "DATE_CREATE"));
	while($ob = $rsElement->GetNextElement())
	{
		$arFields = $ob->GetFields();
		$arResult['ITEMS'][] = $arFields;
		$arProduscIds[$arFields['PROPERTY_PRODUCT_ID_VALUE']] = $arFields['PROPERTY_PRODUCT_ID_VALUE'];
	}
	
	$navComponentParameters = array();
	$arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx(
		$navComponentObject,
		$arParams["PAGER_TITLE"],
		$arParams["PAGER_TEMPLATE"],
		$arParams["PAGER_SHOW_ALWAYS"],
		$this,
		$navComponentParameters
	);
	
	if ($isIblockCatalog) {
		$arProducts = array();
		$res = CIBlockElement::GetList(Array("SORT"=>"ASC", "ID"=>"DESC"), Array("IBLOCK_ID"=>IBLOCK_CATALOG, "ID"=>$arProduscIds), false, Array("nTopCount"=>count($arProduscIds)), Array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL",));
		while($ob = $res->GetNextElement())
		{
			$arFields = $ob->GetFields();
			$arProducts[$arFields['ID']] = $arFields;
		}
		
		foreach ($arResult['ITEMS'] as $key => $item) {
			$arResult['ITEMS'][$key]['PRODUCT'] = $arProducts[$item['PROPERTY_PRODUCT_ID_VALUE']];
		}
	}
	
    $this->IncludeComponentTemplate();
}

