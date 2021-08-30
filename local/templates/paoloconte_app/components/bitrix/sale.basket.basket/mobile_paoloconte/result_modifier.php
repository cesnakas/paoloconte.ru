<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/*$arIds = array();
foreach ($arResult['ITEMS'] as $arItem) {
	$arIds[] = $arItem['PRODUCT_ID'];
}*/

if (!empty($arResult['ITEMS'])) {
	$arEmptyPreview = false;
	$strEmptyPreview = $this->GetFolder() . '/images/no_photo.png';
	$arResult['NOPHOTO'] = $strEmptyPreview;
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strEmptyPreview)) {
		$arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'] . $strEmptyPreview);
		if (!empty($arSizes)) {
			$arEmptyPreview = array(
				'SRC' => $strEmptyPreview,
				'WIDTH' => intval($arSizes[0]),
				'HEIGHT' => intval($arSizes[1])
			);
		}
		unset($arSizes);
	}
	unset($strEmptyPreview);
}

/*ПОДГОТОВКА КАРТИНОК*/
$imageConfig = array(
	'TYPE'=>'ONE',
	'SIZE' => array(
		'SMALL' => array('W'=>90,'H'=>120)
	)
);

foreach ($arResult["GRID"]["ROWS"] as $key => $arItem){
	$articul = trim($arItem['CATALOG']['PROPERTY_184_VALUE'][0]);
	$arResult["GRID"]["ROWS"][$key]['CATALOG_PHOTO'] = \Citfact\Paolo::getProductImage($articul, $imageConfig);
}

// Проверяем наличие размеров на складе интернет-магазина
$arIdsMain = array();
$arIdsOffers = array();
$arIdsOffersAll = array();
$arResult['OFFERS'] = array();
$arResult['OFFERS_AMOUNT'] = array();
$arResult['SIZES_AMOUNT'] = array();

foreach ($arResult["GRID"]["ROWS"] as $key => $arItem){
	$articul = trim($arItem['CATALOG']['PROPERTY_184_VALUE'][0]);
	$arResult["GRID"]["ROWS"][$key]['CATALOG_PHOTO'] = \Citfact\Paolo::getProductImage($articul, $imageConfig);

	if ($arItem['CATALOG']['PROPERTIES']['CML2_LINK']['VALUE'] != '') {
		$arIdsMain[] = $arItem['CATALOG']['PROPERTIES']['CML2_LINK']['VALUE'];
		$arIdsOffers[] = $arItem['PRODUCT_ID'];
	}
}

if (!empty($arIdsOffers)) {
	// Смотрим наличие торговых предложений в складе «Интернет-магазин»
	// Достаем все предложения для основных товаров
	$arOrder = array();
	$arFilter = array('IBLOCK_ID' => IBLOCK_SKU, 'ACTIVE' => 'Y', 'PROPERTY_CML2_LINK' => $arIdsMain);
	$arSelectFields = array("ID", "ACTIVE", 'PROPERTY_CML2_LINK', 'PROPERTY_RAZMER');
	$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
	while ($arOffer = $rsElements->GetNext()) {
		$arResult['OFFERS'] [$arOffer['PROPERTY_CML2_LINK_VALUE']] [$arOffer['PROPERTY_RAZMER_VALUE']] = $arOffer;
		$arIdsOffersAll[] = $arOffer['ID'];
	}

	// Проверяем наличие
	$rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' =>$arIdsOffersAll, 'STORE_ID' => 5), false, false, array());
	while ($arStore = $rsStore->Fetch()){
		if ($arStore['AMOUNT'] > 0){
			$arResult['OFFERS_AMOUNT'] [$arStore['PRODUCT_ID']] = $arStore['AMOUNT'];
		}
	}

	// Заполняем итоговый массив
	$arTemp = array();
	foreach($arResult['OFFERS'] as $arItem){
		foreach ($arItem as $arOffer){
			if (in_array($arResult['OFFERS_AMOUNT'][$arOffer['ID']], $arResult['OFFERS_AMOUNT'])){
				$arTemp[$arOffer['PROPERTY_CML2_LINK_VALUE']] [$arOffer['PROPERTY_RAZMER_VALUE']] = $arResult['OFFERS_AMOUNT'] [$arOffer['ID']];
			}
		}
	}
	$arResult['SIZES_AMOUNT'] = $arTemp;
}