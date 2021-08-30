<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
//_c($arResult);
?>
<script>
	BX.message({
		TEMPLATE_PATH: '<? echo $this->__folder ?>',
		COMPONENT_PATH: '<? echo $this->__component->__path ?>',
		arParams_fast_order: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>

<div class="valign-bottom image">
	<img src="<?=$arParams['PRODUCT_IMAGE']?>">
</div>
<div class="valign-top form">
	<form action="#">
		<?=bitrix_sessid_post()?>
		<input type="text" name="yarobot" value="" class="hide">
		<?foreach ($arResult['SHOW_PROPERTIES'] as $arProp):?>
			<?
			$idElement = !empty($arProp['ID'])? 'id="'.$arProp['ID'].'"': '';
			?>
			<?if ($arProp['PARAMS_TYPE'] == 'text'):?>
				<div class="line">
					<input <?=$idElement?> type="text" name="<?=$arProp['CODE']?>" class="style2 <?=$arProp['CLASS']?> <?=$required?> <?=$phonemask?>" placeholder="<?=$arProp['PLACEHOLDER']?>">
				</div>
			<?elseif($arProp['PARAMS_TYPE'] == 'hidden'):?>
				<input <?=$idElement?> type="hidden" name="<?=$arProp['CODE']?>" value="<?=$arProp['VALUE']?>" class="<?=$arProp['CLASS']?> <?=$required?>"/>
			<?endif;?>
		<?endforeach?>
		<div class="line">
			<a href="#" id="fast_order_submit" class="btn full btn-gray-dark">Оформить заказ</a>
		</div>
		<div class="errors_cont"></div>
		<div class="success_cont"></div>
	</form>
</div>
