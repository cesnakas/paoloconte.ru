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
<form action="" method="post" name="fact_form_register">
	<div class="personal-box">
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				Имя *
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" class="required" name="NAME" placeholder="Иван">
			</div>
		</div>
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				Отчество
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" class="" name="SECOND_NAME" placeholder="Иванович">
			</div>
		</div>
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				Фамилия *
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" class="required" name="LAST_NAME" placeholder="Иванов">
			</div>
		</div>
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				Телефон *
			</div>
			<div class="emulate-cell input-cell">
				<input type="tel" class="required mask-phone" name="PHONE" placeholder="+7 (123) 456-7890">
			</div>
		</div>
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				E-mail *
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" class="required email" name="EMAIL" placeholder="ivan@mail.ru">
			</div>
		</div>
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				№ карты
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" class="" name="UF_CARDNUMBER">
			</div>
		</div>

	</div>


	<div class="personal-box">
		<div class="line-input">
			<div class="discount">
                    <span>
                        Если у Вас есть друг, который уже является клиентом Paolo Conte, Вы можете указать его e-mail и он получит бонус
                   </span>
			</div>
		</div>
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				E-mail Вашего друга
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" class="" placeholder="drug@mail.ru" name="UF_FRIEND_EMAIL">
			</div>
		</div>
	</div>



	<div class="personal-box">
		<?/*<div class="box-title">
			Пароль *
		</div>*/?>

		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				Пароль *
			</div>
			<div class="emulate-cell input-cell">
				<input type="password" placeholder="Введите пароль" class="required" name="PASSWORD">
			</div>
		</div>
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				Подтверждение пароля *
			</div>
			<div class="emulate-cell input-cell">
				<input type="password" placeholder="Повторите пароль" class="required" name="CONFIRM_PASSWORD">
			</div>
		</div>

		<div class="line-input line">
			<div class="check-box">
				<div class="line low">
					<input id="a1" type="checkbox" name="subscribe" value="Y" checked="checked">
					<label for="a1">
						Я хочу первым узнавать о закрытых клубных распродажах и новых акциях
					</label>
				</div>
			</div>
		</div>
        <?if($arResult["CAPTCHA_CODE"]) {?>
            <div class="line-input line emulate-table">
                <div class="emulate-cell name-cell">
                    <input name="captcha_code" data-capcha-hidden value="<?=$arResult["CAPTCHA_CODE"];?>" type="hidden">
                    <img data-capcha-image="/bitrix/tools/captcha.php?captcha_code=" src="/bitrix/tools/captcha.php?captcha_code=<?=$arResult["CAPTCHA_CODE"];?>">
                </div>
                <div class="emulate-cell input-cell">
                    <input id="captcha_word" name="captcha_word" type="text">
                </div>
            </div>
        <?}?>
		<div class="line-input">
            <div class="line-input line">
                <div class="check-box">
                    <div class="line low">

                        <input id="PERSONAL_DATA" type="checkbox" name="subscribe" value="Y" class="PERSONAL_DATA required" checked>
                        <label for="PERSONAL_DATA" id="label_PERSONAL_DATA" style="color: rgb(57, 53, 54)">
                                <?$APPLICATION->IncludeFile(
                                    SITE_DIR."/include/oferta_checkbox_mini.php",
                                    Array(),
                                    Array("MODE"=>"text")
                                );?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="valign-middle">
				<a href="#" id="fact_register_submit" class="btn btn-gray-dark big full mode2 icon-arrow-right">Зарегистрироваться</a>
			</div>

			<div class="valign-middle soc-text">
				Войти через социальные сети
			</div>

			<div class="valign-middle">
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

		<div class="errors_cont"></div>
		<div class="result_cont"></div>

	</div>
</form>

