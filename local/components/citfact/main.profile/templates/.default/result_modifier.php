<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$arResult['ADDRESSES'] = array();
if ($USER->IsAuthorized()) {
	$arResult['ADDRESSES'] = \Citfact\Paolo::GetUserAddresses($USER->GetID());
}

$arResult['GEO_LOCATION'] = \Citfact\Paolo::GetBitrixLocation($_SESSION['CITY_ID']);