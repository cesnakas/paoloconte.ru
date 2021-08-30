<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$count_items = 0;

foreach($arResult["ITEMS"] as $arItem){
	if (array_key_exists($arItem['PROPERTY_TOVAR_ID_VALUE'], $arResult['TOVARS'])){
		$count_items++;
	}
}

// Если нет избранных товаров, показываем сообщение и ссылку на каталог
if ($count_items == 0){
	?>
	<div class="top-text align-center">
		У вас еще нет подписок.
	</div>

	<div class="btn-box align-center">
		<a href="/paoloconte_app/catalog/" class="btn btn-green full big-2 fa mode1 fa-shopping-cart" style="width: 340px;"><span>Перейти в каталог товаров</span></a>
	</div>
<?
}
else{
?>

<div class="wish-list-price-wrap">
	<table class="full">
		<tr>
			<th>
				Товар
			</th>
			<?/*<th>
				Характеристики
			</th>*/?>

			<th>
				Текущая цена
			</th>
			<th>
				Ожидаемая цена
			</th>
			<?/*<th>
				Email
			</th>*/?>
			<th>
			</th>
			<th>
			</th>
		</tr>
		<?foreach($arResult["ITEMS"] as $arItem){
			$tovar_id = $arItem['PROPERTY_TOVAR_ID_VALUE'];
			if (array_key_exists($tovar_id, $arResult['TOVARS'])):?>
				<?
				$price_str = 'CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID'];
				$price_str_action = 'CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID_ACTION'];
				$price = $arResult['TOVARS'] [$tovar_id] [$price_str];
				$price_action = $arResult['TOVARS'] [$tovar_id] [$price_str_action];
				$price_formatted = number_format($price, 0, ',', ' ');
				$price_formatted_action = number_format($price_action, 0, ',', ' ');

				$price_subscribe_formatted = number_format($arItem['PROPERTY_PRICE_VALUE'], 0, ',', ' ');

				$tovar_url = $arResult['TOVARS'] [$tovar_id] ['DETAIL_PAGE_URL'];
				?>

				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				?>
				<tr>
					<td>
						<div class="image">
							<?if($arResult['TOVARS'] [$tovar_id] ['CATALOG_PHOTO']):?>
								<a href="<?=$tovar_url?>"><img src="<?=$arResult['TOVARS'] [$tovar_id] ['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>"></a>
							<?else:?>
								<img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
							<?endif;?>
						</div>

						<div class="name"><a href="/paoloconte_app<?=$tovar_url?>"><?=$arResult['TOVARS'] [$tovar_id] ['NAME']?></a></div>
						<?/*<div class="index"><?=$arResult['TOVARS'] [$tovar_id] ['ARTICUL']?></div>*/?>
					</td>
					<?/*<td>
						<div class="name"><a href="<?=$tovar_url?>"><?=$arResult['TOVARS'] [$tovar_id] ['NAME']?></a></div>
						<div class="index"><?=$arResult['TOVARS'] [$tovar_id] ['ARTICUL']?></div>
					</td>*/?>
					<td>
						<div class="price-box">
							<?if ($price_action != ''):?>
								<div class="old-price rouble">
									<?=$price_formatted?>
								</div>
								<div class="new-price rouble">
									<?=$price_formatted_action?>
								</div>
							<?else:?>
								<div class="new-price rouble">
									<?=$price_formatted?>
								</div>
							<?endif;?>
						</div>
					</td>
					<td>
						<div class="price-box">
							<div class="new-price rouble">
								<?=$price_subscribe_formatted?>
							</div>
						</div>
					</td>
					<?/*<td>
						<div class="price-box">
							<div class="new-price">
								<?=$arItem['PROPERTY_EMAIL_VALUE']?>
							</div>
						</div>
					</td>*/?>
					<td>
						<?if ($arResult['TOVARS'][$tovar_id]['STORE_AMOUNT'] > 0):?>
							<a href="/paoloconte_app<?=$tovar_url . '?id='.$arResult['TOVARS'][$tovar_id]['ID'] . '&action=ADD2BASKET&ajax_basket=Y'?>" class="btn-tobasket-insubscribe" data-product-id="<?=$arItem['ID']?>"><img src="/local/templates/paoloconte_mobile/images/svg/cart.svg"></a>
						<?else:?>
							Нет на складе
						<?endif;?>
					</td>
					<td>
						<a href="#" class="del-elem close-small del-subscribe" data-element-id="<?=$arItem['ID']?>" title="Удалить подписку"></a>
					</td>
				</tr>
			<?endif?>
		<? } ?>

	</table>
</div>
<?}?>