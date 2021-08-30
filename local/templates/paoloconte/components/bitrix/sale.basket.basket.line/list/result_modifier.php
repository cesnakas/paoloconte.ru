<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');

$arIdsOffers = array();
$arCatalogInfo = array();

if (!empty($arResult["CATEGORIES"]['READY'])) {
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

    foreach ($arResult["CATEGORIES"]['READY'] as $key => $arItem){
        $arIdsOffers[$key] = $arItem['PRODUCT_ID'];
    }
}

/*ПОДГОТОВКА КАРТИНОК*/
$imageConfig = array(
	'TYPE'=>'ONE',
	'SIZE' => array(
		'SMALL' => array('W'=>64,'H'=>86)
	)
);
if (!empty($arIdsOffers)) {
    $res = CIBlockElement::GetList(array(), array('ID' => $arIdsOffers), false, array('nTopCount' => count($arIdsOffers)),  array("ID", "IBLOCK_ID", "NAME", "PROPERTY_CML2_ARTICLE", "PROPERTY_CML2_LINK"));
    while ($ob = $res->GetNext())
    {
        $arCatalogInfo[$ob['ID']] = $ob;
    }
}

$arResult['NUM_READY'] = 0;
$arResult['NUM_DELAY'] = 0;
foreach ($arResult["CATEGORIES"] as $category => $items) {
	if (empty($items))
		continue;

	foreach ($items as $key => $arItem) {
		if ($category == 'READY')
			$arResult['NUM_READY']++;
		if ($category == 'DELAY')
			$arResult['NUM_DELAY']++;

        $articul = trim($arCatalogInfo[$arItem['PRODUCT_ID']]['PROPERTY_CML2_ARTICLE_VALUE']);
        if (!empty($articul)) {
            $arResult["CATEGORIES"][$category][$key]['CATALOG_PHOTO'] = \Citfact\Paolo::getProductImage($articul, $imageConfig);
        }
    }
}