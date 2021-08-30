<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

$IBLOCK_ID = 20;
$IBLOCK_ID_REGION = 26;

$arOrder = array();
$arFilter = array('IBLOCK_ID' => $IBLOCK_ID);
$arSelectFields = array("ID", "NAME", 'PROPERTY_OBLAST', 'PROPERTY_OKRUG');
$rsElements = CIBlockElement::GetList($arOrder, $arFilter, array('PROPERTY_OBLAST', 'PROPERTY_OKRUG'), FALSE, $arSelectFields);
while($arElement = $rsElements->GetNext())
{
	echo "<pre style=\"display:block;\">"; print_r($arElement); echo "</pre>";

	$code = CUtil::translit($arElement['PROPERTY_OBLAST_VALUE'], "ru", array("replace_space"=>"-","replace_other"=>"-"));
	$arLoadProductArray = Array(
		"IBLOCK_ID" => $IBLOCK_ID_REGION,
		"MODIFIED_BY"    => $USER->GetID(),
		"NAME" => $arElement['PROPERTY_OBLAST_VALUE'],
		"CODE" => $code,
		"PROPERTY_VALUES" => array('OKRUG' => $arElement['PROPERTY_OKRUG_VALUE'])
	);
	$el = new CIBlockElement;
	$res = $el->Add($arLoadProductArray);
	if ($res == false)
		echo '<span class="error">'.$el->LAST_ERROR.'</span>';

	//break;
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
