<?
use Bitrix\Main\Type\Collection;
use Bitrix\Currency\CurrencyTable;
use Bitrix\Iblock;
use Citfact\ProductAvailabilityBuy;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
$displayPreviewTextMode = array(
	'H' => true,
	'E' => true,
	'S' => true
);
$detailPictMode = array(
	'IMG' => true,
	'POPUP' => true,
	'MAGNIFIER' => true,
	'GALLERY' => true
);

$arDefaultParams = array(
	'TEMPLATE_THEME' => 'blue',
	'ADD_PICT_PROP' => '-',
	'LABEL_PROP' => '-',
	'OFFER_ADD_PICT_PROP' => '-',
	'OFFER_TREE_PROPS' => array('-'),
	'DISPLAY_NAME' => 'Y',
	'DETAIL_PICTURE_MODE' => 'IMG',
	'ADD_DETAIL_TO_SLIDER' => 'N',
	'DISPLAY_PREVIEW_TEXT_MODE' => 'E',
	'PRODUCT_SUBSCRIPTION' => 'N',
	'SHOW_DISCOUNT_PERCENT' => 'N',
	'SHOW_OLD_PRICE' => 'N',
	'SHOW_MAX_QUANTITY' => 'N',
	'SHOW_BASIS_PRICE' => 'N',
	'ADD_TO_BASKET_ACTION' => array('BUY'),
	'SHOW_CLOSE_POPUP' => 'N',
	'MESS_BTN_BUY' => '',
	'MESS_BTN_ADD_TO_BASKET' => '',
	'MESS_BTN_SUBSCRIBE' => '',
	'MESS_BTN_COMPARE' => '',
	'MESS_NOT_AVAILABLE' => '',
	'USE_VOTE_RATING' => 'N',
	'VOTE_DISPLAY_AS_RATING' => 'rating',
	'USE_COMMENTS' => 'N',
	'BLOG_USE' => 'N',
	'BLOG_URL' => 'catalog_comments',
	'BLOG_EMAIL_NOTIFY' => 'N',
	'VK_USE' => 'N',
	'VK_API_ID' => '',
	'FB_USE' => 'N',
	'FB_APP_ID' => '',
	'BRAND_USE' => 'N',
	'BRAND_PROP_CODE' => ''
);
$arParams = array_merge($arDefaultParams, $arParams);

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

$arParams['ADD_PICT_PROP'] = trim($arParams['ADD_PICT_PROP']);
if ('-' == $arParams['ADD_PICT_PROP'])
	$arParams['ADD_PICT_PROP'] = '';
$arParams['LABEL_PROP'] = trim($arParams['LABEL_PROP']);
if ('-' == $arParams['LABEL_PROP'])
	$arParams['LABEL_PROP'] = '';
$arParams['OFFER_ADD_PICT_PROP'] = trim($arParams['OFFER_ADD_PICT_PROP']);
if ('-' == $arParams['OFFER_ADD_PICT_PROP'])
	$arParams['OFFER_ADD_PICT_PROP'] = '';
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
if ('N' != $arParams['DISPLAY_NAME'])
	$arParams['DISPLAY_NAME'] = 'Y';
if (!isset($detailPictMode[$arParams['DETAIL_PICTURE_MODE']]))
	$arParams['DETAIL_PICTURE_MODE'] = 'IMG';
if ('Y' != $arParams['ADD_DETAIL_TO_SLIDER'])
	$arParams['ADD_DETAIL_TO_SLIDER'] = 'N';
if (!isset($displayPreviewTextMode[$arParams['DISPLAY_PREVIEW_TEXT_MODE']]))
	$arParams['DISPLAY_PREVIEW_TEXT_MODE'] = 'E';
if ('Y' != $arParams['PRODUCT_SUBSCRIPTION'])
	$arParams['PRODUCT_SUBSCRIPTION'] = 'N';
if ('Y' != $arParams['SHOW_DISCOUNT_PERCENT'])
	$arParams['SHOW_DISCOUNT_PERCENT'] = 'N';
if ('Y' != $arParams['SHOW_OLD_PRICE'])
	$arParams['SHOW_OLD_PRICE'] = 'N';
if ('Y' != $arParams['SHOW_MAX_QUANTITY'])
	$arParams['SHOW_MAX_QUANTITY'] = 'N';
if ($arParams['SHOW_BASIS_PRICE'] != 'Y')
	$arParams['SHOW_BASIS_PRICE'] = 'N';
if (!is_array($arParams['ADD_TO_BASKET_ACTION']))
	$arParams['ADD_TO_BASKET_ACTION'] = array($arParams['ADD_TO_BASKET_ACTION']);
$arParams['ADD_TO_BASKET_ACTION'] = array_filter($arParams['ADD_TO_BASKET_ACTION'], 'CIBlockParameters::checkParamValues');
if (empty($arParams['ADD_TO_BASKET_ACTION']) || (!in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']) && !in_array('BUY', $arParams['ADD_TO_BASKET_ACTION'])))
	$arParams['ADD_TO_BASKET_ACTION'] = array('BUY');
if ($arParams['SHOW_CLOSE_POPUP'] != 'Y')
	$arParams['SHOW_CLOSE_POPUP'] = 'N';

$arParams['MESS_BTN_BUY'] = trim($arParams['MESS_BTN_BUY']);
$arParams['MESS_BTN_ADD_TO_BASKET'] = trim($arParams['MESS_BTN_ADD_TO_BASKET']);
$arParams['MESS_BTN_SUBSCRIBE'] = trim($arParams['MESS_BTN_SUBSCRIBE']);
$arParams['MESS_BTN_COMPARE'] = trim($arParams['MESS_BTN_COMPARE']);
$arParams['MESS_NOT_AVAILABLE'] = trim($arParams['MESS_NOT_AVAILABLE']);
if ('Y' != $arParams['USE_VOTE_RATING'])
	$arParams['USE_VOTE_RATING'] = 'N';
if ('vote_avg' != $arParams['VOTE_DISPLAY_AS_RATING'])
	$arParams['VOTE_DISPLAY_AS_RATING'] = 'rating';
if ('Y' != $arParams['USE_COMMENTS'])
	$arParams['USE_COMMENTS'] = 'N';
if ('Y' != $arParams['BLOG_USE'])
	$arParams['BLOG_USE'] = 'N';
if ('Y' != $arParams['VK_USE'])
	$arParams['VK_USE'] = 'N';
if ('Y' != $arParams['FB_USE'])
	$arParams['FB_USE'] = 'N';
if ('Y' == $arParams['USE_COMMENTS'])
{
	if ('N' == $arParams['BLOG_USE'] && 'N' == $arParams['VK_USE'] && 'N' == $arParams['FB_USE'])
		$arParams['USE_COMMENTS'] = 'N';
}
if ('Y' != $arParams['BRAND_USE'])
	$arParams['BRAND_USE'] = 'N';
if ($arParams['BRAND_PROP_CODE'] == '')
	$arParams['BRAND_PROP_CODE'] = array();
if (!is_array($arParams['BRAND_PROP_CODE']))
	$arParams['BRAND_PROP_CODE'] = array($arParams['BRAND_PROP_CODE']);

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
			'WIDTH' => (int)$arSizes[0],
			'HEIGHT' => (int)$arSizes[1]
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

	if ($boolSKU && !empty($arParams['OFFER_TREE_PROPS']))
	{
		$arSKUPropList = CIBlockPriceTools::getTreeProperties(
			$arSKU,
			$arParams['OFFER_TREE_PROPS'],
			array(
				'PICT' => $arEmptyPreview,
				'NAME' => '-'
			)
		);
		$arSKUPropIDs = array_keys($arSKUPropList);
	}
}

$arResult['CHECK_QUANTITY'] = false;
if (!isset($arResult['CATALOG_MEASURE_RATIO']))
	$arResult['CATALOG_MEASURE_RATIO'] = 1;
if (!isset($arResult['CATALOG_QUANTITY']))
	$arResult['CATALOG_QUANTITY'] = 0;
$arResult['CATALOG_QUANTITY'] = (
	0 < $arResult['CATALOG_QUANTITY'] && is_float($arResult['CATALOG_MEASURE_RATIO'])
	? (float)$arResult['CATALOG_QUANTITY']
	: (int)$arResult['CATALOG_QUANTITY']
);
$arResult['CATALOG'] = false;
if (!isset($arResult['CATALOG_SUBSCRIPTION']) || 'Y' != $arResult['CATALOG_SUBSCRIPTION'])
	$arResult['CATALOG_SUBSCRIPTION'] = 'N';

CIBlockPriceTools::getLabel($arResult, $arParams['LABEL_PROP']);

$productSlider = CIBlockPriceTools::getSliderForItem($arResult, $arParams['ADD_PICT_PROP'], 'Y' == $arParams['ADD_DETAIL_TO_SLIDER']);
if (empty($productSlider))
{
	$productSlider = array(
		0 => $arEmptyPreview
	);
}
$productSliderCount = count($productSlider);
$arResult['SHOW_SLIDER'] = true;
$arResult['MORE_PHOTO'] = $productSlider;
$arResult['MORE_PHOTO_COUNT'] = count($productSlider);

if ($arResult['MODULES']['catalog'])
{
	$arResult['CATALOG'] = true;
	if (!isset($arResult['CATALOG_TYPE']))
		$arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
	if (
		(CCatalogProduct::TYPE_PRODUCT == $arResult['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arResult['CATALOG_TYPE'])
		&& !empty($arResult['OFFERS'])
	)
	{
		$arResult['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
	}
	switch ($arResult['CATALOG_TYPE'])
	{
		case CCatalogProduct::TYPE_SET:
			$arResult['OFFERS'] = array();
			$arResult['CHECK_QUANTITY'] = ('Y' == $arResult['CATALOG_QUANTITY_TRACE'] && 'N' == $arResult['CATALOG_CAN_BUY_ZERO']);
			break;
		case CCatalogProduct::TYPE_SKU:
			break;
		case CCatalogProduct::TYPE_PRODUCT:
		default:
			$arResult['CHECK_QUANTITY'] = ('Y' == $arResult['CATALOG_QUANTITY_TRACE'] && 'N' == $arResult['CATALOG_CAN_BUY_ZERO']);
			break;
	}
}
else
{
	$arResult['CATALOG_TYPE'] = 0;
	$arResult['OFFERS'] = array();
}

if ($arResult['CATALOG'] && isset($arResult['OFFERS']) && !empty($arResult['OFFERS']))
{
	$boolSKUDisplayProps = false;

	$arResultSKUPropIDs = array();
	$arFilterProp = array();
	$arNeedValues = array();
	foreach ($arResult['OFFERS'] as &$arOffer)
	{
		foreach ($arSKUPropIDs as &$strOneCode)
		{
			if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
			{
				$arResultSKUPropIDs[$strOneCode] = true;
				if (!isset($arFilterProp[$strOneCode]))
					$arFilterProp[$strOneCode] = $arSKUPropList[$strOneCode];
			}
		}
		unset($strOneCode);
	}
	unset($arOffer);

	CIBlockPriceTools::getTreePropertyValues($arSKUPropList, $arNeedValues);
	$arSKUPropIDs = array_keys($arSKUPropList);
	$arSKUPropKeys = array_fill_keys($arSKUPropIDs, false);


	$arMatrixFields = $arSKUPropKeys;
	$arMatrix = array();

	$arNewOffers = array();

	$arIDS = array($arResult['ID']);
	$arOfferSet = array();
	$arResult['OFFER_GROUP'] = false;
	$arResult['OFFERS_PROP'] = false;

	$arDouble = array();
	foreach ($arResult['OFFERS'] as $keyOffer => $arOffer)
	{
		$arOffer['ID'] = (int)$arOffer['ID'];
		if (isset($arDouble[$arOffer['ID']]))
			continue;
		$arIDS[] = $arOffer['ID'];
		$boolSKUDisplayProperties = false;
		$arOffer['OFFER_GROUP'] = false;
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
					$arCell['VALUE'] = (int)$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID'];
				}
				elseif ('E' == $arSKUPropList[$strOneCode]['PROPERTY_TYPE'])
				{
					$arCell['VALUE'] = (int)$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE'];
				}
				$arCell['SORT'] = $arSKUPropList[$strOneCode]['VALUES'][$arCell['VALUE']]['SORT'];
			}
			$arRow[$strOneCode] = $arCell;
		}
		$arMatrix[$keyOffer] = $arRow;

		CIBlockPriceTools::setRatioMinPrice($arOffer, false);

		$arOffer['MORE_PHOTO'] = array();
		$arOffer['MORE_PHOTO_COUNT'] = 0;
		$offerSlider = CIBlockPriceTools::getSliderForItem($arOffer, $arParams['OFFER_ADD_PICT_PROP'], $arParams['ADD_DETAIL_TO_SLIDER'] == 'Y');
		if (empty($offerSlider))
		{
			$offerSlider = $productSlider;
		}
		$arOffer['MORE_PHOTO'] = $offerSlider;
		$arOffer['MORE_PHOTO_COUNT'] = count($offerSlider);

		if (CIBlockPriceTools::clearProperties($arOffer['DISPLAY_PROPERTIES'], $arParams['OFFER_TREE_PROPS']))
		{
			$boolSKUDisplayProps = true;
		}

		$arDouble[$arOffer['ID']] = true;
		$arNewOffers[$keyOffer] = $arOffer;
	}
	$arResult['OFFERS'] = $arNewOffers;
	$arResult['SHOW_OFFERS_PROPS'] = $boolSKUDisplayProps;

	$arUsedFields = array();
	$arSortFields = array();

	foreach ($arSKUPropIDs as $propkey => $strOneCode)
	{
		$boolExist = $arMatrixFields[$strOneCode];
		foreach ($arMatrix as $keyOffer => $arRow)
		{
			if ($boolExist)
			{
				if (!isset($arResult['OFFERS'][$keyOffer]['TREE']))
					$arResult['OFFERS'][$keyOffer]['TREE'] = array();
				$arResult['OFFERS'][$keyOffer]['TREE']['PROP_'.$arSKUPropList[$strOneCode]['ID']] = $arMatrix[$keyOffer][$strOneCode]['VALUE'];
				$arResult['OFFERS'][$keyOffer]['SKU_SORT_'.$strOneCode] = $arMatrix[$keyOffer][$strOneCode]['SORT'];
				$arUsedFields[$strOneCode] = true;
				$arSortFields['SKU_SORT_'.$strOneCode] = SORT_NUMERIC;
			}
			else
			{
				unset($arMatrix[$keyOffer][$strOneCode]);
			}
		}
	}
	$arResult['OFFERS_PROP'] = $arUsedFields;
	$arResult['OFFERS_PROP_CODES'] = (!empty($arUsedFields) ? base64_encode(serialize(array_keys($arUsedFields))) : '');

	Collection::sortByColumn($arResult['OFFERS'], $arSortFields);

	$offerSet = array();
	if (!empty($arIDS) && CBXFeatures::IsFeatureEnabled('CatCompleteSet'))
	{
		$offerSet = array_fill_keys($arIDS, false);
		$rsSets = CCatalogProductSet::getList(
			array(),
			array(
				'@OWNER_ID' => $arIDS,
				'=SET_ID' => 0,
				'=TYPE' => CCatalogProductSet::TYPE_GROUP
			),
			false,
			false,
			array('ID', 'OWNER_ID')
		);
		while ($arSet = $rsSets->Fetch())
		{
			$arSet['OWNER_ID'] = (int)$arSet['OWNER_ID'];
			$offerSet[$arSet['OWNER_ID']] = true;
			$arResult['OFFER_GROUP'] = true;
		}
		if ($offerSet[$arResult['ID']])
		{
			foreach ($offerSet as &$setOfferValue)
			{
				if ($setOfferValue === false)
				{
					$setOfferValue = true;
				}
			}
			unset($setOfferValue);
			unset($offerSet[$arResult['ID']]);
		}
		if ($arResult['OFFER_GROUP'])
		{
			$offerSet = array_filter($offerSet);
			$arResult['OFFER_GROUP_VALUES'] = array_keys($offerSet);
		}
	}

	$arMatrix = array();
	$intSelected = -1;
	$arResult['MIN_PRICE'] = false;
	$arResult['MIN_BASIS_PRICE'] = false;
	foreach ($arResult['OFFERS'] as $keyOffer => $arOffer)
	{
		if (empty($arResult['MIN_PRICE']) && $arOffer['CAN_BUY'])
		{
			$intSelected = $keyOffer;
			$arResult['MIN_PRICE'] = (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']);
			$arResult['MIN_BASIS_PRICE'] = $arOffer['MIN_PRICE'];
		}
		$arSKUProps = false;
		if (!empty($arOffer['DISPLAY_PROPERTIES']))
		{
			$boolSKUDisplayProps = true;
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
		if (isset($arOfferSet[$arOffer['ID']]))
		{
			$arOffer['OFFER_GROUP'] = true;
			$arResult['OFFERS'][$keyOffer]['OFFER_GROUP'] = true;
		}
		reset($arOffer['MORE_PHOTO']);
		$firstPhoto = current($arOffer['MORE_PHOTO']);
		$arOneRow = array(
			'ID' => $arOffer['ID'],
			'NAME' => $arOffer['~NAME'],
			'TREE' => $arOffer['TREE'],
			'PRICE' => (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']),
			'BASIS_PRICE' => $arOffer['MIN_PRICE'],
			'DISPLAY_PROPERTIES' => $arSKUProps,
			'PREVIEW_PICTURE' => $firstPhoto,
			'DETAIL_PICTURE' => $firstPhoto,
			'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
			'MAX_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
			'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
			'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
			'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
			'OFFER_GROUP' => (isset($offerSet[$arOffer['ID']]) && $offerSet[$arOffer['ID']]),
			'CAN_BUY' => $arOffer['CAN_BUY'],
			'SLIDER' => $arOffer['MORE_PHOTO'],
			'SLIDER_COUNT' => $arOffer['MORE_PHOTO_COUNT'],
		);
		$arMatrix[$keyOffer] = $arOneRow;
	}
	if (-1 == $intSelected)
		$intSelected = 0;
	$arResult['JS_OFFERS'] = $arMatrix;
	$arResult['OFFERS_SELECTED'] = $intSelected;

	$arResult['OFFERS_IBLOCK'] = $arSKU['IBLOCK_ID'];
}

if ($arResult['MODULES']['catalog'] && $arResult['CATALOG'])
{
	if ($arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT || $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_SET)
	{
		CIBlockPriceTools::setRatioMinPrice($arResult, false);
		$arResult['MIN_BASIS_PRICE'] = $arResult['MIN_PRICE'];
	}
	if (CBXFeatures::IsFeatureEnabled('CatCompleteSet') && $arResult['CATALOG_TYPE'] == CCatalogProduct::TYPE_PRODUCT)
	{
		$rsSets = CCatalogProductSet::getList(
			array(),
			array(
				'@OWNER_ID' => $arResult['ID'],
				'=SET_ID' => 0,
				'=TYPE' => CCatalogProductSet::TYPE_GROUP
			),
			false,
			false,
			array('ID', 'OWNER_ID')
		);
		if ($arSet = $rsSets->Fetch())
		{
			$arResult['OFFER_GROUP'] = true;
		}
	}
}

if (!empty($arResult['DISPLAY_PROPERTIES']))
{
	foreach ($arResult['DISPLAY_PROPERTIES'] as $propKey => $arDispProp)
	{
		if ('F' == $arDispProp['PROPERTY_TYPE'])
			unset($arResult['DISPLAY_PROPERTIES'][$propKey]);
	}
}

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
$arResult['PRICE_CODE'] = 'CATALOG_PRICE_'.$arResult['PRICES_ALLOW'][0];

/*СОБИРАЕМ ТИПЫ ЦЕН*/
/*foreach ($arResult['CAT_PRICES'] as $key => $price) {
	$price['NAME'] = $key;
	$arResult['ALL_PRICES'][] = $price;
}
$arResult['CUR_PRICE'] = array_shift($arResult['ALL_PRICES']);
unset($price);*/


/*ПОДГОТОВКА КАРТИНОК*/
$imageConfig = array(
	'TYPE'=>'ALL',
	'360'=>'Y',
	'PATH360' => $arResult['PROPERTIES']['SWF']['VALUE'],
	'SIZE' => array(
		'SMALL' => array('W'=>60,'H'=>80),
        'BIG' => array('W'=>500,'H'=>665),
        'BIGGEST' => array('W'=>1000,'H'=>1200),
	)
);
$articul = trim($arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']);
$arResult['CATALOG_IMG'] = Citfact\Paolo::getProductImage($articul, $imageConfig);
$this->__component->SetResultCacheKeys(array("CATALOG_IMG"));


/*РЕСАЙЗ ДЛЯ РЕПОСТОВ*/

$images = Citfact\Tools::getImageFiles($_SERVER['DOCUMENT_ROOT'] . CATALOG_IMG . $articul . CATALOG_IMG_PHOTO);
$image = $images[0];

if ($image) {
	if (!file_exists($_SERVER["DOCUMENT_ROOT"] . '/upload/resize_cache/catalog/' . $articul . '/' . 'resize_1200x500_' . $image)) {
		$in = new Imagick($_SERVER['DOCUMENT_ROOT'] . CATALOG_IMG . $articul . CATALOG_IMG_PHOTO . $image);

		$in->trimImage(0);

		$w = $in->getImageWidth();
		$h = $in->getImageHeight();

		if ($h > $w) {
			$in->thumbnailImage(0, 500);
			$w = 600 - ($in->getImageWidth() / 2);
			$h = 0;
		} else {
			$in->thumbnailImage(500, 0);
			$w = 600 - ($in->getImageWidth() / 2);
			$h = 250 - ($in->getImageHeight() / 2);
		}

		$out = new Imagick();
		$out->newImage(1200, 500, new ImagickPixel('white'));
		$out->compositeImage($in, Imagick::COMPOSITE_OVER, $w, $h);

		$out->writeImage($_SERVER["DOCUMENT_ROOT"] . '/upload/resize_cache/catalog/' . $articul . '/' . 'resize_1200x500_' . $image);
	}

	$arResult['CATALOG_IMG']['PHOTO'][0]['REPOST'] = '/upload/resize_cache/catalog/' . $articul . '/' . 'resize_1200x500_' . $image;

}

Citfact\Tools::newResize($articul, $imageConfig['SIZE']['BIG']['W'], $imageConfig['SIZE']['BIG']['H']);
Citfact\Tools::newResize($articul, $imageConfig['SIZE']['BIGGEST']['W'], $imageConfig['SIZE']['BIGGEST']['H']);

/*ПОХОЖИЕ МОДЕЛИ*/
$otherModels = array();
$modelsGroup = $arResult['PROPERTIES']['GRUPPIROVKA_PO_KOLODKE_SAYT']['VALUE'];
if(!empty($modelsGroup)) {
	$imageConfig = array('TYPE' => 'ONE', 'SIZE' => array('SMALL' => array('W' => 145, 'H' => 145)));
	$arFilter = array(
		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		"PROPERTY_GRUPPIROVKA_PO_KOLODKE_SAYT" => $modelsGroup,
		"ACTIVE" => "Y",
		">PROPERTY_OFFERS_AMOUNT" => 0,
		'PROPERTY_HAS_PHOTO' => 'Y',
		"!ID" => $arResult['ID']
	);
	$res = CIBlockElement::GetList(array('RAND' => 'ASC'), $arFilter, false, array('nTopCount' => 12), array("ID", "IBLOCK_ID", "ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_CML2_ARTICLE", 'CATALOG_GROUP_'.$arResult['PRICES_ALLOW'][0], 'CATALOG_GROUP_'.$arResult['PRICES_ALLOW'][1]));
	while ($ob = $res->GetNext(false, false)) {

		//достаем цены
		$oldPrice='';
		$newPrice='';
		if(empty($ob['CATALOG_PRICE_'.$arResult['PRICES_ALLOW'][0]]) || empty($ob['CATALOG_PRICE_'.$arResult['PRICES_ALLOW'][1]])) {
			$arFilter = array("IBLOCK_ID" => IBLOCK_SKU, "PROPERTY_CML2_LINK" => $ob["ID"], "ACTIVE" => "Y");
			$res_sku = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, array('nTopCount' => 1), array("ID", "IBLOCK_ID", "ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_CML2_ARTICLE", 'CATALOG_GROUP_' . $arResult['PRICES_ALLOW'][0], 'CATALOG_GROUP_' . $arResult['PRICES_ALLOW'][1]));
			if ($ob_sku = $res_sku->GetNext(false, false)) {
				$skuProduct = $ob_sku;
			}
			$oldPrice = $skuProduct['CATALOG_PRICE_'.$arResult['PRICES_ALLOW'][0]];
			$newPrice = $skuProduct['CATALOG_PRICE_'.$arResult['PRICES_ALLOW'][1]];
		}
		$oldPrice = !empty($oldPrice)? $oldPrice : $ob['CATALOG_PRICE_'.$arResult['PRICES_ALLOW'][0]];
		$newPrice = !empty($newPrice)? $newPrice : $ob['CATALOG_PRICE_'.$arResult['PRICES_ALLOW'][1]];
		$ob['OLD_PRICE'] = $oldPrice;
		$ob['NEW_PRICE'] = $newPrice;

		$articul = trim($ob['PROPERTY_CML2_ARTICLE_VALUE']);
		$ob['IMAGES'] = Citfact\Paolo::getProductImage($articul, $imageConfig);
		$otherModels[] = $ob;
	}
}
$arResult['OTHER_MODELS'] = $otherModels;


/*ДРУГИЕ ЦВЕТА*/
$otherColors = array();
$arXmlIds = array();
$colorGroup = $arResult['PROPERTIES']['GRUPPIROVKA_PO_MODELYAM_SAYT_']['VALUE'];
if(!empty($colorGroup)) {
	$imageConfig = array('TYPE' => 'ONE', 'SIZE' => array('SMALL' => array('W' => 45, 'H' => 45)));
	$arFilter = array("IBLOCK_ID" => $arParams['IBLOCK_ID'], "PROPERTY_GRUPPIROVKA_PO_MODELYAM_SAYT_" => $colorGroup, "ACTIVE" => "Y");
	$res = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false,
		array("ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PROPERTY_CML2_ARTICLE", "PROPERTY_TSVET_DLYA_FILTRA")
	);
	while ($ob = $res->GetNext(false, false)) {
		$articul = trim($ob['PROPERTY_CML2_ARTICLE_VALUE']);
		$ob['IMAGES'] = Citfact\Paolo::getProductImage($articul, $imageConfig);
		$otherColors[ $ob['ID'] ] = $ob;

		$arXmlIds[] = $ob['PROPERTY_TSVET_DLYA_FILTRA_VALUE'];
	}
}

$hldata = \Bitrix\Highloadblock\HighloadBlockTable::getById(4)->fetch();
$hlentity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hldata);
$hlDataClass = $hlentity->getDataClass();
$arFilter = array('UF_XML_ID' => $arXmlIds);
$res = $hlDataClass::getList(array('order'=>array('ID'=>'ASC'), 'filter' => $arFilter));
$arFiles = array();
while($arRes = $res->fetch())
{
	$arFiles[$arRes['UF_XML_ID']]= array(
		'FILE_PATH' => \CFile::GetPath($arRes['UF_FILE']),
		'NAME' => $arRes['UF_NAME']
	);
}

foreach ($otherColors as $key => &$arColor){
	$arColor['COLOR'] = $arFiles[$arColor['PROPERTY_TSVET_DLYA_FILTRA_VALUE']];
}
unset($arColor);

$arResult['OTHER_COLORS'] = $otherColors;

/*ОТЗЫВЫ*/
$arResult['REVIEWS'] = array();

$arkeysOtherColors = array_keys($arResult['OTHER_COLORS']);

$offers = CCatalogSku::getOffersList($arResult['ID']);
$grouppedProducts = array_reduce($offers, function ($carry, $item){
	$carry = array_merge($carry, array_keys($item));
	return $carry;
}, $arkeysOtherColors);

$grouppedProducts[] = $arResult['ID'];

$arIdstoReviews = $grouppedProducts;
//#60047 включен вывод отзывов торговых предложений
$arFilter = array("IBLOCK_ID" => IBLOCK_PRODUCT_REVIEW, "PROPERTY_PRODUCT_ID" => $arIdstoReviews, "ACTIVE" => "Y"); 
//$arFilter = array("IBLOCK_ID" => IBLOCK_PRODUCT_REVIEW, "PROPERTY_PRODUCT_ID" => $arResult['ID'], "ACTIVE" => "Y");
$res = CIBlockElement::GetList(array('ID' => 'ASC'), $arFilter, false, false, array("ID", "IBLOCK_ID", "NAME", "DATE_CREATE", "PROPERTY_USER_NAME", "PROPERTY_USER_ID", "PROPERTY_MESSAGE", "PROPERTY_STARS"));
while ($ob = $res->GetNext(false, false)) {
	$arTemp = array();
	$username = '';
	if ($ob['PROPERTY_USER_ID_VALUE'] != '') {
		$arTemp = $ob;
		$arTemp['DATE_CREATE_META'] = FormatDateFromDB($ob['DATE_CREATE'], 'YYYY-MM-DD');
		$arTemp['DATE_CREATE'] = FormatDateFromDB($ob['DATE_CREATE'], 'DD MMMM YYYY');
		$arUser = \Citfact\Tools::getUserInfo($ob['PROPERTY_USER_ID_VALUE']);
		$arTemp['USERNAME'] = $arUser['NAME'];
		$arTemp["USERNAME"] .= !empty($arUser["NAME"]) ? ' '.mb_substr($arUser['LAST_NAME'], 0, 1).'.' : '';
		$arResult['REVIEWS'][] = $arTemp;
	}
}

/*РЕЙТИНГ*/
$arResult['RATING'] = !empty($arResult['PROPERTIES']['rating']['VALUE'])? round($arResult['PROPERTIES']['rating']['VALUE']) : 0;


// МАГАЗИНЫ И НАЛИЧИЕ
$arShops = \Citfact\Paolo::GetShopsByFilter(array('PROPERTY_CITY' => $arParams['USER_CITY_ID']));
// Собираем id складов
$arIdsStores = array();
foreach ($arShops as $arShop) {
	if ($arShop['PROPERTY_STORE_ID_VALUE'] != '')
		$arIdsStores[] = $arShop['PROPERTY_STORE_ID_VALUE'];
}
$arResult['SHOPS'] = $arShops;


$arIdsOffers = array();
$arSizes = array();
if (!empty($arResult['OFFERS'])) {
	foreach ($arResult['OFFERS'] as $arOffer) {
		$arIdsOffers[] = $arOffer['ID'];
		$arSizes[$arOffer['ID']] = $arOffer['PROPERTIES']['RAZMER']['VALUE'];
	}
	$arResult['SIZES'] = $arSizes;
}
else{
	$arIdsOffers[] = $arResult['ID'];
}

$deactivatedOffers = CCatalogSKU::getOffersList(
    $arResult['ID'],
    $arParams['IBLOCK_ID'],
    array('ACTIVE' => 'N'),
    array('PROPERTY_RAZMER'),
    array()
);
$deactivatedOffersIds = array();
foreach ($deactivatedOffers[$arResult['ID']] as $offer) {
    $deactivatedOffersIds[] = $offer['ID'];
    $arResult['SIZES'][$offer['ID']] = $offer['PROPERTY_RAZMER_VALUE'];
}

$arAmount = array();
$rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' => array_merge($arIdsOffers, $deactivatedOffersIds), 'STORE_ID' => $arIdsStores), false, false, array());
while ($arStore = $rsStore->Fetch()){
	if ($arStore['AMOUNT'] > 0) {
		$arAmount[$arStore['STORE_ID']][] = $arStore['PRODUCT_ID'];
	}
}
$arResult['STORES_AMOUNT'] = $arAmount;


// Смотрим наличие торговых предложений в складе «Интернет-магазин»
$arResult['HAS_OFFERS'] = false;
$arResult['OFFERS_AMOUNT'] = array();
if (!empty($arResult['OFFERS'])){
	$arResult['HAS_OFFERS'] = true;
	$arIdsOffers = array();
	foreach ($arResult['OFFERS'] as $arOffer){
		$arIdsOffers[] = $arOffer['ID'];
	}
    $productAvailabilityBuy = new ProductAvailabilityBuy();
    $STORE_ID = [5];
    if($productAvailabilityBuy->isSectionChildClothes($arResult['IBLOCK_SECTION_ID'])){
        $STORE_ID[] = 80;
    }
	$rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' =>$arIdsOffers, 'STORE_ID' => $STORE_ID), false, false, array());
	while ($arStore = $rsStore->Fetch()){
		if ($arStore['AMOUNT'] > 0){
			$arResult['OFFERS_AMOUNT'][$arStore['PRODUCT_ID']] = $arStore['AMOUNT'];
		}
	}

	foreach ($arResult['OFFERS'] as $key => $arOffer){
		if ($arResult['OFFERS_AMOUNT'][$arOffer['ID']] == 0){
			unset($arResult['OFFERS'][$key]);
		}
	}
}


/*ЦЕНЫ ТОВАРА*/
$price_id = $_SESSION['GEO_PRICES']['PRICE_ID'];
$price_name = $_SESSION['GEO_PRICES']['FULL'][$price_id]['NAME'];
$price_action_id = $_SESSION['GEO_PRICES']['PRICE_ID_ACTION'];
$price_action_name = $_SESSION['GEO_PRICES']['FULL'][$price_action_id]['NAME'];
if(!empty($arResult['OFFERS'])) {
	$oldPrice = 999999;
	$oldPrice_print = '';
	$newPrice = 999999;
	$newPrice_print = '';
	foreach ($arResult['OFFERS'] as $key => $arOffer) {
		if ($arOffer['PRICES'][$price_name]['DISCOUNT_VALUE'] != '' && $arOffer['PRICES'][$price_name]['DISCOUNT_VALUE'] > 0 && $arOffer['PRICES'][$price_name]['DISCOUNT_VALUE'] < $oldPrice) {
			$oldPrice = $arOffer['PRICES'][$price_name]['DISCOUNT_VALUE'];
			$oldPrice_print = $arOffer['PRICES'][$price_name]['PRINT_DISCOUNT_VALUE'];
		}
		if ($arOffer['PRICES'][$price_name]['DISCOUNT_VALUE'] != '' && $arOffer['PRICES'][$price_action_name]['DISCOUNT_VALUE'] > 0 && $arOffer['PRICES'][$price_action_name]['VALUE'] < $newPrice) {
			$newPrice = $arOffer['PRICES'][$price_action_name]['DISCOUNT_VALUE'];
			$newPrice_print = $arOffer['PRICES'][$price_action_name]['PRINT_DISCOUNT_VALUE'];
		}
	}
	$arResult['OLD_PRICE'] = $oldPrice_print;
	$arResult['NEW_PRICE'] = $newPrice_print;
    $oldPriceValue = $oldPrice;
    $newPriceValue = $newPrice;
}
else{
	$arResult['OLD_PRICE'] = CurrencyFormat($arResult['CATALOG_PRICE_'.$price_id],'RUB');
	$arResult['NEW_PRICE'] = CurrencyFormat($arResult['CATALOG_PRICE_'.$price_action_id],'RUB');
    $oldPriceValue = $arResult['CATALOG_PRICE_'.$price_id];
    $newPriceValue = $arResult['CATALOG_PRICE_'.$price_action_id];
}

// Процент скидки
$sale_percent = 0;
if ($oldPriceValue > 0 && $newPriceValue > 0){
    $sale_percent = ($oldPriceValue - $newPriceValue) / $oldPriceValue * 100;
}
$arResult['SALE_PERCENT'] = round($sale_percent);


/*ВСЕ РАЗДЕЛЫ ПОСЛЕДНЕГО УРОВНЯ ДЛЯ ТОВАРА В RETAIL ROCKET*/
$allGroupsElement = $arDelGroup = array();
$db_old_groups = CIBlockElement::GetElementGroups($arResult['ID'], true);
while($ar_group = $db_old_groups->Fetch()){
	$nav = CIBlockSection::GetNavChain($arResult['IBLOCK_ID'], $ar_group['ID']);
	while($arSectionPath = $nav->GetNext()){
		if ($ar_group['ID'] != $arSectionPath['ID']) {
			$arDelGroup[] = $arSectionPath['ID'];
		}else{
			$allGroupsElement[$ar_group['ID']] = '"'.trim($arSectionPath['SECTION_PAGE_URL'], '/').'"';
		}
	}
}

foreach ($arDelGroup as $del) {
	if (!empty($allGroupsElement[$del])) {
		unset($allGroupsElement[$del]);
	}
}
$arResult['ELEMENT_ALL_GROUPS'] = implode($allGroupsElement, ',');

// Вывод "своих" хлебных крошек в зависимости от доп. св-ва раздела UF_BREADCRUMB
if (!$arParams["ADD_SECTIONS_CHAIN"]) {

    // собираем значение доп. св-ва UF_BREADCRUMB по разделам
    $arSectionBreadcrumb = [];
    $ob = \CIBlockSection::GetList(
        ["SORT" => "ID"],
        ["IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE" => "Y", "GLOBAL_ACTIVE" => "Y", "!UF_BREADCRUMB" => false],
        false,
        ["ID", "UF_BREADCRUMB"]
    );
    while ($res = $ob->GetNext()) {
        $arSectionBreadcrumb[$res["ID"]] = $res["UF_BREADCRUMB"];
    }

    // собираем цепочку разделов
    if ($arResult["IBLOCK_SECTION_ID"] > 0) {
        $arResult['PATH'] = [];
        $pathIterator = CIBlockSection::GetNavChain(
            $arResult['IBLOCK_ID'],
            $arResult["IBLOCK_SECTION_ID"],
            array(
                'ID', 'CODE', 'XML_ID', 'EXTERNAL_ID', 'IBLOCK_ID',
                'IBLOCK_SECTION_ID', 'SORT', 'NAME', 'ACTIVE',
                'DEPTH_LEVEL', 'SECTION_PAGE_URL'
            )
        );
        $pathIterator->SetUrlTemplates('', $arParams['SECTION_URL']);
        while ($path = $pathIterator->GetNext()) {
            //$ipropValues = new Iblock\InheritedProperty\SectionValues($arParams['IBLOCK_ID'], $path['ID']);
            //$path['IPROPERTY_VALUES'] = $ipropValues->getValues();
            $arResult['PATH'][] = $path;
        }
    }

    $this->__component->arResultCacheKeys = array_merge($this->__component->arResultCacheKeys, array('PATH'));
}

if ($arResult['PROPERTIES']['BLOK_TOVARA']['VALUE_ENUM'] == 'Одежда') {
    $arResult['HIDDEN_SHOPS_TAB'] = 'Y';
}

//убираем обозначение валюты из цены
$arResult["OLD_PRICE"] = mb_eregi_replace('[а-яА-Я.]', '', $arResult["OLD_PRICE"]);
$arResult["NEW_PRICE"] = mb_eregi_replace('[а-яА-Я.]', '', $arResult["NEW_PRICE"]);
