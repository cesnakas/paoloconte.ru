<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
//$GLOBALS['APPLICATION']->AddHeadScript($templateFolder . '/script.js');
//$GLOBALS['APPLICATION']->SetAdditionalCSS($templateFolder . '/style.css');
?>
<script>
	BX.message({
		TEMPLATE_PATH_CITFACT_REGISTER_AJAX: '<? echo $this->__folder ?>',
		arParams: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>
<form class="form" action="" method="post" name="fact_form_register">
    <div class="form__item">
        <label for="NAME" class="form__label">
            Имя *
        </label>
        <input type="text" class="required" name="NAME" id="NAME" placeholder="Иван">
        <i class="label-icon label-alert" data-text="Обязательное поле"></i>
    </div> 
    <div class="form__item">
        <label for="SECOND_NAME" class="form__label">
            Отчество
        </label>
        <input type="text" class="" name="SECOND_NAME" id="SECOND_NAME" placeholder="Иванович">
        <i class="label-icon label-alert" data-text="Обязательное поле"></i>
    </div>
    <div class="form__item">
        <label for="LAST_NAME" class="form__label">
            Фамилия *
        </label>
        <input type="text" class="required" name="LAST_NAME" id="LAST_NAME" placeholder="Иванов">
        <i class="label-icon label-alert" data-text="Обязательное поле"></i>
    </div>
    <div class="form__item">
        <label for="BIRTH_DATE" class="form__label">
            Дата рождения *
        </label>
        <input type="text" class="required datepicker-here dateField" data-position="bottom left" name="BIRTH_DATE" id="BIRTH_DATE" placeholder="дд.мм.гггг">
        <i class="label-icon label-alert" data-text="Обязательное поле"></i>
    </div>       
    <div class="form__item">
        <label for="PHONE" class="form__label">
            Телефон *
        </label>
        <input type="text" class="required mask-phone" name="PHONE" id="PHONE" placeholder="+7 (123) 456-7890">
        <i class="label-icon label-alert" data-text="Обязательное поле"></i>
    </div>
    <div class="form__item">
        <label for="EMAIL" class="form__label">
            E-mail *
        </label>
        <input type="text" class="required email" name="EMAIL" id="EMAIL" placeholder="ivan@mail.ru">
        <i class="label-icon label-alert" data-text="Обязательное поле"></i>
    </div>
    <div class="form__item">
        <label for="UF_CARDNUMBER" class="form__label">
            № бонусной карты paolo conte <span>(если есть)</span>
        </label>
        <input type="text" class="mask-card" name="UF_CARDNUMBER" id="UF_CARDNUMBER">
        <i class="label-icon label-alert" data-text="Обязательное поле"></i>
    </div>

    <? if($arParams['SHOW_FRIEND_EMAIL'] == 'Y') {?>
        <div class="b-register-discount">
            <div class="discount">
                <span>
                    Если у Вас есть друг, который уже является клиентом Paolo Conte, Вы можете указать его e-mail и он получит бонус
               </span>
            </div>
        </div>

        <div class="form__item">
            <label for="UF_FRIEND_EMAIL" class="form__label">
                E-mail Вашего друга
            </label>
            <input type="text" class="" placeholder="drug@mail.ru" name="UF_FRIEND_EMAIL" id="UF_FRIEND_EMAIL">
            <i class="label-icon label-alert" data-text="Обязательное поле"></i>
        </div>
    <? } ?>

    <div class="form__item">
        <label for="PASSWORD" class="form__label">
            Пароль *
        </label>
        <input type="password" placeholder="Введите пароль" class="required" name="PASSWORD" id="PASSWORD">
    </div>
    <div class="form__item">
        <label for="CONFIRM_PASSWORD" class="form__label">
            Подтверждение пароля *
        </label>
        <input type="password" placeholder="Повторите пароль" class="required" name="CONFIRM_PASSWORD" id="CONFIRM_PASSWORD">
    </div>
<?if ($arResult["USE_CAPTCHA"] == "Y") {?>
    <div class="g-recaptcha" data-sitekey="<?=GoogleReCaptcha::getPublicKey()?>"></div>    
<?}?>        

    <div class="b-checkbox">
        <label class="b-checkbox__label">
            <input id="a1" type="checkbox" name="subscribe" value="Y" checked="checked" class="b-checkbox__input">
            <span class="b-checkbox__box">
                    <span class="b-checkbox__line b-checkbox__line--short"></span>
                    <span class="b-checkbox__line b-checkbox__line--long"></span>
                </span>
            <span class="b-checkbox__text">Я хочу первым узнавать о закрытых клубных распродажах и новых акциях</span>
        </label>
    </div>
    
    <?/*if($arResult["CAPTCHA_CODE"]) {?>
        <div class="b-register-line">
            <div class="emulate-cell name-cell">
                <input name="captcha_code" data-capcha-hidden value="<?=$arResult["CAPTCHA_CODE"];?>" type="hidden">
                <img style="margin-right: 10px;" data-capcha-image="/bitrix/tools/captcha.php?captcha_code=" src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"];?>">
            </div>
            <div class="emulate-cell input-cell">
                <input id="captcha_word" name="captcha_word" type="text">
            </div>
        </div>
    <?}*/?>

    <div class="b-checkbox">
        <label class="b-checkbox__label">
            <input id="PERSONAL_DATA" type="checkbox" name="subscribe" value="Y" class="b-checkbox__input PERSONAL_DATA required" checked>
            <span class="b-checkbox__box">
                    <span class="b-checkbox__line b-checkbox__line--short"></span>
                    <span class="b-checkbox__line b-checkbox__line--long"></span>
                </span>
            <span class="b-checkbox__text">
                <?$APPLICATION->IncludeFile(
                //SITE_DIR."include/oferta_checkbox_mini.php",
                    SITE_DIR."include/oferta_application.php",
                    Array(),
                    Array("MODE"=>"text")
                );?>
            </span>
        </label>
    </div>
    
    

	<div class="b-register-bottom">

		<div class="line-input emulate-table">
			<div class="emulate-cell input-cell valign-middle">
				<a href="#" id="fact_register_submit" class="btn btn--black">Зарегистрироваться</a>
			</div>
			<div class="emulate-cell valign-middle soc-text">
				Войти через социальные сети
			</div>

			<div class="emulate-cell valign-middle">
				<div class="social float-right">
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
							"PROVIDERS" => "twitter,facebook,vkontakte,instagram",
							//"HIDDEN" => "other",
							"TYPE" => "buttons",
							"REDIRECT_PAGE" => $arParams['REDIRECT_TO'],
							"UNIQUE_EMAIL" => "Y",
							"SEND_MAIL" => "N",
							"GROUP_ID" => array(
								0 => "5",
							)
						),
						false
					);*/?>
				</div>
			</div>
		</div>

		<div class="reg_errors_cont"></div>
		<div class="reg_result_cont"></div>

	</div>
</form>
