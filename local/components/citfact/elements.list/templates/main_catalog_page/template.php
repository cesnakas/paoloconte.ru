<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<? foreach ($arResult['ITEMS'] as $key => $arItem):?>
		<div class="main-title-catalog">
			<?=$arItem['NAME']?>
		</div>
		<?if (!empty($arItem['PROPERTY_CATALOG_SECTION_VALUE'])):?>
			<div class="table-emulate full table-section">
				<?foreach ($arItem['PROPERTY_CATALOG_SECTION_VALUE'] as $section_id):?>
					<?$arSection = $arResult['SECTIONS'][$section_id];?>
					<div class="cell-emulate align-center valign-middle half">
						<a class="main-a-catalog" href="/paoloconte_app<?=$arSection['URL']?>">
							<div class="img-block">
								<img src="<?=$arSection['UF_PHOTO_MOBILE']?>">
							</div>
							<?=$arSection['NAME']?>
						</a>
					</div>
				<?endforeach;?>
			</div>
		<?endif?>
<?endforeach?>