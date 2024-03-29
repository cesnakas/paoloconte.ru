<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
//$GLOBALS['APPLICATION']->AddHeadScript($templateFolder . '/script.js');
//$GLOBALS['APPLICATION']->SetAdditionalCSS($templateFolder . '/style.css');
?>
<?$this->setFrameMode(true);?>
<script>
	BX.message({
		TEMPLATE_PATH_<?=$arParams['FORM_ID']?>: '<? echo $this->__folder ?>',
		arParams_<?=$arParams['FORM_ID']?>: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>
<form name="fact_form_authorize" data-form-id="<?=$arParams['FORM_ID']?>">
	<div class="line">
        <label for="EMAIL" class="form__label">E-mail или телефон</label>
		<input type="text" class="required" id="EMAIL" name="EMAIL">
	</div>

	<div class="line">
        <label for="PASSWORD" class="form__label">Пароль</label>
		<input type="password" class="required" id="PASSWORD" name="PASSWORD">
	</div>

    <a href="#" onclick="FactAjaxAuth($(this)); return false;" data-form-id="<?=$arParams['FORM_ID']?>" class="btn btn--black fact_authorize_submit">ВОЙТИ</a>

	<div class="result_cont"></div>
	<div class="errors_cont"></div>
    <div class="modal-body">
        <div class="line line--flex">
            <a href="/forgotpassword/" class="link">Забыли пароль?</a>
            <a href="/register/" class="link link--reg">Регистрация</a>
        </div>
    </div>
	<div class="soc-wrap">
		<div class="social">
            <? $frame = $this->createFrame()->begin(''); ?>
            <? if ($arResult["AUTH_SERVICES"]) { ?>
                <? $APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons",
                    array(
                        "AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
                        "SUFFIX" => "form",
                    ),
                    false,
                    array("HIDE_ICONS" => "Y")
                ); ?>
            <? } ?>
            <? $frame->end(); ?>

            <?/*$APPLICATION->IncludeComponent(
				"ulogin:auth",
				".default",
				array(
					"PROVIDERS" => "twitter,facebook,vkontakte,instagram,googleplus",
					//"HIDDEN" => "other",
					"TYPE" => "buttons",
					"REDIRECT_PAGE" => $arParams['REDIRECT_TO'],
					"UNIQUE_EMAIL" => "Y",
					"SEND_MAIL" => "N",
					"GROUP_ID" => array(
						0 => USERGROUP_TO_SOCIAL,
					)
				),
				false
			);*/?>
		</div>
	</div>
</form>