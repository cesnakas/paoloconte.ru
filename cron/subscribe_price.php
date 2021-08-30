<?
$dir = __DIR__;
if (strpos($dir, '/cron')) {
	$dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');

if(!Loader::IncludeModule("iblock"))
{
	ShowError("IBLOCK_MODULE_NOT_INSTALLED");
	return;
}
if(!Loader::IncludeModule("sale") || !Loader::IncludeModule("catalog"))
{
	ShowError("SALE_MODULE_NOT_INSTALLED");
	return;
}
global $USER;
global $DB;

// Достаем подписки, которые еще не высылались
$cnt = array('OFFER'=>0, 'ITEM'=>0, 'ALL'=>0);
$arIdsTovars = array();
$arIdsCities = array();
$arSubscribes = array();
$arOrder = array();
$arFilter = array('IBLOCK_ID' => IBLOCK_SUBSCRIBE_PRICE, 'ACTIVE' => 'Y', 'PROPERTY_SENDED' => false,);
$arSelectFields = array("ID", "NAME", "IBLOCK_ID", "ACTIVE", "PROPERTY_TOVAR_ID", "PROPERTY_CITY_ID", "PROPERTY_USER_ID", "PROPERTY_EMAIL", "PROPERTY_PRICE", "DATE_CREATE");
$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
while($arElement = $rsElements->GetNext())
{
	if (CCatalogSKU::getProductList($arElement['PROPERTY_TOVAR_ID_VALUE'])){
		$arIdsTovars[] = $arElement['PROPERTY_TOVAR_ID_VALUE'];
		$cnt['OFFER']++;
	}else{
		$arIdsTovarsNotOffer[] = $arElement['PROPERTY_TOVAR_ID_VALUE'];
		$cnt['ITEM']++;
	}
	$cnt['ALL']++;
	$arIdsCities[] = $arElement['PROPERTY_CITY_ID_VALUE'];
	$arSubscribes[] = $arElement;
}
\Citfact\Tools::pre($cnt, $stdin=false, $die=false, $all=false);

if (!empty($arSubscribes)) {
	
	// Достаем ID типов цен в городах
	$arIdsCities = array_unique($arIdsCities);
	$arPriceTypes = array();
	foreach ($arIdsCities as $city_id) {
		$arPriceTypes[$city_id] = \Citfact\Paolo::GetRegionPriceTypes($city_id);
	}

	$arPriceTypeIds = array();
	foreach ($arPriceTypes as $arPriceType) {
		$arPriceTypeIds[] = $arPriceType['PRICE_ID'];
		$arPriceTypeIds[] = $arPriceType['PRICE_ID_ACTION'];
	}

	// Формируем массив выбора цен каталога
	$arSelectPrices = array();
	foreach ($arPriceTypeIds as $price_id) {
		$arSelectPrices[] = 'CATALOG_GROUP_' . $price_id;
	}

	$arTovars = array();
	// Достаем товары с ценами (SKU)
	$arOrder = array();
	if (!empty($arIdsTovars)) {
		$arFilter = array('IBLOCK_ID' => IBLOCK_SKU, 'ACTIVE' => 'Y', 'ID' => $arIdsTovars);
		$arSelectFields = array("ID", "IBLOCK_ID", "NAME", 'PROPERTY_CML2_LINK', 'PROPERTY_CML2_LINK.NAME', 'DETAIL_PAGE_URL');
		$arSelectFields = array_merge($arSelectFields, $arSelectPrices);
		$rsElements = CIBlockElement::GetList($arOrder, $arFilter, false, FALSE, $arSelectFields);
		while ($arElement = $rsElements->GetNext()) {
			$arTovars[$arElement['ID']] = $arElement;
			$arTovars[$arElement['ID']]['ISOFFER'] = 'Y';
		}
	}
	// Достаем товары с ценами (CATALOG)
	if (!empty($arIdsTovarsNotOffer)) {
		$arFilter = array('IBLOCK_ID' => IBLOCK_CATALOG, 'ACTIVE' => 'Y', 'ID' => $arIdsTovarsNotOffer);
		$arSelectFields = array("ID", "IBLOCK_ID", "NAME", 'DETAIL_PAGE_URL');
		$arSelectFields = array_merge($arSelectFields, $arSelectPrices);
		$rsElements = CIBlockElement::GetList($arOrder, $arFilter, false, FALSE, $arSelectFields);
		while ($arElement = $rsElements->GetNext()) {
			$arTovars[$arElement['ID']] = $arElement;
			$arTovars[$arElement['ID']]['ISOFFER'] = 'N';
		}
	}
	
	// Перебираем подписки и сравниваем цены
	foreach ($arSubscribes as $arSubscribe) {
		$tovar_id = $arSubscribe['PROPERTY_TOVAR_ID_VALUE'];
		$email = $arSubscribe['PROPERTY_EMAIL_VALUE'];
		$city_id = $arSubscribe['PROPERTY_CITY_ID_VALUE'];
		$price_sub = $arSubscribe['PROPERTY_PRICE_VALUE'];
		$price_id = $arPriceTypes[$city_id]['PRICE_ID'];
		$price_id_action = $arPriceTypes[$city_id]['PRICE_ID_ACTION'];

		$tovar_price = $arTovars[$tovar_id]['CATALOG_PRICE_' . $price_id];
		$tovar_price_action = $arTovars[$tovar_id]['CATALOG_PRICE_' . $price_id_action];

		echo 'Подписка на товар '.(($arTovars[$tovar_id]['ISOFFER']=='Y')?"(offer)":"(item)").' ' . $tovar_id . ' Email ' . $email . ' Цена рассылки ' . $price_sub . '<br>';
		echo $tovar_price . '<br>';
		echo $tovar_price_action . '<br>';
		if ($tovar_price != '' && $tovar_price > 0 && $tovar_price <= $price_sub
			|| $tovar_price_action != '' && $tovar_price_action > 0 && $tovar_price_action <= $price_sub
		) {
			echo 'Отсылаем<br>';

			$arFields = array(
				'TOVAR_NAME' => ($arTovars[$tovar_id]['ISOFFER']=='Y')?$arTovars[$tovar_id]['PROPERTY_CML2_LINK_NAME']:$arTovars[$tovar_id]['NAME'],
				'TOVAR_URL' => 'http://' . SITE_SERVER_NAME . $arTovars[$tovar_id]['DETAIL_PAGE_URL'],
				'USER_EMAIL' => $email
			);

			CEvent::Send('SUBSCRIBE_PRICE', SITE_ID, $arFields);

			// Помечаем подписку как высланную
			CIblockElement::SetPropertyValuesEx($arSubscribe['ID'], false, array('SENDED' => 'Y'));
		}
	}
}

