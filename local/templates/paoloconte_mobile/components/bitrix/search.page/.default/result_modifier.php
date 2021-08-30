<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arIds = array();
foreach ($arResult["SEARCH"] as $arItem){
	$arIds[] = $arItem['ITEM_ID'];
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