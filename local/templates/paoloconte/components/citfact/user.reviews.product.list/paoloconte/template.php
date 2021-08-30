<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();?>

<div class="reviews_block_product_user">
	<?if (!empty($arResult['ITEMS'])) {?>
		<?foreach ($arResult['ITEMS'] as $item) {?>
			<div class="item">
				<div class="styled-text-box">
					<div class="title">
						<?=$item['PROPERTY_USER_NAME_VALUE']?> <span class="time-box"><?=$item['DATE_CREATE']?></span>
					</div>
					<div class="tite-desc">
						<div class="href-prod">
							<a href="<?=$item['PRODUCT']['DETAIL_PAGE_URL']?>"><?=$item['PRODUCT']['NAME']?></a>
						</div>
						<?=$item['PROPERTY_MESSAGE_VALUE']?>
					</div>
				</div>
			</div>
		<?}?>
		<div class="pagination-wrap emulate-table full">
			<?=$arResult['NAV_STRING']?>
		</div>
	<?}else{?>
		<p>Список отзывов пуст</p>
	<?}?>
</div>