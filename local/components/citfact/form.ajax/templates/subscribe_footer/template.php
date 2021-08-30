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
		arParams_subscribe_footer: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>
<form action="#" class="footer-form" id="citfact_form_subscribe_footer">
	<?=bitrix_sessid_post()?>
	<input type="text" name="yarobot" value="" class="hide">
    
    <div class="footer-form__label">
        Узнавайте первыми
        о наших новинках и скидках!
    </div>
    <div class="footer-form__inner">
        <?foreach ($arResult['SHOW_PROPERTIES'] as $arProp):?>
            <?if ($arProp['PARAMS_TYPE'] == 'text'):?>
                <input type="text" name="<?=$arProp['CODE']?>" class="<?=$arProp['REQUIRED'] == 'Y'? 'required':''?>" placeholder="<?=$arProp['PLACEHOLDER']?>">
                <?if ($arProp['CODE'] == 'EMAIL'):?>
                    <div class="errors_cont"></div>
                    <div class="success_cont"></div>
                <?endif;?>
            <?endif?>
        <?endforeach?>
        <input type="hidden" name="HASH" value="<?= $this->randString(10); ?>" />

        <a href="#" id="subscribe_footer_submit" class="btn btn--black"><span>Подписаться</span></a>
    </div>
</form>
