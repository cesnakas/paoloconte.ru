<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?$this->setFrameMode(true);?>
<script>
	BX.message({
		TEMPLATE_PATH: '<? echo $this->__folder ?>',
		COMPONENT_PATH: '<? echo $this->__component->__path ?>',
		arParams_callback: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>
<form action="#" id="citfact_form_ajax_callback">
	<?=bitrix_sessid_post()?>
	<input type="text" name="yarobot" value="" class="hide">

	<?foreach ($arResult['SHOW_PROPERTIES'] as $arProp):?>
		<div class="line">
			<?if ($arProp['PARAMS_TYPE'] == 'text'):?>
				<input type="text" name="<?=$arProp['CODE']?>" class="style2 <?=$arProp['REQUIRED'] == 'Y'? 'required':''?> <?=$arProp['CODE'] == 'USERPHONE'? 'mask-phone':''?>" placeholder="<?=$arProp['PLACEHOLDER']?>">
			<?elseif($arProp['PARAMS_TYPE'] == 'textarea'):?>
				<textarea name="<?=$arProp['CODE']?>" cols="30" rows="10" class="style2 <?=$arProp['REQUIRED'] == 'Y'? 'required':''?>" placeholder="<?=$arProp['PLACEHOLDER']?>"></textarea>
			<?endif;?>
		</div>
	<?endforeach?>

	<div class="line">
		<a href="#" id="callback_submit" class="btn full btn-gray-dark  mode2 icon-arrow-right" onclick="yaCounter209275.reachGoal('Back_call');">Заказать звонок</a>
	</div>
	<div class="errors_cont"></div>
	<div class="success_cont"></div>
</form>
<?//\Citfact\Tools::pre($arResult['SHOW_PROPERTIES']);?>
