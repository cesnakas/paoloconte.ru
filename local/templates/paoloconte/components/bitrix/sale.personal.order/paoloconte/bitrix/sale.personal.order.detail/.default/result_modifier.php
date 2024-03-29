<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$cp = $this->__component;
if (is_object($cp))
{
	CModule::IncludeModule('iblock');

	if(empty($arResult['ERRORS']['FATAL']))
	{

		$hasDiscount = false;
		$hasProps = false;
		$productSum = 0;
		$basketRefs = array();

		$noPict = array(
			'SRC' => $this->GetFolder().'/images/no_photo.png'
		);

		if(is_readable($nPictFile = $_SERVER['DOCUMENT_ROOT'].$noPict['SRC']))
		{
			$noPictSize = getimagesize($nPictFile);
			$noPict['WIDTH'] = $noPictSize[0];
			$noPict['HEIGHT'] = $noPictSize[1];
		}

		foreach($arResult["BASKET"] as $k => &$prod)
		{
			if(floatval($prod['DISCOUNT_PRICE']))
				$hasDiscount = true;

			// move iblock props (if any) to basket props to have some kind of consistency
			if(isset($prod['IBLOCK_ID']))
			{
				$iblock = $prod['IBLOCK_ID'];
				if(isset($prod['PARENT']))
					$parentIblock = $prod['PARENT']['IBLOCK_ID'];

				foreach($arParams['CUSTOM_SELECT_PROPS'] as $prop)
				{
					$key = $prop.'_VALUE';
					if(isset($prod[$key]))
					{
						// in the different iblocks we can have different properties under the same code
						if(isset($arResult['PROPERTY_DESCRIPTION'][$iblock][$prop]))
							$realProp = $arResult['PROPERTY_DESCRIPTION'][$iblock][$prop];
						elseif(isset($arResult['PROPERTY_DESCRIPTION'][$parentIblock][$prop]))
							$realProp = $arResult['PROPERTY_DESCRIPTION'][$parentIblock][$prop];

						if(!empty($realProp))
							$prod['PROPS'][] = array(
								'NAME' => $realProp['NAME'],
								'VALUE' => htmlspecialcharsEx($prod[$key])
							);
					}
				}
			}

			// if we have props, show "properties" column
			if(!empty($prod['PROPS']))
				$hasProps = true;

			$productSum += $prod['PRICE'] * $prod['QUANTITY'];

			$basketRefs[$prod['PRODUCT_ID']][] =& $arResult["BASKET"][$k];

			/*if(!isset($prod['PICTURE']))
				$prod['PICTURE'] = $noPict;*/
		}


		$arResult['HAS_DISCOUNT'] = $hasDiscount;
		$arResult['HAS_PROPS'] = $hasProps;

		$arResult['PRODUCT_SUM_FORMATTED'] = SaleFormatCurrency($productSum, $arResult['CURRENCY']);

		if($img = intval($arResult["DELIVERY"]["STORE_LIST"][$arResult['STORE_ID']]['IMAGE_ID']))
		{

			$pict = CFile::ResizeImageGet($img, array(
				'width' => 150,
				'height' => 90
			), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);

			if(strlen($pict['src']))
				$pict = array_change_key_case($pict, CASE_UPPER);

			$arResult["DELIVERY"]["STORE_LIST"][$arResult['STORE_ID']]['IMAGE'] = $pict;
		}


		/****** CUSTO MIZATIONS ******/

		$arIds = array();
		foreach($arResult["BASKET"] as $arItem){
			$arIds[] = $arItem['PRODUCT_ID'];
		}

		// Достаем параметры товаров (ТП и не ТП)
		$arItems = array();

		$arOrder = array();
		$arFilter = array('IBLOCK_ID' => IBLOCK_SKU, 'ID' => $arIds);
		$arSelectFields = array("ID", "ACTIVE", "NAME", 'PROPERTY_CML2_LINK.PROPERTY_CML2_ARTICLE', 'PROPERTY_RAZMER');
		$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
		while($arElement = $rsElements->GetNext())
		{
			$arItems[$arElement['ID']]['ARTICLE'] = $arElement['PROPERTY_CML2_LINK_PROPERTY_CML2_ARTICLE_VALUE'];
			$arItems[$arElement['ID']]['RAZMER'] = $arElement['PROPERTY_RAZMER_VALUE'];
		}

		$arFilter = array('IBLOCK_ID' => IBLOCK_CATALOG, 'ID' => $arIds);
		$arSelectFields = array("ID", "ACTIVE", "NAME", 'PROPERTY_CML2_ARTICLE');
		$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
		while($arElement = $rsElements->GetNext())
		{
			$arItems[$arElement['ID']]['ARTICLE'] = $arElement['PROPERTY_CML2_ARTICLE_VALUE'];
		}

		// ПОДГОТОВКА КАРТИНОК
		$imageConfig = array(
			'TYPE'=>'ONE',
			'SIZE' => array(
				'SMALL' => array('W'=>50,'H'=>100)
			)
		);

		foreach($arResult["BASKET"] as $arItem){
			$articul = trim($arItems[$arItem['PRODUCT_ID']]['ARTICLE']);
			$arItems[$arItem['PRODUCT_ID']]['CATALOG_PHOTO'] = Citfact\Paolo::getProductImage($articul, $imageConfig);
		}

		$arResult['ITEMS'] = $arItems;

        $arResult['CAN_CANCEL'] = in_array($arResult['STATUS']['ID'], $arParams['CAN_BE_CANCELED_AT_STATUSES']) ? "Y" : "N";
	}
}
?>