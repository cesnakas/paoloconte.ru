<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("catalog");
CModule::IncludeModule("sale");
$arBasketItems = array();
$dbBasketItems = CSaleBasket::GetList(array(),
			array(
				"FUSER_ID" => CSaleBasket::GetBasketUserID(),
				"LID" => "s1",
				"ORDER_ID" => "NULL",
				"DELAY" => "N",
				"CAN_BUY"=> "Y"
			),
		false,
		false,
		array("PRODUCT_ID")
	);
while ($arItems = $dbBasketItems->Fetch())
{
	$baseProduct = CCatalogSku::GetProductInfo(
		$arItems['PRODUCT_ID']
	);
	if ($baseProduct) {
		$arBasketItems[] = $baseProduct['ID'];
	} else {
		$arBasketItems[] = $arItems['PRODUCT_ID'];
	}
}
$arBasketItems = array_unique($arBasketItems);
if (is_array($arBasketItems) && !empty($arBasketItems)) {
    $strIDs =  implode(',', $arBasketItems);
} else {
    $strIDs = '';
}
echo $strIDs;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>