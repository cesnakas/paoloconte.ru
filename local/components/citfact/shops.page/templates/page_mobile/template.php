<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
?>
<div class="container">
	<?if (empty($arResult['SHOPS_CURRENT'])):?>
		<div class="map-list-wrap">
			<div class="title">
				Россия и СНГ
			</div>
			<div class="map-list">
				<ul>
				<?foreach ($arResult['CITIES'] as $arCity):?>
					<li>
						<a href="/shops/<?=$arCity['CODE']?>/" data-city-id="<?=$arCity['ID']?>"><?=$arCity['NAME']?></a>
						<span class="count"><?=$arCity['SHOPS_COUNT']?></span>
					</li>
				<?endforeach?>
				</ul>
			</div>
		</div>

	<?else:?>

		<div class="map-link-list">
			<div class="list-wrap">
				<div class="city-cont">
					<?foreach ($arResult['SHOPS_CURRENT'] as $arShop):?>
						<div class="list-item collapsed clear-after">
							<div class="shop-name">
								<?=$arShop['NAME']?>
							</div>
							<div class="">
								Адрес: <?=$arShop['PROPERTY_ADDRESS_VALUE']?>
							</div>
							<div class="">
								<?if($arShop['~PROPERTY_GRAPHICK_VALUE']['TEXT'] != ''):?>График работы: <?=$arShop['~PROPERTY_GRAPHICK_VALUE']['TEXT']?><br><?endif?>

								<? if ($arShop['PROPERTY_PHONE_VALUE'] != ''): ?>Телефон:
									<a href="tel:<?= $arShop['PROPERTY_PHONE_VALUE'] ?>"><?= $arShop['PROPERTY_PHONE_VALUE'] ?></a>
									<? endif; ?>

							</div>
						</div>
					<?endforeach?>
				</div>
			</div>

			<div class="btn-wrap">
				<a href="/shops/" class="btn big btn-gold mode2">Магазины в другом городе</a>
			</div>
		</div>
	<?endif;?>
</div>
