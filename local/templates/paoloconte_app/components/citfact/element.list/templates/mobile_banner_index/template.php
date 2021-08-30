<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?foreach($arResult['ITEMS'] as $arItem) { ?>
	<?$file = CFile::ResizeImageGet($arItem['PROPERTY_IMAGE_VALUE'], array('width'=>640, 'height'=>180), BX_RESIZE_IMAGE_EXACT, true);?>
	<a href="<?=$arItem['PROPERTY_LINK_VALUE']?>"><img src="<?=$file['src']?>"></a>
<? } ?>