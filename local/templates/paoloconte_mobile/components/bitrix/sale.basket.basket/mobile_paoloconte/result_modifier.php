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
    $arIdsOffers[$key] = $arItem['PRODUCT_ID'];
}

$arCatalogInfo = array();
$res = CIBlockElement::GetList(array(), array('ID' => $arIdsOffers), false, array('nTopCount' => count($arIdsOffers)),  array("ID", "IBLOCK_ID", "NAME", "PROPERTY_CML2_ARTICLE", "PROPERTY_CML2_LINK"));
while ($ob = $res->GetNext())
{
    $arCatalogInfo[$ob['ID']] = $ob;
}


foreach ($arResult["GRID"]["ROWS"] as $key => $arItem){
    $arResult["GRID"]["ROWS"][$key]['CATALOG'] = $arCatalogInfo[$arItem['PRODUCT_ID']];
    $articul = trim($arCatalogInfo[$arItem['PRODUCT_ID']]['PROPERTY_CML2_ARTICLE_VALUE']);
    $arResult["GRID"]["ROWS"][$key]['CATALOG_PHOTO'] = \Citfact\Paolo::getProductImage($articul, $imageConfig);

    if ($arCatalogInfo[$arItem['PRODUCT_ID']]['PROPERTY_CML2_LINK_VALUE'] != '') {
        $arIdsMain[] = $arCatalogInfo[$arItem['PRODUCT_ID']]['PROPERTY_CML2_LINK_VALUE'];
        $arIdsOffers[] = $arItem['PRODUCT_ID'];
    }
}
