<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}//echo'List <pre>';print_r($arResult['ITEMS']);echo'</pre>';
?>
<?$this->setFrameMode(true);?>
<div class="main-slider">
	<?foreach($arResult['ITEMS'] as $arItem) { ?>
		<?$file = CFile::ResizeImageGet($arItem['PROPERTY_IMAGE_VALUE'], array('width'=>1920, 'height'=>740), BX_RESIZE_IMAGE_EXACT, true);?>
		<div class="slide lazy">
			<div class="slide-bg" style="background-image: url('<?=$file['src']?>')"></div>

			<div class="slide-wrap">
				<div class="slider-content">
					<div class="title">
						<?=$arItem['NAME']?>
					</div>
					<div class="description">
						<?=$arItem['PREVIEW_TEXT']//=$arItem['PROPERTY_PODPIS_VALUE']?>
					</div>
					<?if ($arItem['PROPERTY_LINK_VALUE'] != ''):?>
					<a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="btn btn-icon btn-gold small mode2 icon-arrow-right">
						Подробнее
					</a>
					<?endif;?>
				</div>
			</div>
		</div>
	<? } ?>
</div>