<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');

if(CModule::IncludeModule("iblock") && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")) {

    function getItemImage($articul) {
        $imageConfig = array('TYPE' => 'ONE', 'SIZE' => array('SMALL' => array('W' => 145, 'H' => 145)));
        $res = Citfact\Paolo::getProductImage($articul, $imageConfig);
        if (!empty($res)) {
            return $res['PHOTO']['0']['SMALL'];
        } else {
            return SITE_TEMPLATE_PATH.'/images/no_photo.png';
        }
    }

    function getItemInfo($ID) {
        $arFilterParent = array('IBLOCK_ID' => IBLOCK_CATALOG, 'ID' => $ID);
        $arSelectFieldsParent = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_TSVET_MARKETING", "PROPERTY_CML2_ARTICLE");
        $rsElementsParent = CIBlockElement::GetList(array(), $arFilterParent, FALSE, array('nTopCount' => 1), $arSelectFieldsParent);
        if($arElementParent = $rsElementsParent->GetNext()) {
            return $arElementParent;
        }
    }

    function getOfferInfo($ID) {
        $arFilter = array('IBLOCK_ID' => IBLOCK_SKU, 'ID' => $ID, 'ACTIVE' => 'Y');
        $arOrder = array();
        $arSelectFields = array("ID", "IBLOCK_ID", "NAME", "PROPERTY_RAZMER", "PROPERTY_CML2_ARTICLE");
        $rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, array('nTopCount' => 1), $arSelectFields);
        if($arElement = $rsElements->GetNext()) {
            return $arElement;
        }
    }

    function isOffer($ID) {
        $arResItem = $mxResult = CCatalogSku::GetProductInfo($ID);
        if (!empty($arResItem['ID'])) {
            return $arResItem['ID'];
        }
    }


	$RESULT["PostData"] = $_REQUEST; // заодно прикрепляем к массиву то что было прислано
    $RESULT['ITEM'] = array();

	$productId = (int)$_REQUEST['product_id'];
	$isOffer = false;

	// Проверяем, ТП или нет добавляется в корзину
    $arOfferElement = getOfferInfo($productId);
    if (!empty($arOfferElement)) {
        $isOffer = true;
        $RESULT['ITEM'] = array(
            'ID' => $arOfferElement['ID'],
            'RAZMER' => $arOfferElement['PROPERTY_RAZMER_VALUE'],
            'ARTICUL' => (!empty($arOfferElement['PROPERTY_CML2_ARTICLE_VALUE'])) ? $arOfferElement['PROPERTY_CML2_ARTICLE_VALUE'] : '',
        );
        if ($arResItemID = isOffer($arOfferElement['ID'])) {
            $arElementParent = getItemInfo($arResItemID);
            if (!empty($arElementParent)) {
                $RESULT['ITEM']['NAME'] = $arElementParent['NAME'];
                $RESULT['ITEM']['TSVET_MARKETING'] = $arElementParent['PROPERTY_TSVET_MARKETING_VALUE'];
                $RESULT['ITEM']['ARTICUL'] = (!empty($arElementParent['PROPERTY_CML2_ARTICLE_VALUE'])) ? $arElementParent['PROPERTY_CML2_ARTICLE_VALUE'] : '';
            }
        }
    } else {
        $arElementParent = getItemInfo($productId);
        if (!empty($arElementParent)) {
            $RESULT['ITEM']['ID'] = $arElementParent['ID'];
            $RESULT['ITEM']['NAME'] = $arElementParent['NAME'];
            $RESULT['ITEM']['TSVET_MARKETING'] = $arElementParent['PROPERTY_TSVET_MARKETING_VALUE'];
            $RESULT['ITEM']['RAZMER'] = false;
            $RESULT['ITEM']['ARTICUL'] = (!empty($arElementParent['PROPERTY_CML2_ARTICLE_VALUE'])) ? $arElementParent['PROPERTY_CML2_ARTICLE_VALUE'] : '';
        }
    }
    $RESULT['ITEM']['IMAGE'] = getItemImage($RESULT['ITEM']['ARTICUL']);



	// Если добавляется ТП, проверяем, есть ли оно уже в корзине
	$hasOffer = false;
//	if ($isOffer === true) {
		$arID = array();
		$arProductId = array();
		$arBasketItems = array();

		$dbBasketItems = CSaleBasket::GetList(
			array(
				"NAME" => "ASC",
				"ID" => "ASC"
			),
			array(
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => SITE_ID,
				"ORDER_ID" => "NULL",
				"DELAY" => 'N',
				'PRODUCT_ID' => $productId
		),
			false,
			false,
			array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "PRODUCT_PROVIDER_CLASS")
		);
		while ($arItems = $dbBasketItems->Fetch()) {
			if ('' != $arItems['PRODUCT_PROVIDER_CLASS'] || '' != $arItems["CALLBACK_FUNC"]) {
				CSaleBasket::UpdatePrice($arItems["ID"],
					$arItems["CALLBACK_FUNC"],
					$arItems["MODULE"],
					$arItems["PRODUCT_ID"],
					$arItems["QUANTITY"],
					"N",
					$arItems["PRODUCT_PROVIDER_CLASS"]
				);
				$arID[] = $arItems["ID"];
			}
		}
		if (!empty($arID)) {
			$dbBasketItems = CSaleBasket::GetList(
				array(
					"NAME" => "ASC",
					"ID" => "ASC"
				),
				array(
					"ID" => $arID,
					"ORDER_ID" => "NULL"
				),
				false,
				false,
				array("ID", "CALLBACK_FUNC", "MODULE", "PRODUCT_ID", "QUANTITY", "DELAY", "CAN_BUY", "PRICE", "WEIGHT", "PRODUCT_PROVIDER_CLASS", "NAME")
			);
			if ($arItems = $dbBasketItems->Fetch()) {
//				$arBasketItems[] = $arItems;
				$hasOffer = true;
			}
		}
//	}

	// Печатаем массив, содержащий актуальную на текущий момент корзину
//	$RESULT['BASKET'] = $arBasketItems;
	$RESULT['IS_OFFER'] = $isOffer;
	$RESULT['HAS_OFFER'] = $hasOffer;

	print json_encode($RESULT); // возвращаем JSON результат
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");