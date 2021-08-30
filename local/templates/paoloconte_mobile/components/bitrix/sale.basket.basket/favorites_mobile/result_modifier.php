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

// Достаем артикулы и наличие отложенных товаров
foreach ($arResult["GRID"]["ROWS"] as $k => $arItem) {
	if ($arItem["DELAY"] == "Y" && $arItem["CAN_BUY"] == "Y") {
		$arIds[] = $arItem['PRODUCT_ID'];
	}
}

if (!empty($arIds)) {
	$arOrder = array("SORT" => "ASC");
	$arFilter = array('IBLOCK_ID' => IBLOCK_SKU, 'ACTIVE' => 'Y', 'ID' => $arIds);
	$arSelectFields = array("ID", "ACTIVE", "NAME",
		'PROPERTY_CML2_LINK.PROPERTY_CML2_ARTICLE',
		'PROPERTY_CML2_LINK.PROPERTY_OFFERS_AMOUNT',
		'PROPERTY_RAZMER');
	$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
	$arArticuls = array();
	$arOffersAmount = array();
	$arSizes = array();
	while ($arElement = $rsElements->GetNext()) {
		//echo "<pre style=\"display:block;\">"; print_r($arElement); echo "</pre>";
		$arArticuls[$arElement['ID']] = $arElement['PROPERTY_CML2_LINK_PROPERTY_CML2_ARTICLE_VALUE'];
		$arOffersAmount[$arElement['ID']] = $arElement['PROPERTY_CML2_LINK_PROPERTY_OFFERS_AMOUNT_VALUE'];
		$arSizes[$arElement['ID']] = $arElement['PROPERTY_RAZMER_VALUE'];
	}

	$arResult['ARTICULS'] = $arArticuls;
	$arResult['OFFERS_AMOUNT'] = $arOffersAmount;

	/*ПОДГОТОВКА КАРТИНОК*/
	$imageConfig = array(
		'TYPE' => 'ONE',
		'SIZE' => array(
			'SMALL' => array('W' => 90, 'H' => 120)
		)
	);

	foreach ($arResult["GRID"]["ROWS"] as $key => $arItem) {
		if ($arItem["DELAY"] == "Y" && $arItem["CAN_BUY"] == "Y") {
			$articul = trim($arArticuls[$arItem['PRODUCT_ID']]);
			$arResult["GRID"]["ROWS"][$key]['ARTICUL'] = $articul;
			$arResult["GRID"]["ROWS"][$key]['CATALOG_PHOTO'] = \Citfact\Paolo::getProductImage($articul, $imageConfig);
			$arResult["GRID"]["ROWS"][$key]['RAZMER'] = $arSizes[$arItem['PRODUCT_ID']];
			$arResult["GRID"]["ROWS"][$key]['OFFERS_AMOUNT'] = $arOffersAmount[$arItem['PRODUCT_ID']];
		}
	}


	// Смотрим наличие на складе «Интернет-магазин»
	$arItemsAmount = array();
	$rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' => $arIds, 'STORE_ID' => 5), false, false, array());
	while ($arStore = $rsStore->Fetch()) {
		if ($arStore['AMOUNT'] > 0) {
			$arItemsAmount[ $arStore['PRODUCT_ID'] ] = $arStore['AMOUNT'];
		}
	}
	$arResult['STORE_AMOUNT'] = $arItemsAmount;
}