<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?
$cities_list = CUtil::PhpToJSObject($arResult['AUTOCOMPLETE_CITIES'], false, true, true);
$cities_ids = CUtil::PhpToJSObject($arResult['AUTOCOMPLETE_IDS'], false, true, true);
?>
<script src="<?=$this->GetFolder()?>/jquery-ui.min.js"></script>
<script>
	$(document).ready(function () {
		var cities_list = <?=$cities_list?>;
		var cities_ids = <?=$cities_ids?>;
		$( "#city-input" ).autocomplete({
			source: cities_list,
			appendTo: "#cities-autocomplete-cont",
			minLength: 1,
			select: function( event, ui ) {
				var value = cities_ids[ui.item.value];
				var url = location.protocol + '//' + location.host + location.pathname + '?city=' + value;
				window.location.href = url;
			}
		});
	});
</script>

<?/*<div class="your-city">
	<div class="city">
		Ваш город <span><?=$arResult['CURRENT_CITY_NAME']?>?</span>
	</div>
	<div class="btn-box">
		<a href="#" class="btn btn-gold" data-dismiss="modal">Да, всё верно</a>
	</div>
</div>*/?>
<form action="#">
	<div class="modal-body">
		<div class="emulate-table city-box-1 full">
			<div class="emulate-row text valign-middle">
				Введите название города
			</div>
			<div class="emulate-row input-cell valign-middle">
				<input type="text" id="city-input" placeholder="Вышний Волочек" />
				<input type="hidden" value="" id="city-id-autocomplete" />
				<div id="cities-autocomplete-cont"></div>
			</div>
		</div>

		<div class="main-city">
			<ul>
				<?foreach($arResult['MAIN_CITIES'] as $arItem):?>
					<li>
						<a href="<?=$APPLICATION->GetCurPageParam("city=".$arItem['ID'], array("city"));?>"><?=$arItem['NAME']?></a>
					</li>
				<?endforeach?>
			</ul>
			<div class="clear"></div>
		</div>

		<div class="word-box">
			<?foreach($arResult['ITEMS'] as $key=>$arItem):?>
				<a href="#" class="<?=($key == $arResult['ACTIVE_LETTER']? 'active':'')?>" data-letter="<?=$key?>"><?=$key?></a>
			<?endforeach?>
		</div>

		<div class="city-list-wrap">
			<?foreach($arResult['ITEMS'] as $key=>$arItem):?>
				<div class="city-list <?=($key == $arResult['ACTIVE_LETTER']? 'active':'')?>" data-letter="<?=$key?>">
					<ul>
						<?
						/*$count = count($arItem);
						$count_incol = floor($count/4);
						if ($count_incol <= 1){
							$count_incol = $count;
						}
						if ($count%$count_incol > 0){
							$count_incol++;
						}*/
						?>
						<?foreach($arItem as $key2=>$arCity):?>
							<a href="<?=$APPLICATION->GetCurPageParam("city=".$arCity['ID'], array("city"));?>" title="<?=$arCity['PROPERTY_OBLAST_VALUE'];?>">
							<li>
								<?=$arCity['NAME'];?>
							</li>
							</a>
							<?/*if (($key2+1)%$count_incol == 0 && $key2 != 0):?></ul><ul><?endif;*/?>
						<?endforeach;?>
					</ul>
				</div>
			<?endforeach;?>
			<?/*<div class="city-list">
				<?for($i=1;$i<5;$i++) { ?>
					<ul>
						<?for($j=1;$j<12;$j++) { ?>
							<li>
								<a href="#">Дзержинское</a>
							</li>
						<? } ?>
					</ul>
				<? } ?>
				<div class="clear"></div>
			</div>*/?>
		</div>
	</div>
</form>