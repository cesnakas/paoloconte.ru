<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

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

$arIds = array();
foreach ($arResult["ITEMS"] as $key => $arItem){
	$arIds[] = $arItem['PROPERTY_TOVAR_ID_VALUE'];
}

// Достаем товары
$arResult['TOVARS'] = array();
if (!empty($arIds)) {
	// Достаем ТП
	$arOrder = array();
	$arFilter = array('IBLOCK_ID' => IBLOCK_SKU, 'ID' => $arIds, 'ACTIVE' => 'Y');
	$arSelectFields = array("ID", "ACTIVE", "NAME",
		'PROPERTY_CML2_LINK.PROPERTY_CML2_ARTICLE',
		'DETAIL_PAGE_URL',
		'CATALOG_GROUP_' . $_SESSION['GEO_PRICES']['PRICE_ID'],
		'CATALOG_GROUP_' . $_SESSION['GEO_PRICES']['PRICE_ID_ACTION'],
		'PROPERTY_RAZMER',
		'PROPERTY_CML2_LINK.PROPERTY_OFFERS_AMOUNT',
	);
	$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
	while ($arElement = $rsElements->GetNext()) {
		$arResult['TOVARS'][$arElement['ID']] = $arElement;
		$articul = trim($arElement['PROPERTY_CML2_LINK_PROPERTY_CML2_ARTICLE_VALUE']);
		$arResult['TOVARS'][$arElement['ID']]['ARTICUL'] = $articul;
		$arResult['TOVARS'][$arElement['ID']]['CATALOG_PHOTO'] = \Citfact\Paolo::getProductImage($articul, $imageConfig);

		$arResult['TOVARS'][$arElement['ID']]['OFFERS_AMOUNT'] = $arElement['PROPERTY_CML2_LINK_PROPERTY_OFFERS_AMOUNT_VALUE'];
	}


	// Достаем НЕ ТП
	$arFilter = array('IBLOCK_ID' => IBLOCK_CATALOG, 'ID' => $arIds, 'ACTIVE' => 'Y');
	$arSelectFields = array("ID", "ACTIVE", "NAME",
		'PROPERTY_CML2_ARTICLE',
		'DETAIL_PAGE_URL',
		'CATALOG_GROUP_' . $_SESSION['GEO_PRICES']['PRICE_ID'],
		'CATALOG_GROUP_' . $_SESSION['GEO_PRICES']['PRICE_ID_ACTION'],
		'PROPERTY_OFFERS_AMOUNT'
	);
	$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
	while ($arElement = $rsElements->GetNext()) {
		//\Citfact\Tools::pre($arElement);
		$arResult['TOVARS'][$arElement['ID']] = $arElement;
		$articul = trim($arElement['PROPERTY_CML2_ARTICLE_VALUE']);
		$arResult['TOVARS'][$arElement['ID']]['ARTICUL'] = $articul;
		$arResult['TOVARS'][$arElement['ID']]['CATALOG_PHOTO'] = \Citfact\Paolo::getProductImage($articul, $imageConfig);

		$arResult['TOVARS'][$arElement['ID']]['OFFERS_AMOUNT'] = $arElement['PROPERTY_OFFERS_AMOUNT_VALUE'];
	}


	// Смотрим наличие на складе «Интернет-магазин»
	$arItemsAmount = array();
	$rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' => $arIds, 'STORE_ID' => 5), false, false, array());
	while ($arStore = $rsStore->Fetch()) {
		if ($arStore['AMOUNT'] > 0) {
			$arResult['TOVARS'][$arStore['PRODUCT_ID']]['STORE_AMOUNT'] = $arStore['AMOUNT'];
		}
	}
}