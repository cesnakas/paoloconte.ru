<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<script>
    BX.message({
        TEMPLATE_PATH_subscribe_price: '<? echo $this->__folder ?>',
        COMPONENT_PATH_subscribe_price: '<? echo $this->__component->__path ?>',
        arParams_subscribe_price: <?=CUtil::PhpToJSObject($arParams)?>
    });
</script>

<div class="desc">
	Вы получите письмо, когда цена станет <span class="price">ниже указанной вами цены.</span>
</div>
<form action="#">
	<?=bitrix_sessid_post()?>
	<input type="text" name="yarobot" value="" class="hide">

	<div class="form-box">
		<?foreach ($arResult['SHOW_PROPERTIES'] as $arProp):?>
			<div class="line">
				<?if ($arProp['PARAMS_TYPE'] == 'text'):?>
					<input type="text" name="<?=$arProp['CODE']?>" class="style2 <?=$arProp['REQUIRED'] == 'Y'? 'required':''?> <?=$arProp['CODE'] == 'USERPHONE'? 'mask-phone':''?> <?=$arProp['CODE'] == 'EMAIL'? 'email':''?> <?=$arProp['CODE'] == 'PRICE'? 'mask-price':''?>" placeholder="<?=$arProp['PLACEHOLDER']?>"/>
				<?elseif($arProp['PARAMS_TYPE'] == 'textarea'):?>
					<textarea name="<?=$arProp['CODE']?>" cols="30" rows="10" class="style2 <?=$arProp['REQUIRED'] == 'Y'? 'required':''?>" placeholder="<?=$arProp['PLACEHOLDER']?>"></textarea>
				<?elseif ($arProp['PARAMS_TYPE'] == 'hidden'):?>
					<input type="hidden" name="<?=$arProp['CODE']?>" class="<?=$arProp['REQUIRED'] == 'Y'? 'required':''?>" value="<?=$arProp['VALUE']?>"/>
				<?endif;?>
			</div>
		<?endforeach?>

		<div class="subscribe_price_block">
			<div class="check-box">
				<div class="line">
					<input id="a1" type="checkbox" name="SUBSCRIBE" value="Y">
					<label for="a1">
						Подписка на новинки
					</label>
				</div>
			</div>
		</div>
		<div class="btn-box">
			<a href="#" id="subscribe_price_submit" class="btn btn-gray-dark">Отправить заявку</a>
		</div>
		<div class="errors_cont"></div>
		<div class="success_cont"></div>
        <div class="oferta">
            <?$APPLICATION->IncludeFile(
                SITE_DIR."/include/oferta_application.php",
                Array(),
                Array("MODE"=>"text")
            );?>
        </div>
	</div>
</form>