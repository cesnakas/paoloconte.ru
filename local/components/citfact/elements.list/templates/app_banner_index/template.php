<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<div class="detail-item-big">
	<?foreach($arResult['ITEMS'] as $arItem) { ?>
		<?$file = CFile::ResizeImageGet($arItem['PROPERTY_IMAGE_VALUE'], array('width'=>640, 'height'=>180), BX_RESIZE_IMAGE_EXACT, true);?>
		<div class="item">
			<a href="/paoloconte_app<?=$arItem['PROPERTY_LINK_VALUE']?>" style="background-image: url('<?=$file['src']?>')"></a>
		</div>
	<? } ?>
</div>
