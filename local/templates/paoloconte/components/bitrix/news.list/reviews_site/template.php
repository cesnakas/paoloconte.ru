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
$this->setFrameMode(true);
?>

<a href="#" class="get-review review_site btn btn-cell big mode1 fa fa-pencil" data-toggle="modal" data-target="#reviewModal">
	<span>Написать отзыв</span>
</a>

<?foreach($arResult["ITEMS"] as $arItem){?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="item">
		<div class="styled-text-box">

			<div class="title">
				<?$date = FormatDateFromDB($arItem['DATE_CREATE'], 'DD MMMM YYYY');?>
				<?=$arItem['PROPERTIES']['USERNAME']['VALUE']?> <span class="time-box"><?=$date?> </span>
			</div>
			<div class="tite-desc">
				<?=$arItem['PROPERTIES']['REVIEW_TEXT']['VALUE']['TEXT']?>
			</div>

		</div>
	</div>
<? } ?>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
	<?=$arResult["NAV_STRING"]?>
<?endif;?>