<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
//_c($arResult);
?>
<script>
	BX.message({
		TEMPLATE_PATH: '<? echo $this->__folder ?>',
		COMPONENT_PATH: '<? echo $this->__component->__path ?>',
		arParams_product_review: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>

<form action="#">
	<?=bitrix_sessid_post()?>
	<input type="text" name="yarobot" value="" class="hide">
	<?/*<div class="line">
		<a href="#" class="get-modal" data-toggle="modal" data-target="#enterModal">Сменить учетную запись</a>
	</div>*/?>
	<?foreach ($arResult['SHOW_PROPERTIES'] as $arProp):?>
		<?
		$idElement = !empty($arProp['ID'])? 'id="'.$arProp['ID'].'"': '';
		?>
		<?if ($arProp['PARAMS_TYPE'] == 'textarea'):?>
			<div class="line">
				<textarea <?=$idElement?> class="<?=$arProp['CLASS']?>" name="<?=$arProp['CODE']?>" placeholder="<?=$arProp['PLACEHOLDER']?>"></textarea>
			</div>
		<?elseif($arProp['PARAMS_TYPE'] == 'hidden'):?>
			<input <?=$idElement?> class="<?=$arProp['CLASS']?>" type="hidden" name="<?=$arProp['CODE']?>" value="<?=$arProp['VALUE']?>"/>
		<?elseif($arProp['PARAMS_TYPE'] == 'stars'):?>
			<div class="line">
				<div class="rating_stars">
					<?for($i=1; $i<=$arProp['VALUE']; $i++):?>
						<?$checked = $i==4? 'checked' : ''?>
						<input type="radio" name="<?=$arProp['CODE']?>" title="<?=$i?>" value="<?=$i?>" class="<?=$arProp['CLASS']?>" <?=$checked?>/>
					<?endfor?>
				</div>
			</div>
		<?elseif($arProp['PARAMS_TYPE'] == 'checkbox'):?>
			<div class="subscribe_me_block">
				<div class="check-box">
					<div class="line">
						<input id="subscribe_me" type="checkbox" name="<?=$arProp['CODE']?>" value="<?=$arProp['VALUE']?>">
						<label for="subscribe_me">Подписаться на новости и скидки</label>
					</div>
				</div>
			</div>
		<?endif;?>
	<?endforeach?>
	<div class="line">
		<a href="#" id="product_review_submit" class="btn btn-gray-dark  mode2">Отправить отзыв</a>
	</div>
	<div class="errors_cont"></div>
	<div class="success_cont"></div>
</form>