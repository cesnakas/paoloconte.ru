<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$frame = $this->createFrame()->begin();?>
<?if (!empty($arResult['ITEMS'])):?>
	<div class="detail-add-box  align-center">
		<?/*<div class="btn-box">
			<a href="#" class="btn btn-white full">Вы смотрели</a>
		</div>*/?>
		<div class="title-r align-center">
			Вы смотрели
		</div>

		<div class="catalog-wrap-mobile clear-after">
			<?foreach ($arResult['ITEMS'] as $arItem):?>
				<div class="item-wrap">
					<div class="item catalog-item">
						<div class="item-body">
							<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
								<div class="image">
									<?if($arItem['CATALOG_PHOTO']):?>
										<img src="<?=$arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>">
									<?else:?>
										<img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
									<?endif;?>
								</div>
							</a>
							<div class="price-box">
								<?if(!empty($arItem['NEW_PRICE'])):?>
									<div class="old-price rouble">
										<?=$arItem['OLD_PRICE'];?>
									</div>
									<div class="new-price rouble">
										<?=$arItem['NEW_PRICE'];?>
									</div>
								<?else:?>
									<div class="new-price rouble">
										<?=$arItem['OLD_PRICE'];?>
									</div>
								<?endif;?>
							</div>
						</div>
					</div>
				</div>
			<?endforeach;?>
		</div>
	</div>
<?endif?>

<?/*if (!empty($arResult['ITEMS'])):?>
	<div class="detail-add-wrap">
		<div class="container">
			<div class="title-add">
				Вы посмотрели
			</div>
			<div class="add-slider-wrap">
				<ul class="add-slider">
					<?foreach ($arResult['ITEMS'] as $arItem):?>
						<li>
							<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
								<div class="image">
									<?if($arItem['CATALOG_PHOTO']):?>
										<img src="<?=$arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>">
									<?else:?>
										<img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
									<?endif;?>
								</div>
							</a>
							<div class="price-box">
								<?if(!empty($arItem['NEW_PRICE'])):?>
									<div class="old-price rouble">
										<?=$arItem['OLD_PRICE'];?>
									</div>
									<div class="new-price rouble">
										<?=$arItem['NEW_PRICE'];?>
									</div>
								<?else:?>
									<div class="new-price rouble">
										<?=$arItem['OLD_PRICE'];?>
									</div>
								<?endif;?>
							</div>
						</li>
					<?endforeach;?>
				</ul>
			</div>
		</div>
	</div>
<?endif*/?>
<?$frame->beginStub();?>
<?$frame->end();?>