<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
//$GLOBALS['APPLICATION']->AddHeadScript($templateFolder . '/script.js');
//$GLOBALS['APPLICATION']->SetAdditionalCSS($templateFolder . '/style.css');
?>

	<script>
		BX.message({
			TEMPLATE_PATH_<?=$arParams['FORM_ID']?>: '<? echo $this->__folder ?>',
			arParams_<?=$arParams['FORM_ID']?>: <?=CUtil::PhpToJSObject($arParams)?>
		});
	</script>
	<form name="fact_form_authorize" data-form-id="<?=$arParams['FORM_ID']?>" style="margin: 20px;">
		<div class="line">
			<input type="text" class="style2 required" name="EMAIL" placeholder="Введите e-mail">
		</div>

		<div class="line">
			<input type="password" class="style2 required" name="PASSWORD" placeholder="Введите пароль">
		</div>

		<div class="line">
			<a href="#" onclick="FactAjaxAuth($(this)); return false;" data-form-id="<?=$arParams['FORM_ID']?>" class="btn full btn-gray-dark  mode2 icon-arrow-right fact_authorize_submit">Войти</a>
		</div>

		<?/*app.loadPage('<?= "paoloconte_app/menu.php" ?>');*/?>

		<div class="result_cont"></div>
		<div class="errors_cont"></div>

		<?/*
			<div class="line clear-after soc-wrap">
				Войти через соц. сети
				<div class="social">
					<?$APPLICATION->IncludeComponent(
						"ulogin:auth",
						".default",
						array(
							"PROVIDERS" => "twitter,facebook,vkontakte,instagram",
							//"HIDDEN" => "other",
							"TYPE" => "buttons",
							"REDIRECT_PAGE" => '',
							"UNIQUE_EMAIL" => "Y",
							"SEND_MAIL" => "N",
							"GROUP_ID" => array(
								0 => "5",
							)
						),
						false
					);?>
				</div>
			</div>
		*/?>
	</form>

