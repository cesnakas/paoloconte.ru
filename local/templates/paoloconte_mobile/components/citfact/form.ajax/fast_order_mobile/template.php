<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
?>
<script>
	BX.message({
		TEMPLATE_PATH_fast_order: '<? echo $this->__folder ?>',
		COMPONENT_PATH_fast_order: '<? echo $this->__component->__path ?>',
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
			<?if ($arProp['PARAMS_TYPE'] == 'text' || $arProp['PARAMS_TYPE'] == 'tel'):?>
				<div class="line">
					<input <?=$idElement?> type="<?= $arProp['PARAMS_TYPE']; ?>" name="<?=$arProp['CODE']?>" class="style2 <?=$arProp['CLASS']?> <?=$required?> <?=$phonemask?>" placeholder="<?=$arProp['PLACEHOLDER']?>">
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
        <div class="oferta">
            <?$APPLICATION->IncludeFile(
                SITE_DIR."/include/oferta_order.php",
                Array(),
                Array("MODE"=>"text")
            );?>
        </div>
	</form>
</div>
