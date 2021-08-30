<?
$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arResult['SHOP']["IBLOCK_ID"], $arResult['SHOP']["ID"]);
$arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
/*echo "<pre>1";
print_r($arResult["IPROPERTY_VALUES"]);*/
if ($arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"])
		$APPLICATION->SetPageProperty("title", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]);
if ($arResult["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"])
		$APPLICATION->SetPageProperty("keywords", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"]);
if ($arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"])
		$APPLICATION->SetPageProperty("description", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);
?>