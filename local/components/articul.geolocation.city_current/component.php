<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
if (!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;
if (!isset($arParams["CURRENT_CITY_ID"]) && $_SESSION["CITY_ID"])
	$arParams["CURRENT_CITY_ID"] = $_SESSION["CITY_ID"];

if ($this->StartResultCache()) 
{
    if (!CModule::IncludeModule("iblock")) {
        $this->AbortResultCache();
        ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
        return;
    }

    $res = CIBlockElement::GetByID($arParams["CURRENT_CITY_ID"]);
    if ($arFields = $res->GetNext())
    {
        $arResult["CURRENT"] = $arFields;
    }
	
	if ($arResult["CURRENT"]["NAME"])
	{	
		$this->IncludeComponentTemplate();
	}
	else
	{
		$this->AbortResultCache();
		CHTTP::SetStatus("404 Not Found");
		@define('ERROR_404', 'Y');
	}
}