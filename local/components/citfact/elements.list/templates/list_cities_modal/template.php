<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?$this->setFrameMode(true);?>
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

		$('#city-choose-btn').click(function () {
			var input_val = $('#city-input').val();
			for(var key in cities_list) {
				if (cities_list[key].value.toLowerCase() == input_val.toLowerCase()){
					var value = cities_ids[cities_list[key].value];
					var url = location.protocol + '//' + location.host + location.pathname + '?city=' + value;
					window.location.href = url;
				}
			}

			return false;
		});

	});
</script>

<?$showWindow = $arParams['SHOW_WINDOW'];?>
<? $frame = $this->createFrame()->begin(""); ?>
<?if ($showWindow === true): ?>
	<script>
		$(document).ready(function () {
			$('#cityModal').modal('show');

			var block = $('.block-first-show');
			var btn = $('.btn-first-show');
			block.hide();
			btn.show();

			btn.click(function () {
				btn.fadeOut();
				block.show();
				return false;
			});
		});
	</script>
<? endif ?>
<? $frame->end(); ?>

<div class="your-city">
	<div class="city">
		Ваш город <span><?=$arResult['CURRENT_CITY_NAME']?>?</span>
	</div>
	<div class="btn-box">
		<a href="#" class="btn btn--black" data-dismiss="modal">Да, всё верно</a>
	</div>
	<div class="btn-box btn-first-show" style="display: none;">
		<a href="#" class="btn btn-gray">Нет, другой</a>
	</div>
</div>
<form action="#">
	<div class="modal-body block-first-show">
		<div class="emulate-table city-box-1 full">
			<div class="emulate-cell input-cell valign-middle">
				<input type="text" id="city-input" placeholder="Введите название города" />
				<input type="hidden" value="" id="city-id-autocomplete" />
				<div id="cities-autocomplete-cont"></div>
			</div>

			<div class="emulate-cell text valign-middle">
				<a href="#" class="btn btn--black" id="city-choose-btn">Выбрать</a>
			</div>

			<div class="emulate-cell text valign-middle">
				или выберите из списка
			</div>
		</div>

		<div class="word-box">
			<?foreach($arResult['ITEMS'] as $key=>$arItem):?>
				<a href="#" class="<?=($key == $arResult['ACTIVE_LETTER']? 'active':'')?>" data-letter="<?=$key?>"><?=$key?></a>
			<?endforeach?>
		</div>

		<div class="main-city">
			<ul>
				<?foreach($arResult['MAIN_CITIES'] as $arItem):?>
					<li>
						<a href="<?=$APPLICATION->GetCurPageParam("city=".$arItem['ID'], array("city"), false);?>"><?=$arItem['NAME']?></a>
					</li>
				<?endforeach?>
			</ul>
			<div class="clear"></div>
		</div>
		<div class="city-list-wrap">
			<?foreach($arResult['ITEMS'] as $key=>$arItem):?>
				<div class="city-list <?=($key == $arResult['ACTIVE_LETTER']? 'active':'')?>" data-letter="<?=$key?>">
					<ul>
						<?
						$count = count($arItem);
						$count_incol = floor($count/4);
						if ($count_incol <= 1){
							$count_incol = $count;
						}
						if ($count%$count_incol > 0){
							$count_incol++;
						}
						?>
						<?foreach($arItem as $key2=>$arCity):?>
							<li>
								<a href="<?=$APPLICATION->GetCurPageParam("city=".$arCity['ID'], array("city"), false);?>" title="<?=$arCity['PROPERTY_OBLAST_VALUE'];?>"><?=$arCity['NAME'];?></a>
							</li>
							<?if (($key2+1)%$count_incol == 0 && $key2 != 0):?></ul><ul><?endif;?>
						<?endforeach;?>
					</ul>
				</div>
			<?endforeach;?>
		</div>
	</div>
</form>
