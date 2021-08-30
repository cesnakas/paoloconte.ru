<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(false);
?>
<?
global $USER;
$isAuth = $USER->IsAuthorized();
$authY = 'Для авторизованного';
$authN = 'Для Не авторизованного';
?>
<?foreach ($arResult['ITEMS'] as $item) {?>
	<?if (
		($isAuth && $item['PROPERTY_AUTH_VALUE'] == $authY) ||
		(!$isAuth && $item['PROPERTY_AUTH_VALUE'] == $authN) ||
		empty($item['PROPERTY_AUTH_VALUE'])
	) {?>
		<li>
			<?if (!empty($item['PROPERTY_GEOLOCATION_VALUE'])) {?>
				<i class="fa fa-map-marker"></i> <a href="#" data-toggle="modal" data-target="#cityModal"><?$APPLICATION->IncludeComponent("articul.geolocation.city_current", "mobile", array(), false);?></a>
			<?}else{?>
				<?if(!empty($item['PROPERTY_ICON_VALUE'])){?>
					<i class="fa <?=$item['PROPERTY_ICON_VALUE'];?>"></i>
				<?}?>
				<?if(!empty($item['PROPERTY_LINK_VALUE'])){?>
					<a href="<?=$item['PROPERTY_LINK_VALUE'];?>" <?=(!empty($item['PROPERTY_PARAMS_VALUE']))?($item['PROPERTY_PARAMS_VALUE']):'';?>><?=$item['NAME'];?></a>
				<?}else{?>
					<span class="gray" <?=(!empty($item['PROPERTY_PARAMS_VALUE']))?($item['PROPERTY_PARAMS_VALUE']):'';?>><?=$item['NAME'];?></span>
				<?}?>
			<?}?>
		</li>
	<?}?>
<?}?>