<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<ul class="promo-menu">
<?foreach($arResult['ITEMS'] as $arItem):?>
	<li>
		<a href="<?=$arItem['PROPERTY_LINK_VALUE']?>"><?=$arItem['NAME']?></a>
	</li>
<?endforeach?>
</ul>