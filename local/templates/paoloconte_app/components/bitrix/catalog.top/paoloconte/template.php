<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<?if (!empty($arResult['ITEMS'])) {?>
	<div class="block-title">
		<h4>Новинки сезона</h4>
		<div class="title-link">
			<a href="/paoloconte_app/catalog/"><span>Все модели</span>  <i class="fa fa-caret-right"></i></a>
		</div>
	</div>

	<div class="catalog-wrap-mobile clear-after">
		<?foreach ($arResult['ITEMS'] as $arItem) {?>
			<div class="item-wrap">
				<div class="item catalog-item">
					<div class="item-body">
						<a href="/paoloconte_app<?=$arItem['DETAIL_PAGE_URL']?>">
							<?if($arItem['CATALOG_PHOTO']):?>
								<img src="<?=$arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>">
							<?else:?>
								<img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
							<?endif;?>
						</a>
						<div class="price-box">
							<div class="old-price <?=$arItem['OLD_PRICE'] != ''? 'rouble':''?>"><?=$arItem['OLD_PRICE'];?></div>
							<div class="new-price rouble">
								<?=$arItem['NEW_PRICE']?>
							</div>
						</div>
					</div>
				</div>
			</div>
		<? } ?>
	</div>
<?}?>