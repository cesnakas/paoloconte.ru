<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');

if(CModule::IncludeModule("iblock") && CModule::IncludeModule("sale")) {
	$RESULT["PostData"] = $_REQUEST; // заодно прикрепляем к массиву то что было прислано

	$productId = (int)$_REQUEST['product_id'];
	$isOffer = false;
	// Проверяем, ТП или нет добавляется в корзину
	$arOrder = array();
	$arFilter = array('IBLOCK_ID' => IBLOCK_SKU, 'ID' => $productId, 'ACTIVE' => 'Y');
	$RESULT['FILTER'] = $arFilter;
	$arSelectFields = array("ID");
	$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
	if($arElement = $rsElements->GetNext()) {
		$isOffer = true;
	}

	// Если добавляется ТП, проверяем, есть ли оно уже в корзине
	$hasOffer = false;
	if ($isOffer === true) {
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
			while ($arItems = $dbBasketItems->Fetch()) {
				$arBasketItems[] = $arItems;
				$hasOffer = true;
			}
		}
	}

	// Печатаем массив, содержащий актуальную на текущий момент корзину
	//$RESULT['BASKET'] = $arBasketItems;
	$RESULT['IS_OFFER'] = $isOffer;
	$RESULT['HAS_OFFER'] = $hasOffer;

	print json_encode($RESULT); // возвращаем JSON результат
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>