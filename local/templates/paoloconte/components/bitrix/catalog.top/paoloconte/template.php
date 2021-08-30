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

<div class="main-item-slider">
<?if (!empty($arResult['ITEMS']))
{
	foreach ($arResult['ITEMS'] as $arItem) {?>
		<div class="slide">
			<div class="image">
				<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
					<?if($arItem['CATALOG_PHOTO']):?>
						<img src="<?=$arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>">
					<?else:?>
						<img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
					<?endif;?>
				</a>
			</div>
			<div class="price-box">
				<div class="old-price <?=($arItem['OLD_PRICE'] != ''? 'rouble':'')?>">
					<?=$arItem['OLD_PRICE']?>
				</div>
				<div class="new-price rouble">
					<?=$arItem['NEW_PRICE']?>
				</div>
			</div>
		</div>
	<?
	}
}
?>
</div>