<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?$this->setFrameMode(true);?>
<div class="main-slider">
	<?foreach($arResult['ITEMS'] as $arItem) { ?>
		<?$file = CFile::ResizeImageGet($arItem['PROPERTY_IMAGE_VALUE'], array('width'=>1920, 'height'=>740), BX_RESIZE_IMAGE_EXACT, true);?>
		<div class="slide">
			<div class="slide-bg lazy" data-src="<?=$file["src"]?>"></div>
			<? if ($arItem['PROPERTY_LINK_VALUE'] != '') { ?>
				<a href="<?=$arItem['PROPERTY_LINK_VALUE']?>">
			<? } ?>
				<div class="slide-wrap">
					<div class="slider-content">
						<div class="title">
							<?=$arItem['NAME']?>
						</div>
						<div class="description"><?=$arItem['PROPERTY_PODPIS_VALUE']?></div>
					</div>
				</div>
			<? if ($arItem['PROPERTY_LINK_VALUE'] != '') { ?>
				</a>
			<? } ?>
		</div>
	<? } ?>
</div>
