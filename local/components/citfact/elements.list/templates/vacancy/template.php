<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<?if (count($arResult['ITEMS']) <= 0):?>
	<div class="vacancy_noitems">К сожалению, в вашем городе нет вакансий. <?/*Предлагаем вам <a href="<?echo $APPLICATION->GetCurPageParam("show_all=yes", array(
			"show_all"));
		?>">посмотреть вакансии во всех городах</a>.*/?>
	</div>
<?endif?>

<div class="panel-group styled-vacancy" id="" role="tablist" aria-multiselectable="true">
	<?$count = 0;?>
	<?foreach($arResult['SECTIONS'] as $key => $arSection) { ?>
		<div class="panel">
			<div class="panel-heading pts-bold count-<?=$count?>" role="tab" id="heading-<?echo $key;?>">
				<a class="<?/*collapsed*/?>" data-toggle="collapse" href="#collapse-<?echo $key;?>" aria-expanded="true" aria-controls="collapse-<?echo $key;?>">
					<?=$arSection['NAME']?> <span class="count"><?=$arSection['COUNT']?></span> <i class="fa fa-chevron-down"></i>
				</a>
			</div>

			<div id="collapse-<?echo $key;?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-<?echo $key;?>">
				<div class="panel-body">
					<div class="vacancy-list">
						<?foreach($arSection['ITEMS'] as $key2 => $arItem) { 
						
							if ($arItem["CODE"])
								$code=$arItem["CODE"];
							else
								$code=get_translit($arItem["NAME"]);
							?>
							<div class="emulate-table full">
								<div class="emulate-cell name">
									<a href="<?/*=$arItem['DETAIL_PAGE_URL']*/?><?=$_SERVER["REQUEST_URI"].$code?>"><?=$arItem['NAME']?></a>
								</div>
								<div class="emulate-cell money">
									<?=$arItem['PROPERTY_SALARY_VALUE'];?>
									<?//if (!empty($arItem['PROPERTY_SALARY_TO_VALUE'])){echo ' до '.$arItem['PROPERTY_SALARY_TO_VALUE'];}?>
								</div>
								<div class="emulate-cell city">
									<?=$arItem['PROPERTY_CITY_NAME']?>
								</div>
								<?/*<div class="emulate-cell social">
									<a href="#" class="twitter">
										<i class="fa fa-twitter"></i>
									</a>
									<a href="https://www.facebook.com/Paolo.Conte.Shoes" class="facebook">
										<i class="fa fa-facebook"></i>
									</a>
									<a href="https://vk.com/paolo.conte.shop" class="vk">
										<i class="fa fa-vk"></i>
									</a>
									<a href="https://instagram.com/paolo.conte.shop/" class="instagram">
										<i class="fa fa-instagram"></i>
									</a>
								</div>*/?>
							</div>
						<? } ?>
					</div>
				</div>
			</div>
		</div>
		<?$count++;?>
	<? } ?>
</div><?//\Citfact\Tools::pre($arResult);?>
