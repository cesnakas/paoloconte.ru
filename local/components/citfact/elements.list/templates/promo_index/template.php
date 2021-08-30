<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?$this->setFrameMode(true);?>
<?foreach($arResult["ITEMS"] as $arItem){?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="deal-item">
		<div class="deal-body">
			<div class="item-head">
				<div class="title">
					<?=$arItem['PROPERTY_TITLE_VALUE']?>
				</div>
				<div class="tite-desc">
					<?=$arItem['PROPERTY_DESC_VALUE']?>
				</div>
			</div>
			<?$path = CFile::GetPath($arItem['PROPERTY_IMAGE_VALUE']);?>
			<?if(!empty($arItem['PROPERTY_LINK_VALUE'])){?>
				<a class="item-body" href="<?=$arItem['PROPERTY_LINK_VALUE'];?>" style="background-image: url('<?=$path?>')"></a>
			<?}else{?>
				<div class="item-body" style="background-image: url('<?=$path?>')"></div>
			<?}?>
			<div class="item-footer">
				<a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="btn btn-gold full small mode2 icon-arrow-right"><?=$arItem['PROPERTY_BUTTON_TEXT_VALUE']?></a>
			</div>
		</div>
	</div>
<? } ?>