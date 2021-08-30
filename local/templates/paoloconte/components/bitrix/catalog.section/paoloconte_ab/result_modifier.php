<?
use Bitrix\Main\Type\Collection;
use Bitrix\Currency\CurrencyTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
$arDefaultParams = array(
	'TEMPLATE_THEME' => 'blue',
	'PRODUCT_DISPLAY_MODE' => 'N',
	'ADD_PICT_PROP' => '-',
	'LABEL_PROP' => '-',
	'OFFER_ADD_PICT_PROP' => '-',
	'OFFER_TREE_PROPS' => array('-'),
	'PRODUCT_SUBSCRIPTION' => 'N',
	'SHOW_DISCOUNT_PERCENT' => 'N',
	'SHOW_OLD_PRICE' => 'N',
	'ADD_TO_BASKET_ACTION' => 'ADD',
	'SHOW_CLOSE_POPUP' => 'N',
	'MESS_BTN_BUY' => '',
	'MESS_BTN_ADD_TO_BASKET' => '',
	'MESS_BTN_SUBSCRIBE' => '',
	'MESS_BTN_DETAIL' => '',
	'MESS_NOT_AVAILABLE' => '',
	'MESS_BTN_COMPARE' => ''
);

$arParams = array_merge($arDefaultParams, $arParams);

if (!isset($arParams['LINE_ELEMENT_COUNT']))
	$arParams['LINE_ELEMENT_COUNT'] = 3;
$arParams['LINE_ELEMENT_COUNT'] = intval($arParams['LINE_ELEMENT_COUNT']);
if (2 > $arParams['LINE_ELEMENT_COUNT'] || 5 < $arParams['LINE_ELEMENT_COUNT'])
	$arParams['LINE_ELEMENT_COUNT'] = 3;

$arParams['TEMPLATE_THEME'] = (string)($arParams['TEMPLATE_THEME']);
if ('' != $arParams['TEMPLATE_THEME'])
{
	$arParams['TEMPLATE_THEME'] = preg_replace('/[^a-zA-Z0-9_\-\(\)\!]/', '', $arParams['TEMPLATE_THEME']);
	if ('site' == $arParams['TEMPLATE_THEME'])
	{
		$arParams['TEMPLATE_THEME'] = COption::GetOptionString('main', 'wizard_eshop_adapt_theme_id', 'blue', SITE_ID);
	}
	if ('' != $arParams['TEMPLATE_THEME'])
	{
		if (!is_file($_SERVER['DOCUMENT_ROOT'].$this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css'))
			$arParams['TEMPLATE_THEME'] = '';
	}
}
if ('' == $arParams['TEMPLATE_THEME'])
	$arParams['TEMPLATE_THEME'] = 'blue';

if ('Y' != $arParams['PRODUCT_DISPLAY_MODE'])
	$arParams['PRODUCT_DISPLAY_MODE'] = 'N';

$arParams['ADD_PICT_PROP'] = trim($arParams['ADD_PICT_PROP']);
if ('-' == $arParams['ADD_PICT_PROP'])
	$arParams['ADD_PICT_PROP'] = '';
$arParams['LABEL_PROP'] = trim($arParams['LABEL_PROP']);
if ('-' == $arParams['LABEL_PROP'])
	$arParams['LABEL_PROP'] = '';
$arParams['OFFER_ADD_PICT_PROP'] = trim($arParams['OFFER_ADD_PICT_PROP']);
if ('-' == $arParams['OFFER_ADD_PICT_PROP'])
	$arParams['OFFER_ADD_PICT_PROP'] = '';
if ('Y' == $arParams['PRODUCT_DISPLAY_MODE'])
{
	if (!is_array($arParams['OFFER_TREE_PROPS']))
		$arParams['OFFER_TREE_PROPS'] = array($arParams['OFFER_TREE_PROPS']);
	foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value)
	{
		$value = (string)$value;
		if ('' == $value || '-' == $value)
			unset($arParams['OFFER_TREE_PROPS'][$key]);
	}
	if (empty($arParams['OFFER_TREE_PROPS']) && isset($arParams['OFFERS_CART_PROPERTIES']) && is_array($arParams['OFFERS_CART_PROPERTIES']))
	{
		$arParams['OFFER_TREE_PROPS'] = $arParams['OFFERS_CART_PROPERTIES'];
		foreach ($arParams['OFFER_TREE_PROPS'] as $key => $value)
		{
			$value = (string)$value;
			if ('' == $value || '-' == $value)
				unset($arParams['OFFER_TREE_PROPS'][$key]);
		}
	}
}
else
{
	$arParams['OFFER_TREE_PROPS'] = array();
}
if ('Y' != $arParams['PRODUCT_SUBSCRIPTION'])
	$arParams['PRODUCT_SUBSCRIPTION'] = 'N';
if ('Y' != $arParams['SHOW_DISCOUNT_PERCENT'])
	$arParams['SHOW_DISCOUNT_PERCENT'] = 'N';
if ('Y' != $arParams['SHOW_OLD_PRICE'])
	$arParams['SHOW_OLD_PRICE'] = 'N';
if ($arParams['ADD_TO_BASKET_ACTION'] != 'BUY')
	$arParams['ADD_TO_BASKET_ACTION'] = 'ADD';
if ($arParams['SHOW_CLOSE_POPUP'] != 'Y')
	$arParams['SHOW_CLOSE_POPUP'] = 'N';
$arParams['MESS_BTN_BUY'] = trim($arParams['MESS_BTN_BUY']);
$arParams['MESS_BTN_ADD_TO_BASKET'] = trim($arParams['MESS_BTN_ADD_TO_BASKET']);
$arParams['MESS_BTN_SUBSCRIBE'] = trim($arParams['MESS_BTN_SUBSCRIBE']);
$arParams['MESS_BTN_DETAIL'] = trim($arParams['MESS_BTN_DETAIL']);
$arParams['MESS_NOT_AVAILABLE'] = trim($arParams['MESS_NOT_AVAILABLE']);
$arParams['MESS_BTN_COMPARE'] = trim($arParams['MESS_BTN_COMPARE']);

if (!empty($arResult['ITEMS']))
{
	$arEmptyPreview = false;
	$strEmptyPreview = $this->GetFolder().'/images/no_photo.png';
	$arResult['NOPHOTO'] = $strEmptyPreview;
	if (file_exists($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview))
	{
		$arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'].$strEmptyPreview);
		if (!empty($arSizes))
		{
			$arEmptyPreview = array(
				'SRC' => $strEmptyPreview,
				'WIDTH' => intval($arSizes[0]),
				'HEIGHT' => intval($arSizes[1])
			);
		}
		unset($arSizes);
	}
	unset($strEmptyPreview);

	$arSKUPropList = array();
	$arSKUPropIDs = array();
	$arSKUPropKeys = array();
	$boolSKU = false;
	$strBaseCurrency = '';
	$boolConvert = isset($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);

	if ($arResult['MODULES']['catalog'])
	{
		if (!$boolConvert)
			$strBaseCurrency = CCurrency::GetBaseCurrency();

		$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
		$boolSKU = !empty($arSKU) && is_array($arSKU);
		if ($boolSKU && !empty($arParams['OFFER_TREE_PROPS']) && 'Y' == $arParams['PRODUCT_DISPLAY_MODE'])
		{
			$arSKUPropList = CIBlockPriceTools::getTreeProperties(
				$arSKU,
				$arParams['OFFER_TREE_PROPS'],
				array(
					'PICT' => $arEmptyPreview,
					'NAME' => '-'
				)
			);

			$arNeedValues = array();
			CIBlockPriceTools::getTreePropertyValues($arSKUPropList, $arNeedValues);
			$arSKUPropIDs = array_keys($arSKUPropList);
			if (empty($arSKUPropIDs))
				$arParams['PRODUCT_DISPLAY_MODE'] = 'N';
			else
				$arSKUPropKeys = array_fill_keys($arSKUPropIDs, false);
		}
	}

	$arNewItemsList = array();
	foreach ($arResult['ITEMS'] as $key => $arItem)
	{
		$arItem['CHECK_QUANTITY'] = false;
		if (!isset($arItem['CATALOG_MEASURE_RATIO']))
			$arItem['CATALOG_MEASURE_RATIO'] = 1;
		if (!isset($arItem['CATALOG_QUANTITY']))
			$arItem['CATALOG_QUANTITY'] = 0;
		$arItem['CATALOG_QUANTITY'] = (
			0 < $arItem['CATALOG_QUANTITY'] && is_float($arItem['CATALOG_MEASURE_RATIO'])
			? floatval($arItem['CATALOG_QUANTITY'])
			: intval($arItem['CATALOG_QUANTITY'])
		);
		$arItem['CATALOG'] = false;
		if (!isset($arItem['CATALOG_SUBSCRIPTION']) || 'Y' != $arItem['CATALOG_SUBSCRIPTION'])
			$arItem['CATALOG_SUBSCRIPTION'] = 'N';

		CIBlockPriceTools::getLabel($arItem, $arParams['LABEL_PROP']);

		$productPictures = CIBlockPriceTools::getDoublePicturesForItem($arItem, $arParams['ADD_PICT_PROP']);
		if (empty($productPictures['PICT']))
			$productPictures['PICT'] = $arEmptyPreview;
		if (empty($productPictures['SECOND_PICT']))
			$productPictures['SECOND_PICT'] = $productPictures['PICT'];

		$arItem['PREVIEW_PICTURE'] = $productPictures['PICT'];
		$arItem['PREVIEW_PICTURE_SECOND'] = $productPictures['SECOND_PICT'];
		$arItem['SECOND_PICT'] = true;
		$arItem['PRODUCT_PREVIEW'] = $productPictures['PICT'];
		$arItem['PRODUCT_PREVIEW_SECOND'] = $productPictures['SECOND_PICT'];

		if ($arResult['MODULES']['catalog'])
		{
			$arItem['CATALOG'] = true;
			if (!isset($arItem['CATALOG_TYPE']))
				$arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
			if (
				(CCatalogProduct::TYPE_PRODUCT == $arItem['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arItem['CATALOG_TYPE'])
				&& !empty($arItem['OFFERS'])
			)
			{
				$arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
			}
			switch ($arItem['CATALOG_TYPE'])
			{
				case CCatalogProduct::TYPE_SET:
					$arItem['OFFERS'] = array();
					$arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
					break;
				case CCatalogProduct::TYPE_SKU:
					break;
				case CCatalogProduct::TYPE_PRODUCT:
				default:
					$arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
					break;
			}
		}
		else
		{
			$arItem['CATALOG_TYPE'] = 0;
			$arItem['OFFERS'] = array();
		}

		if ($arItem['CATALOG'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
		{
			if ('Y' == $arParams['PRODUCT_DISPLAY_MODE'])
			{
				$arMatrixFields = $arSKUPropKeys;
				$arMatrix = array();

				$arNewOffers = array();
				$boolSKUDisplayProperties = false;
				$arItem['OFFERS_PROP'] = false;

				$arDouble = array();
				foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
				{
					$arOffer['ID'] = intval($arOffer['ID']);
					if (isset($arDouble[$arOffer['ID']]))
						continue;
					$arRow = array();
					foreach ($arSKUPropIDs as $propkey => $strOneCode)
					{
						$arCell = array(
							'VALUE' => 0,
							'SORT' => PHP_INT_MAX,
							'NA' => true
						);
						if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
						{
							$arMatrixFields[$strOneCode] = true;
							$arCell['NA'] = false;
							if ('directory' == $arSKUPropList[$strOneCode]['USER_TYPE'])
							{
								$intValue = $arSKUPropList[$strOneCode]['XML_MAP'][$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']];
								$arCell['VALUE'] = $intValue;
							}
							elseif ('L' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
							{
								$arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID']);
							}
							elseif ('E' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
							{
								$arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']);
							}
							$arCell['SORT'] = $arSKUPropList[$strOneCode]['VALUES'][$arCell['VALUE']]['SORT'];
						}
						$arRow[$strOneCode] = $arCell;
					}
					$arMatrix[$keyOffer] = $arRow;

					CIBlockPriceTools::clearProperties($arOffer['DISPLAY_PROPERTIES'], $arParams['OFFER_TREE_PROPS']);

					CIBlockPriceTools::setRatioMinPrice($arOffer, false);

					$offerPictures = CIBlockPriceTools::getDoublePicturesForItem($arOffer, $arParams['OFFER_ADD_PICT_PROP']);
					$arOffer['OWNER_PICT'] = empty($offerPictures['PICT']);
					$arOffer['PREVIEW_PICTURE'] = false;
					$arOffer['PREVIEW_PICTURE_SECOND'] = false;
					$arOffer['SECOND_PICT'] = true;
					if (!$arOffer['OWNER_PICT'])
					{
						if (empty($offerPictures['SECOND_PICT']))
							$offerPictures['SECOND_PICT'] = $offerPictures['PICT'];
						$arOffer['PREVIEW_PICTURE'] = $offerPictures['PICT'];
						$arOffer['PREVIEW_PICTURE_SECOND'] = $offerPictures['SECOND_PICT'];
					}
					if ('' != $arParams['OFFER_ADD_PICT_PROP'] && isset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]))
						unset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]);

					$arDouble[$arOffer['ID']] = true;
					$arNewOffers[$keyOffer] = $arOffer;
				}
				$arItem['OFFERS'] = $arNewOffers;

				$arUsedFields = array();
				$arSortFields = array();

				foreach ($arSKUPropIDs as $propkey => $strOneCode)
				{
					$boolExist = $arMatrixFields[$strOneCode];
					foreach ($arMatrix as $keyOffer => $arRow)
					{
						if ($boolExist)
						{
							if (!isset($arItem['OFFERS'][$keyOffer]['TREE']))
								$arItem['OFFERS'][$keyOffer]['TREE'] = array();
							$arItem['OFFERS'][$keyOffer]['TREE']['PROP_'.$arSKUPropList[$strOneCode]['ID']] = $arMatrix[$keyOffer][$strOneCode]['VALUE'];
							$arItem['OFFERS'][$keyOffer]['SKU_SORT_'.$strOneCode] = $arMatrix[$keyOffer][$strOneCode]['SORT'];
							$arUsedFields[$strOneCode] = true;
							$arSortFields['SKU_SORT_'.$strOneCode] = SORT_NUMERIC;
						}
						else
						{
							unset($arMatrix[$keyOffer][$strOneCode]);
						}
					}
				}
				$arItem['OFFERS_PROP'] = $arUsedFields;
				$arItem['OFFERS_PROP_CODES'] = (!empty($arUsedFields) ? base64_encode(serialize(array_keys($arUsedFields))) : '');

				Collection::sortByColumn($arItem['OFFERS'], $arSortFields);

				$arMatrix = array();
				$intSelected = -1;
				$arItem['MIN_PRICE'] = false;
				$arItem['MIN_BASIS_PRICE'] = false;
				foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
				{
					if (empty($arItem['MIN_PRICE']) && $arOffer['CAN_BUY'])
					{
                        $minPriceof = null;
                        foreach ($arOffer['PRICES'] as $arPriceValue) {
                            if (
                                $arPriceValue['DISCOUNT_VALUE'] > 0
                                && (
                                    $arPriceValue['DISCOUNT_VALUE'] < $minPriceof['DISCOUNT_VALUE']
                                    || $minPriceof === null
                                )
                            ) {
                                $minPriceof = $arPriceValue;
                            }
                        }
						$intSelected = $keyOffer;
                        $arItem['MIN_PRICE'] = $minPriceof;
                        //						$arItem['MIN_PRICE'] = (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $minPriceof);
                        $arItem['MIN_BASIS_PRICE'] = $arOffer['MIN_PRICE'];
					}
					$arSKUProps = false;
					if (!empty($arOffer['DISPLAY_PROPERTIES']))
					{
						$boolSKUDisplayProperties = true;
						$arSKUProps = array();
						foreach ($arOffer['DISPLAY_PROPERTIES'] as &$arOneProp)
						{
							if ('F' == $arOneProp['PROPERTY_TYPE'])
								continue;
							$arSKUProps[] = array(
								'NAME' => $arOneProp['NAME'],
								'VALUE' => $arOneProp['DISPLAY_VALUE']
							);
						}
						unset($arOneProp);
					}

					$arOneRow = array(
						'ID' => $arOffer['ID'],
						'NAME' => $arOffer['~NAME'],
						'TREE' => $arOffer['TREE'],
						'DISPLAY_PROPERTIES' => $arSKUProps,
						'PRICE' => (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']),
						'BASIS_PRICE' => $arOffer['MIN_PRICE'],
						'SECOND_PICT' => $arOffer['SECOND_PICT'],
						'OWNER_PICT' => $arOffer['OWNER_PICT'],
						'PREVIEW_PICTURE' => $arOffer['PREVIEW_PICTURE'],
						'PREVIEW_PICTURE_SECOND' => $arOffer['PREVIEW_PICTURE_SECOND'],
						'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
						'MAX_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
						'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
						'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
						'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
						'CAN_BUY' => $arOffer['CAN_BUY'],
					);
					$arMatrix[$keyOffer] = $arOneRow;
				}
				if (-1 == $intSelected)
					$intSelected = 0;
				if (!$arMatrix[$intSelected]['OWNER_PICT'])
				{
					$arItem['PREVIEW_PICTURE'] = $arMatrix[$intSelected]['PREVIEW_PICTURE'];
					$arItem['PREVIEW_PICTURE_SECOND'] = $arMatrix[$intSelected]['PREVIEW_PICTURE_SECOND'];
				}
				$arItem['JS_OFFERS'] = $arMatrix;
				$arItem['OFFERS_SELECTED'] = $intSelected;
				$arItem['OFFERS_PROPS_DISPLAY'] = $boolSKUDisplayProperties;
			}
			else
			{
				$arItem['MIN_PRICE'] = CIBlockPriceTools::getMinPriceFromOffers(
					$arItem['OFFERS'],
					$boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency
				);
			}
		}

		if (
			$arResult['MODULES']['catalog']
			&& $arItem['CATALOG']
			&&
				($arItem['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT
				|| $arItem['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET)
		)
		{
			CIBlockPriceTools::setRatioMinPrice($arItem, false);
			$arItem['MIN_BASIS_PRICE'] = $arItem['MIN_PRICE'];
		}

		if (!empty($arItem['DISPLAY_PROPERTIES']))
		{
			foreach ($arItem['DISPLAY_PROPERTIES'] as $propKey => $arDispProp)
			{
				if ('F' == $arDispProp['PROPERTY_TYPE'])
					unset($arItem['DISPLAY_PROPERTIES'][$propKey]);
			}
		}
		$arItem['LAST_ELEMENT'] = 'N';
		$arNewItemsList[$key] = $arItem;
	}
	$arNewItemsList[$key]['LAST_ELEMENT'] = 'Y';
	$arResult['ITEMS'] = $arNewItemsList;
	$arResult['SKU_PROPS'] = $arSKUPropList;
	$arResult['DEFAULT_PICTURE'] = $arEmptyPreview;

	$arResult['CURRENCIES'] = array();
	if ($arResult['MODULES']['currency'])
	{
		if ($boolConvert)
		{
			$currencyFormat = CCurrencyLang::GetFormatDescription($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);
			$arResult['CURRENCIES'] = array(
				array(
					'CURRENCY' => $arResult['CONVERT_CURRENCY']['CURRENCY_ID'],
					'FORMAT' => array(
						'FORMAT_STRING' => $currencyFormat['FORMAT_STRING'],
						'DEC_POINT' => $currencyFormat['DEC_POINT'],
						'THOUSANDS_SEP' => $currencyFormat['THOUSANDS_SEP'],
						'DECIMALS' => $currencyFormat['DECIMALS'],
						'THOUSANDS_VARIANT' => $currencyFormat['THOUSANDS_VARIANT'],
						'HIDE_ZERO' => $currencyFormat['HIDE_ZERO']
					)
				)
			);
			unset($currencyFormat);
		}
		else
		{
			$currencyIterator = CurrencyTable::getList(array(
				'select' => array('CURRENCY')
			));
			while ($currency = $currencyIterator->fetch())
			{
				$currencyFormat = CCurrencyLang::GetFormatDescription($currency['CURRENCY']);
				$arResult['CURRENCIES'][] = array(
					'CURRENCY' => $currency['CURRENCY'],
					'FORMAT' => array(
						'FORMAT_STRING' => $currencyFormat['FORMAT_STRING'],
						'DEC_POINT' => $currencyFormat['DEC_POINT'],
						'THOUSANDS_SEP' => $currencyFormat['THOUSANDS_SEP'],
						'DECIMALS' => $currencyFormat['DECIMALS'],
						'THOUSANDS_VARIANT' => $currencyFormat['THOUSANDS_VARIANT'],
						'HIDE_ZERO' => $currencyFormat['HIDE_ZERO']
					)
				);
			}
			unset($currencyFormat, $currency, $currencyIterator);
		}
	}


	/*************CUSTOMIZATIONS*************/
	/*КОД ЦЕНЫ*/
	$arResult['PRICE_CODE'] = 'CATALOG_PRICE_'.$arResult['PRICES_ALLOW'][1];


	/*СОРТИРОВКА КАТАЛОГА*/
	$delParam = array('by', 'order', 'SECTION_PATH');
	$arResult['SORT'] = array(
		'PRICE' => array(
			'ACTIVE' => '',
			//'URL' => $APPLICATION->GetCurPageParam("by=".$arResult['PRICE_CODE']."&amp;order=asc", $delParam),
			'URL' => $APPLICATION->GetCurPageParam("by=PRICE&amp;order=asc", $delParam),
		),
		'DISCOUNT' => array(
			'ACTIVE' => '',
			'URL' => $APPLICATION->GetCurPageParam("by=DISCOUNT&amp;order=asc", $delParam),
		),
		'RATE' => array(
			'ACTIVE' => '',
			'URL' => $APPLICATION->GetCurPageParam("by=PROPERTY_rating&amp;order=desc", $delParam),
		),
		'POPULAR' => array(
			'ACTIVE' => '',
			'URL' => $APPLICATION->GetCurPageParam("by=PROPERTY_POPULAR&amp;order=desc", $delParam),
		),
	);

	if(!empty($_REQUEST["by"]) && !empty($_REQUEST["order"])){
		switch ($_REQUEST["by"]){
			//case $arResult['PRICE_CODE']:
			case 'PRICE':
				if($_REQUEST["order"] == "asc") {
					//$arResult['SORT']['PRICE']['URL'] = $APPLICATION->GetCurPageParam("by=".$arResult['PRICE_CODE']."&amp;order=desc", $delParam);
					$arResult['SORT']['PRICE']['URL'] = $APPLICATION->GetCurPageParam("by=PRICE&amp;order=desc", $delParam);
					$arResult['SORT']['PRICE']['ACTIVE'] = "active";
				}
				elseif($_REQUEST["order"] == "desc") {
					//$arResult['SORT']['PRICE']['URL'] = $APPLICATION->GetCurPageParam("by=".$arResult['PRICE_CODE']."&amp;order=asc", $delParam);
					$arResult['SORT']['PRICE']['URL'] = $APPLICATION->GetCurPageParam("by=PRICE&amp;order=asc", $delParam);
					$arResult['SORT']['PRICE']['ACTIVE'] = "active";
				}
				break;
			case 'DISCOUNT':
				if($_REQUEST["order"] == "asc") {
					$arResult['SORT']['DISCOUNT']['URL'] = $APPLICATION->GetCurPageParam("by=DISCOUNT&amp;order=desc", $delParam);
					$arResult['SORT']['DISCOUNT']['ACTIVE'] = "active";
				}
				elseif($_REQUEST["order"] == "desc") {
					$arResult['SORT']['DISCOUNT']['URL'] = $APPLICATION->GetCurPageParam("by=DISCOUNT&amp;order=asc", $delParam);
					$arResult['SORT']['DISCOUNT']['ACTIVE'] = "";
				}
				break;
			case 'PROPERTY_rating':
				if($_REQUEST["order"] == "asc") {
					$arResult['SORT']['RATE']['URL'] = $APPLICATION->GetCurPageParam("by=PROPERTY_rating&amp;order=desc", $delParam);
					$arResult['SORT']['RATE']['ACTIVE'] = "active";
				}
				elseif($_REQUEST["order"] == "desc") {
					$arResult['SORT']['RATE']['URL'] = $APPLICATION->GetCurPageParam("by=PROPERTY_rating&amp;order=asc", $delParam);
					$arResult['SORT']['RATE']['ACTIVE'] = "active";
				}
				break;
			case 'PROPERTY_POPULAR':
				if($_REQUEST["by"] == "PROPERTY_POPULAR" && $_REQUEST["order"] == "asc") {
					$arResult['SORT']['POPULAR']['URL'] = $APPLICATION->GetCurPageParam("by=PROPERTY_POPULAR&amp;order=desc", $delParam);
					$arResult['SORT']['POPULAR']['ACTIVE'] = "active";
				}
				elseif($_REQUEST["by"] == "PROPERTY_POPULAR" && $_REQUEST["order"] == "desc") {
					$arResult['SORT']['POPULAR']['URL'] = $APPLICATION->GetCurPageParam("by=PROPERTY_POPULAR&amp;order=asc", $delParam);
					$arResult['SORT']['POPULAR']['ACTIVE'] = "active";
				}
				break;
		}
	}

	// Корректировка сортировки по цене: изначально сортируем по свойству «Минимальная цена (Москва)»
	// Чтобы в других городах сортировалось корректно, прописываем каждому товару минимальную цену и перетасовываем товары по ней
/*	if ($_REQUEST["by"] == 'PRICE') {
		foreach($arResult['ITEMS'] as $key => $arItem){
			//\Citfact\Tools::pre($arItem['CATALOG_PRICE_' . $_SESSION['GEO_PRICES']['PRICE_ID']]);
			//\Citfact\Tools::pre($arItem['CATALOG_PRICE_' . $_SESSION['GEO_PRICES']['PRICE_ID_ACTION']]);
			$price = $arItem['CATALOG_PRICE_' . $_SESSION['GEO_PRICES']['PRICE_ID']];
			$price_action = $arItem['CATALOG_PRICE_' . $_SESSION['GEO_PRICES']['PRICE_ID_ACTION']];
			if ($price_action < $price){
				$arResult['ITEMS'][$key]['MINIMAL_PRICE'] = $price_action;
			}
			else{
				$arResult['ITEMS'][$key]['MINIMAL_PRICE'] = $price;
			}
		}

		function cmp_catalog_prices($a, $b){
			if ($a['MINIMAL_PRICE'] == $b['MINIMAL_PRICE']) { return 0; }
			if ($_REQUEST["order"] == 'asc')
				return ($a['MINIMAL_PRICE'] < $b['MINIMAL_PRICE']) ? -1 : 1;
			if ($_REQUEST["order"] == 'desc')
				return ($a['MINIMAL_PRICE'] > $b['MINIMAL_PRICE']) ? -1 : 1;
		}
		uasort($arResult['ITEMS'], "cmp_catalog_prices");
	}
*/

	/*КОЛИЧЕСТВО ОТОБРАЖАЕМЫХ ЭЛЕМЕНТОВ*/
	$delParam = array('count', 'SECTION_PATH');
	$elementsCounter = array('15','30','60');
	foreach ($elementsCounter as $value) {
		$arResult['SHOW_ELEMENTS'][$value]['URL'] = $APPLICATION->GetCurPageParam("count=".$value, $delParam);
		$arResult['SHOW_ELEMENTS'][$value]['ACTIVE'] = (!empty($_REQUEST["count"]) && $_REQUEST["count"] == $value || (empty($_REQUEST["count"]) && $value == '30') )? 'selected' : '';
	}


	/*SEO-ТЕКСТ ДЛЯ РАЗДЕЛОВ КАТАЛОГА*/
	$arFilter = Array('IBLOCK_ID'=>$arParams['IBLOCK_ID'], 'CODE'=>$arParams['SECTION_CODE']);
	$db_list = CIBlockSection::GetList(Array(), $arFilter, false, array('ID', 'IBLOCK_ID', 'DESCRIPTION'));
	while($arRes = $db_list->GetNext()) {
		if ($arRes['DESCRIPTION'] != ''){
			$arResult['SECTION_SEO_TEXT'] = $arRes['DESCRIPTION'];
		}
	}


	/*ПОДГОТОВКА КАРТИНОК*/
	$imageConfig = array(
		'TYPE'=>'ONE',
		'SIZE' => array(
			'SMALL' => array('W'=>300,'H'=>400)
		)
	);
	foreach ($arResult['ITEMS'] as $key => $arItem){
		$articul = trim($arItem['PROPERTIES']['CML2_ARTICLE']['VALUE']);
		$arResult['ITEMS'][$key]['CATALOG_PHOTO'] = Citfact\Paolo::getProductImage($articul, $imageConfig);
	}


	/*РЕЙТИНГ*/
	foreach ($arResult['ITEMS'] as $key => $arItem){
		$arResult['ITEMS'][$key]['RATING'] = !empty($arItem['PROPERTIES']['rating']['VALUE'])? round($arItem['PROPERTIES']['rating']['VALUE']) : 0;
	}


	/*ЦЕНЫ ТОВАРА*/
	$price_id = $_SESSION['GEO_PRICES']['PRICE_ID'];
	$price_name = $_SESSION['GEO_PRICES']['FULL'][$price_id]['NAME'];

	foreach ($arResult['ITEMS'] as &$arItem) {
		$min_price = (defined('PHP_INT_MAX')) ? PHP_INT_MAX : 9999999;
		$min_price_print = '';

		if (!empty($arItem['OFFERS'])) {
			foreach ($arItem['OFFERS'] as $key => $arOffer) {
				if ($arItem['OFFERS'][$key]['PRICES'][$price_name]['DISCOUNT_VALUE'] > 0
					&& $arItem['OFFERS'][$key]['PRICES'][$price_name]['DISCOUNT_VALUE'] < $min_price
				) {
					$min_price = $arItem['OFFERS'][$key]['PRICES'][$price_name]['DISCOUNT_VALUE'];
					$min_price_print = $arItem['OFFERS'][$key]['PRICES'][$price_name]['PRINT_DISCOUNT_VALUE'];
				}
			}
		} else {
			$curPrice = $arItem['PRICES'][$price_name]['DISCOUNT_VALUE_VAT'] != ''? $arItem['PRICES'][$price_name]['DISCOUNT_VALUE_VAT'] : $arItem['PRICES'][$price_name]['DISCOUNT_VALUE'];
			$curPricePrint = $arItem['PRICES'][$price_name]['PRINT_DISCOUNT_VALUE_VAT'] != ''? $arItem['PRICES'][$price_name]['PRINT_DISCOUNT_VALUE_VAT'] : $arItem['PRICES'][$price_name]['PRINT_DISCOUNT_VALUE'];
			if ($curPrice > 0
				&& $curPrice < $min_price
			) {
				$min_price = $curPrice;
				$min_price_print = $curPricePrint;
			}
		}

		$curPriceMin = $arItem['MIN_PRICE']['DISCOUNT_VALUE_VAT'] != ''? $arItem['MIN_PRICE']['DISCOUNT_VALUE_VAT'] : $arItem['MIN_PRICE']['DISCOUNT_VALUE'];
		$oldPrice = ('Y' == $arParams['SHOW_OLD_PRICE'] && $curPriceMin < $min_price) ? $min_price : '';
		$newPrice = $curPriceMin;

		$arItem['OLD_PRICE'] = number_format($oldPrice, 0, ',', ' ');
		$arItem['NEW_PRICE'] = number_format($newPrice, 0, ',', ' ');

		// Процент скидки
		$sale_percent = 0;
		if ($oldPrice > 0 && $newPrice > 0){
			$sale_percent = ($oldPrice - $newPrice) / $oldPrice * 100;
		}
		$arItem['SALE_PERCENT'] = round($sale_percent);
	}
	unset ($arItem);



	// Смотрим наличие торговых предложений в складе «Интернет-магазин»
	$arResult['OFFERS_AMOUNT'] = array();
	foreach ($arResult['ITEMS'] as $key => $arItem) {
		if (!empty($arItem['OFFERS'])){
			$arIdsOffers = array();
			foreach ($arItem['OFFERS'] as $arOffer){
				$arIdsOffers[] = $arOffer['ID'];
				//$arAmount = array();
				//$arResult['STORES_AMOUNT'] = $arAmount;
			}

			$rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' =>$arIdsOffers, 'STORE_ID' => 5), false, false, array());
			while ($arStore = $rsStore->Fetch()){
				if ($arStore['AMOUNT'] > 0){
					$arResult['OFFERS_AMOUNT'] [$arStore['PRODUCT_ID']] = $arStore['AMOUNT'];
				}
			}

			foreach ($arItem['OFFERS'] as $key2 => $arOffer){
				if ($arResult['OFFERS_AMOUNT'][$arOffer['ID']] == 0){
					unset($arResult['ITEMS'][$key]['OFFERS'][$key2]);
				}
			}

			if (count($arResult['ITEMS'][$key]['OFFERS']) == 0){
				unset($arResult['ITEMS'][$key]);
			}
			else{
			}
		}
	}
	/*************END CUSTOMIZATIONS*************/
	
	//
    // TODO
	// BEGIN 13.05.2016 Не выводить в раздел "Распродажа", товары у которых отсутствует тип цены "Розничная Интернет Акция".
    // необходимо данный код удалить, и вынести в фильтр
	//
	$exists = false;
	foreach( $arResult['PATH'] as $item ){
		if( $item['CODE'] == 'rasprodazha' ){
			$exists = true;
		}
	}
    if ($arParams['CONVERT_CURRENCY'] == 'Y')
    {
        if (!Loader::includeModule('currency'))
        {
            $arParams['CONVERT_CURRENCY'] = 'N';
            $arParams['CURRENCY_ID'] = '';
        }
        else
        {
            $arResultModules['currency'] = true;
            $currency = Currency\CurrencyTable::getList(array(
                'select' => array('CURRENCY'),
                'filter' => array('=CURRENCY' => $arParams['CURRENCY_ID'])
            ))->fetch();
            if (!empty($currency))
            {
                $arParams['CURRENCY_ID'] = $currency['CURRENCY'];
                $arConvertParams['CURRENCY_ID'] = $currency['CURRENCY'];
            }
            else
            {
                $arParams['CONVERT_CURRENCY'] = 'N';
                $arParams['CURRENCY_ID'] = '';
            }
            unset($currency);
        }
    }
	if( $exists == true ){
		foreach( $arResult['ITEMS'] as $i => $item ){
			$price = 0;
			/*
			// TODO
			Begin Костыль №2.
			Обновили модуль sale до 17. перестала прилетать цена "Розничная Интернет Акция"
			Пока, как временное решение - достаем цены из offers
			*/
			if (empty($item['PRICES'])) {
//                $arResult['ITEMS'][$i]["PRICES"] = CIBlockPriceTools::GetItemPrices($arParams["IBLOCK_ID"], $arResult["PRICES"], $item, $arParams['PRICE_VAT_INCLUDE'], $arConvertParams);
//                $item["PRICES"] = $arResult['ITEMS'][$i]["PRICES"];
                foreach ($item['OFFERS'] as $kof => $offer) {
//                    if ($offer['ACTIVE'] == 'Y') {
                        $item["PRICES"] = $offer["PRICES"];
                        break;
//                    }
                }
                $arResult['ITEMS'][$i]['PRICES'] = $item["PRICES"];
            }
            /*
			End Костыль №2.
            */
			foreach( $item['PRICES'] as $price_item ){
				if( $price_item['PRICE_ID'] == 5 ){
					$price = intval( $price_item['VALUE'] );
					break;
				}
			}

			if( $price == 0 ){
				unset($arResult['ITEMS'][$i]);
			}
		}
	}
	//
	// END 13.05.2016 Не выводить в раздел "Распродажа", товары у которых отсутствует тип цены "Розничная Интернет Акция".
	//





}