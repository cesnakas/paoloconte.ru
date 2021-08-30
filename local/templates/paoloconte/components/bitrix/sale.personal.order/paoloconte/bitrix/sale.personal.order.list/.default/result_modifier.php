<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
	use Bitrix\Main\Localization\Loc;

	Loc::loadMessages(__FILE__);

	// we dont trust input params, so validation is required
	$legalColors = array(
		'green' => true,
		'yellow' => true,
		'red' => true,
		'gray' => true
	);
	// default colors in case parameters unset
	$defaultColors = array(
		'N' => 'green',
		'P' => 'yellow',
		'F' => 'gray',
		'PSEUDO_CANCELLED' => 'red'
	);

	foreach ($arParams as $key => $val)
		if(strpos($key, "STATUS_COLOR_") !== false && !$legalColors[$val])
			unset($arParams[$key]);

	// to make orders follow in right status order
	if(is_array($arResult['INFO']) && !empty($arResult['INFO']))
	{
		foreach($arResult['INFO']['STATUS'] as $id => $stat)
		{
			$arResult['INFO']['STATUS'][$id]["COLOR"] = $arParams['STATUS_COLOR_'.$id] ? $arParams['STATUS_COLOR_'.$id] : (isset($defaultColors[$id]) ? $defaultColors[$id] : 'gray');
			$arResult["ORDER_BY_STATUS"][$id] = array();
		}
	}
	$arResult["ORDER_BY_STATUS"]["PSEUDO_CANCELLED"] = array();

	$arResult["INFO"]["STATUS"]["PSEUDO_CANCELLED"] = array(
		"NAME" => Loc::getMessage('SPOL_PSEUDO_CANCELLED'),
		"COLOR" => $arParams['STATUS_COLOR_PSEUDO_CANCELLED'] ? $arParams['STATUS_COLOR_PSEUDO_CANCELLED'] : (isset($defaultColors['PSEUDO_CANCELLED']) ? $defaultColors['PSEUDO_CANCELLED'] : 'gray')
	);

	if(is_array($arResult["ORDERS"]) && !empty($arResult["ORDERS"]))
	{
		$arIds = array();
		foreach ($arResult["ORDERS"] as $order)
		{
			$order['HAS_DELIVERY'] = intval($order["ORDER"]["DELIVERY_ID"]) || strpos($order["ORDER"]["DELIVERY_ID"], ":") !== false;

			$stat = $order['ORDER']['CANCELED'] == 'Y' ? 'PSEUDO_CANCELLED' : $order["ORDER"]["STATUS_ID"];
			$color = $arParams['STATUS_COLOR_'.$stat];
			$order['STATUS_COLOR_CLASS'] = empty($color) ? 'gray' : $color;

			$arResult["ORDER_BY_STATUS"][$stat][] = $order;

			$arIds[] = $order['ORDER']['ID'];
		}


		$arResult['STATUSES_SHEEPLA'] = array();
		$arPropCodes = array(
			'status' => "STATUS_SHEEPLA",
			'substatus' => "SUBSTATUS_SHEEPLA",
			'ctn' => "CTN_SHEEPLA"
		);

		// Достаем свойства заказа
		foreach ($arIds as $order_id){
			//echo "<pre style=\"display:none;\">"; print_r($order_id); echo "</pre>";
			// Ищем значение статусов Шиплы
			foreach ($arPropCodes as $key => $propCode) {
				if ($arProp = CSaleOrderProps::GetList(array(), array('CODE' => $propCode))->Fetch()) {
					if ($arPropValue = CSaleOrderPropsValue::GetList(array("SORT" => "ASC"),
						array(
							"ORDER_ID" => $order_id,
							"ORDER_PROPS_ID" => $arProp["ID"]
						))->Fetch()
					) {
						$arResult['STATUSES_SHEEPLA'][$order_id][$arPropValue['CODE']] = $arPropValue['VALUE'];
					} else {

					}
				}
			}
		}

		/****** CUSTO MIZATIONS ******/

		$arIds = array();
		foreach ($arResult["ORDERS"] as $keyOrder=>$arOrder) {
			foreach ($arOrder['BASKET_ITEMS'] as $keyItem => $arItem) {
				$arIds[] = $arItem['PRODUCT_ID'];
			}
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

		/*ПОДГОТОВКА КАРТИНОК*/
		$imageConfig = array(
			'TYPE'=>'ONE',
			'SIZE' => array(
				'SMALL' => array('W'=>50,'H'=>100)
			)
		);

		foreach ($arResult["ORDERS"] as $keyOrder=>$arOrder) {
			foreach ($arOrder['BASKET_ITEMS'] as $keyItem => $arItem) {
				$articul = trim($arItems[$arItem['PRODUCT_ID']]['ARTICLE']);
				$arItems[$arItem['PRODUCT_ID']]['CATALOG_PHOTO'] = Citfact\Paolo::getProductImage($articul, $imageConfig);
			}
		}

		$arResult['ITEMS'] = $arItems;
	}

//_c($arResult);
?>