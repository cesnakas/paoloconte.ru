<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);?>
<form action="<?=$arResult["FORM_ACTION"]?>">
	<?if($arParams["USE_SUGGEST"] === "Y"):?><?$APPLICATION->IncludeComponent(
		"bitrix:search.suggest.input",
		"",
		array(
			"NAME" => "q",
			"VALUE" => "",
			"INPUT_SIZE" => 15,
			"DROPDOWN_SIZE" => 10,
		),
		$component, array("HIDE_ICONS" => "Y")
	);?>
	<?else:?>
		<input type="text" name="q" value="" size="15" maxlength="100" placeholder="Искать товар или магазин" />
		<div class="search-btn no-select"><i class="fa fa-search" onclick="$('#search-submit-404').click();"></i></div>
		<input id="search-submit-404" type="submit" class="search-icon hide">
	<?endif;?>
	<?/*<input name="s" type="submit" value="<?=GetMessage("BSF_T_SEARCH_BUTTON");?>" />*/?>
</form>