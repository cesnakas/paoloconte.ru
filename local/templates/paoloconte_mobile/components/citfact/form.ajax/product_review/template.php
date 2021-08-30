<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
?>
<script type="text/javascript">
	BX.message({
		TEMPLATE_PATH_product_review: '<? echo $this->__folder ?>',
		COMPONENT_PATH_product_review: '<? echo $this->__component->__path ?>',
		arParams_product_review: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>

<form action="#">
	<?=bitrix_sessid_post()?>
	<input type="text" name="yarobot" value="" class="hide">
	<div class="line ready_props">
		<?foreach ($arResult['SHOW_PROPERTIES'] as $key => $arProp){
			if ($arProp['PARAMS_TYPE'] == 'ready' && !empty($arProp['VALUE'])){
				$idElement = !empty($arProp['ID'])? 'id="'.$arProp['ID'].'"': '';?>
				<div class=""><?=$arProp['VALUE'];?></div>
				<input <?=$idElement?> class="<?=$arProp['CLASS']?>" type="hidden" name="<?=$arProp['CODE']?>" value="<?=$arProp['VALUE']?>"/>
				<?unset($arResult['SHOW_PROPERTIES'][$key]);
			}elseif($arProp['PARAMS_TYPE'] == 'img' && !empty($arProp['VALUE'])){
				$idElement = !empty($arProp['ID'])? 'id="'.$arProp['ID'].'"': '';?>
				<img src="<?=$arProp['VALUE'];?>" class="" />
				<input <?=$idElement?> class="<?=$arProp['CLASS']?>" type="hidden" name="<?=$arProp['CODE']?>" value="<?=$arProp['VALUE']?>"/>
				<?unset($arResult['SHOW_PROPERTIES'][$key]);
			}
		}?>
	</div>
	<?foreach ($arResult['SHOW_PROPERTIES'] as $arProp):?>
		<?
		$idElement = !empty($arProp['ID'])? 'id="'.$arProp['ID'].'"': '';
		?>
		<?if ($arProp['PARAMS_TYPE'] == 'textarea'):?>
			<div class="line line_relative">
				<div class="show_error"><?=$arProp['ERROR']?></div>
				<div class="show_length"><span>0</span> <p>символов</p></div>
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
			<div class="line subscribe_me_block">
				<div class="check-box">
					<input id="subscribe_me" type="checkbox" checked="checked" name="<?=$arProp['CODE']?>" value="<?=$arProp['VALUE']?>">
					<label for="subscribe_me">Подписаться на новости об акциях и скидках</label>
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