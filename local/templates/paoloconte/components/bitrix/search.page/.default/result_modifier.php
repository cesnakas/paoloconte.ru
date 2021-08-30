<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arIds = array();
$arIdsShops = array();
foreach ($arResult["SEARCH"] as $arItem){
	$arIds[] = $arItem['ITEM_ID'];

	if ($arItem['PARAM2'] == IBLOCK_SHOPS){
		$arIdsShops[] = $arItem['ITEM_ID'];
	}
}

if (!empty($arIds)) {
	$res = CIBlockElement::GetList(
		Array(),    //array arOrder
		Array("IBLOCK_ID" => IBLOCK_CATALOG, 'ID' => $arIds, "ACTIVE" => "Y", "SECTION_GLOBAL_ACTIVE" => "Y"),    //array arFilter
		false,    // mixed arGroupBy
		false,    //mixed arNavStartParams
		Array('ID')    //array arSelectFields
	);
	$arResult['CATALOG_IDS'] = array();
	while ($arRes = $res->GetNext()) {
		$arResult['CATALOG_IDS'][] = $arRes['ID'];
	}
}

$arResult['SHOPS_CURRENT'] = array();

if (!empty($arIdsShops)) {
	$arOrder = array("SORT" => "ASC");
	$arFilter = array('IBLOCK_ID' => IBLOCK_SHOPS, "ID" => $arIdsShops, 'ACTIVE' => 'Y');
	$arSelectFields = array("ID", "ACTIVE", "NAME", "CODE", "PROPERTY_ADDRESS", "PROPERTY_GRAPHICK", "PROPERTY_PHONE", "PROPERTY_CITY.CODE", "PROPERTY_CITY.NAME");
	$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
	while ($arElement = $rsElements->GetNext()) {
		$arResult["SHOPS_CURRENT"][] = $arElement;
	}
}