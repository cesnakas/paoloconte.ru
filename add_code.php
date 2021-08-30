<?
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");

$IBLOCK_ID = 20;

$arOrder = array();
$arFilter = array('IBLOCK_ID' => $IBLOCK_ID);
$arSelectFields = array("ID", "NAME");
$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
while($arElement = $rsElements->GetNext())
{
	echo "<pre style=\"display:block;\">"; print_r($arElement); echo "</pre>";
	$code = CUtil::translit($arElement['NAME'], "ru", array("replace_space"=>"-","replace_other"=>"-"));
	echo "<pre style=\"display:block;\">"; print_r($code); echo "</pre>";

	$arLoadProductArray = Array(
		"IBLOCK_ID" => $IBLOCK_ID,
		"MODIFIED_BY"    => $USER->GetID(),
		"CODE"			 => $code,
	);
	$el = new CIBlockElement;
	$res = $el->Update($arElement['ID'], $arLoadProductArray);
	if ($res == false)
		echo '<span class="error">'.$el->LAST_ERROR.'</span>';
	//break;
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
