<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

if(!CModule::IncludeModule("citfact.tools"))
    return;

$arTypes = CIBlockParameters::GetIBlockTypes();

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = $arRes["NAME"];

$arIBlocksSections=array();

$db_iblock = CIBlockSection::GetList(array("SORT"=>"ASC"), array( "SITE_ID"=>$_REQUEST["site"], "IBLOCK_ID" =>IBLOCK_AS_SLIDER));
while($arRes = $db_iblock->Fetch())
    $arIBlocksSections[$arRes["ID"]] = $arRes["NAME"];

$arUGroupsEx = array();
$dbUGroups = CGroup::GetList($by = "c_sort", $order = "asc");
while($arUGroups = $dbUGroups -> Fetch())
{
	$arUGroupsEx[$arUGroups["ID"]] = $arUGroups["NAME"];
}

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
        "IBLOCK_SECTION" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_SECTION_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocksSections,
            "DEFAULT" => '',
            "ADDITIONAL_VALUES" => "Y",
            "REFRESH" => "Y",
        ),
        "DISPLAY_PREVIEW" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_DISPLAY_PREVIEW"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "ACTIVE_AUTOSCROLLING" => Array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_IBLOCK_DESC_ACTIVE_AUTOSCROLLING"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
        "CACHE_TIME"  =>  array("DEFAULT"=>36000000),
        "CACHE_GROUPS" => array(
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => GetMessage("CP_BND_CACHE_GROUPS"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "Y",
        ),
	),
);

if($arCurrentValues["USE_PERMISSIONS"]!="Y")
	unset($arComponentParameters["PARAMETERS"]["GROUP_PERMISSIONS"]);
