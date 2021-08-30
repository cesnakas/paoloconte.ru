<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?foreach($arResult['ITEMS'] as $arItem) { ?>
	<?$file = CFile::ResizeImageGet($arItem['PROPERTY_IMAGE_VALUE'], array('width'=>1920, 'height'=>1280), BX_RESIZE_IMAGE_EXACT, true);?>
	<div class="slide" style="background-image: url('<?=$file['src']?>')" data-description="<?=$arItem['PROPERTY_PODPIS_VALUE']?>">
		<div class="slider-wrap emulate-table full full-height">
			<div class="slider-content emulate-cell valign-middle full full-height">
				<div class="title">
					<?=$arItem['NAME']?>
				</div>
				<div class="description">
					<?=$arItem['PROPERTY_PODPIS_VALUE']?>
				</div>
			</div>
		</div>
	</div>
<? } ?>