<?
$ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arResult['CITY_CURRENT'][0]["IBLOCK_ID"], $arResult['CITY_CURRENT'][0]["ID"]);
$arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();

if ($arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"])
		$APPLICATION->SetPageProperty("title", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]);
if ($arResult["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"])
		$APPLICATION->SetPageProperty("keywords", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_KEYWORDS"]);
if ($arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"])
		$APPLICATION->SetPageProperty("description", $arResult["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);
?>