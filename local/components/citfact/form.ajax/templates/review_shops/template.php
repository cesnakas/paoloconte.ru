<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
?>
<script>
	BX.message({
		TEMPLATE_PATH: '<? echo $this->__folder ?>',
		COMPONENT_PATH: '<? echo $this->__component->__path ?>',
		arParams_reviews_shops: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>
<form action="#" id="citfact_form_ajax_review_shops" class="form">
	<?=bitrix_sessid_post()?>
	<input type="text" name="yarobot" value="" class="hide">

	<?foreach ($arResult['SHOW_PROPERTIES'] as $arProp):?>
		<div class="form__item">
			<?if ($arProp['PARAMS_TYPE'] == 'text'):?>
				<input type="text" name="<?=$arProp['CODE']?>" class="<?=$arProp['REQUIRED'] == 'Y'? 'required':''?> <?=$arProp['CODE'] == 'USERPHONE'? 'mask-phone':''?>" placeholder="<?=$arProp['PLACEHOLDER']?>"/>
			<?elseif($arProp['PARAMS_TYPE'] == 'textarea'):?>
				<textarea name="<?=$arProp['CODE']?>" cols="30" rows="10" class="<?=$arProp['REQUIRED'] == 'Y'? 'required':''?>" placeholder="<?=$arProp['PLACEHOLDER']?>"></textarea>
			<?elseif ($arProp['PARAMS_TYPE'] == 'hidden'):?>
				<input type="hidden" name="<?=$arProp['CODE']?>" class="<?=$arProp['REQUIRED'] == 'Y'? 'required':''?>" value="<?=$arProp['VALUE']?>"/>
			<?endif;?>
		</div>
	<?endforeach?>

    <div class="modal-pp">
        <a href="#" id="review_shops_submit" class="btn btn--black">Отправить отзыв</a>
        <div class="modal-pp__text">
            <?$APPLICATION->IncludeFile(
                SITE_DIR."include/oferta_application.php",
                Array(),
                Array("MODE"=>"text")
            );?>

            <?/*$APPLICATION->IncludeFile(
                SITE_DIR."/include/oferta_review.php",
                Array(),
                Array("MODE"=>"text")
            );*/?>
        </div>
    </div>
    
	<div class="errors_cont"></div>
	<div class="success_cont"></div>
</form>
