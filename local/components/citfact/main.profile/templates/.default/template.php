<?
/**
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<script>
	//BX.addCustomEvent("onAjaxSuccess", Application.Components.Main.setMaskedInputs());
	$("select").select2();
</script>
<form method="post" name="form1" class="form" action="<?=$arResult["FORM_TARGET"]?>" enctype="multipart/form-data">
	<?=$arResult["BX_SESSION_CHECK"]?>
	<input type="hidden" name="lang" value="<?=LANG?>" />
	<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />

	<input type="hidden" name="LOGIN" maxlength="50" value="<? echo $arResult["arUser"]["LOGIN"]?>" />
	<input type="hidden" name="EMAIL" maxlength="50" value="<? echo $arResult["arUser"]["EMAIL"]?>" />

        
    <?if ($arResult["strProfileError"] != ''):?>
        <div class="errors_cont">
            <p><?=$arResult["strProfileError"]?></p>
        </div>
    <?endif?>
    <?if ($arResult['DATA_SAVED'] == 'Y'):?>
        <div class="success_cont">
            <p><?=GetMessage('PROFILE_DATA_SAVED');?></p>
        </div>
    <?endif?>
    
    <div class="title-4">
        Личные данные
    </div>
    
    <div class="form__item">
        <label for="NAME" class="form__label">
            Имя *
        </label>
        <input type="text" name="NAME" id="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"]?>" class="required" />
        <?/*<i class="label-icon label-alert" data-text="Обязательное поле"></i>*/?>
    </div>
    <div class="form__item">
        <label for="SECOND_NAME" class="form__label">
            Отчество
        </label>
        <input type="text" name="SECOND_NAME"  id="SECOND_NAME" maxlength="50" value="<?=$arResult["arUser"]["SECOND_NAME"]?>" class="" />
        <?/*<i class="label-icon label-alert" data-text="Обязательное поле"></i>*/?>
    </div>
    <div class="form__item">
        <label for="LAST_NAME" class="form__label">
            Фамилия *
        </label>
        <input type="text" name="LAST_NAME" id="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"]?>" class="required" />
        <?/*<i class="label-icon label-alert" data-text="Обязательное поле"></i>*/?>
    </div>
    <div class="form__item">
        <label for="PERSONAL_PHONE" class="form__label">
            Телефон *
        </label>
        <input type="tel" name="PERSONAL_PHONE" id="PERSONAL_PHONE" maxlength="50" value="<?=$arResult["arUser"]["PERSONAL_PHONE"]?>" class="required mask-phone <?/*error*/?>" placeholder="+7 (123) 456-7890"/>
        <?/*<i class="label-icon label-alert" data-text="Обязательное поле"></i>*/?>
    </div>
    <div class="form__item">
        <div class="form__label">
            E-mail
        </div>
        <?=$arResult["arUser"]["EMAIL"]?>
        <?/*<i class="label-icon label-alert" data-text="Обязательное поле"></i>*/?>
    </div>
    <div class="form__item">
        <label for="PERSONAL_BIRTHDAY" class="form__label">
            Дата рождения
        </label>
        <input type="text" name="PERSONAL_BIRTHDAY" id="PERSONAL_BIRTHDAY" maxlength="20" value="<?=$arResult["arUser"]["PERSONAL_BIRTHDAY"]?>" class="required <?/*error*/?> dateField"  placeholder="дд.мм.гггг" />
    </div>
    <div class="form__item">
        <div class="form__label">
            Пол
        </div>
        <select name="PERSONAL_GENDER">
            <option value=""><?=GetMessage("USER_DONT_KNOW")?></option>
            <option value="M"<?=$arResult["arUser"]["PERSONAL_GENDER"] == "M" ? " SELECTED=\"SELECTED\"" : ""?>><?=GetMessage("USER_MALE")?></option>
            <option value="F"<?=$arResult["arUser"]["PERSONAL_GENDER"] == "F" ? " SELECTED=\"SELECTED\"" : ""?>><?=GetMessage("USER_FEMALE")?></option>
        </select>
    </div>
    <div class="form__item">
        <label for="UF_CARDNUMBER" class="form__label">
            Номер карты
            <i class="label-icon label-help" data-text="12-значный номер дисконтной карты, указан на обратной стороне карты"></i>
        </label>
        <?if(empty($arResult["USER_PROPERTIES"]['DATA']['UF_LOYALTY_CARD']['VALUE'])):?>
        <input
                class="mask-card"
                type="text"
                name="UF_CARDNUMBER"
                id="UF_CARDNUMBER"
                maxlength="20"
                value="<?
                if ($arResult["USER_PROPERTIES"]['DATA']['UF_USE_LOYALTY_CARD']['VALUE']) {
                    echo $arResult["USER_PROPERTIES"]['DATA']['UF_LOYALTY_CARD']['VALUE'];
                }
                ?>"
                class="<?/*required mask-date*/?>" <?/*placeholder="дд.мм.гггг"*/?>
        />
        <?else:?>
            <?echo $arResult["USER_PROPERTIES"]['DATA']['UF_LOYALTY_CARD']['VALUE'];?>
        <?endif;?>
    </div>
    
    <div class="title-4">
        Пароль
    </div>

    <div class="form__item">
        <label for="NEW_PASSWORD" class="form__label">
            Новый пароль
        </label>
        <input type="password" name="NEW_PASSWORD" id="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" class="bx-auth-input" placeholder="Введите новый пароль" />
    </div>

    <div class="form__item">
        <label for="NEW_PASSWORD_CONFIRM" class="form__label">
            Подтверждение пароля
        </label>
        <input type="password" name="NEW_PASSWORD_CONFIRM" id="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" placeholder="Повторите новый пароль" />
    </div>

    <?/*<div class="line-input emulate-table">
        <div class="emulate-cell name-cell">
        </div>
        <div class="emulate-cell input-cell">
            <?//<a href="#" class="btn btn-gray-dark big full mode2 icon-arrow-right">Сохранить настройки профиля</a>?>
            <input class="btn btn-gray-dark big full mode2 icon-arrow-right" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? 'Сохранить настройки профиля' : GetMessage("MAIN_ADD"))?>">

            <?if ($arResult["strProfileError"] != ''):?>
                <div class="errors_cont">
                    <p><?=$arResult["strProfileError"]?></p>
                </div>
            <?endif?>
            <?if ($arResult['DATA_SAVED'] == 'Y'):?>
                <div class="success_cont">
                    <p><?=GetMessage('PROFILE_DATA_SAVED');?></p>
                </div>
            <?endif?>
        </div>
    </div>*/?>
    
    <div class="title-4">Адреса доставки</div>
    
    <?if (!empty($arResult['ADDRESSES'])):?>
        <?foreach ($arResult['ADDRESSES'] as $key => $arAddress):?>
            <div class="address-cont">
                <div class="line radio-box">
                    <input id="ch1-<?=$key?>" type="radio" name="ch1" value="1" <?=$arAddress['SELECTED'] == 1? 'checked':''?> data-element-id="<?=$arAddress['ID']?>">
                    <label for="ch1-<?=$key?>">
                        <?=$arAddress['LOCATION_NAME']?>, <?=$arAddress['ADDRESS']?><a href="#" class="desc editAddress" data-toggle="modal" data-target="#editAddressModal">Изменить</a><a href="#" class="desc delete-address" data-element-id="<?=$arAddress['ID']?>">Удалить</a>
                    </label>
                </div>

                <div class="edit-address-cont" style="display: none;">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:sale.location.selector.steps",
                        "",
                        Array(
                            "COMPONENT_TEMPLATE" => ".default",
                            "ID" => $arAddress['LOCATION_ID'],
                            "CODE" => "",
                            "INPUT_NAME" => "LOCATION",
                            "PROVIDE_LINK_BY" => "id",
                            "JSCONTROL_GLOBAL_ID" => "",
                            "JS_CALLBACK" => "",
                            "SEARCH_BY_PRIMARY" => "N",
                            "FILTER_BY_SITE" => "Y",
                            "SHOW_DEFAULT_LOCATIONS" => "Y",
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "36000000",
                            "FILTER_SITE_ID" => "s1"
                        )
                    );?>
                    <div class="form__item">
                        <label for="address" class="form__label">Улица, дом, квартира</label>
                        <input type="text" name="address" value="<?=$arAddress['ADDRESS']?>" placeholder=""/>
                    </div>
                    <a href="#" class="btn btn--black save-link" data-element-id="<?=$arAddress['ID']?>" style="margin:15px 0;">Сохранить адрес</a>
                </div>
            </div>
        <?endforeach;?>
    <?endif?>

	<div class="personal-box">
		<div>
			<div class="line">
				<a href="#" class="btn btn--black addAddress">Добавить адрес</a>
			</div>

			<div class="add-address-cont" style="display: none;">
				<?$APPLICATION->IncludeComponent(
					"bitrix:sale.location.selector.steps",
					"",
					Array(
						"COMPONENT_TEMPLATE" => ".default",
						"ID" => $arResult['GEO_LOCATION'],
						"CODE" => "",
						"INPUT_NAME" => "LOCATION",
						"PROVIDE_LINK_BY" => "id",
						"JSCONTROL_GLOBAL_ID" => "",
						"JS_CALLBACK" => "",
						"SEARCH_BY_PRIMARY" => "N",
						"FILTER_BY_SITE" => "Y",
						"SHOW_DEFAULT_LOCATIONS" => "Y",
						"CACHE_TYPE" => "A",
						"CACHE_TIME" => "36000000",
						"FILTER_SITE_ID" => "s1"
					)
				);?>
                <div class="form__item">
                    <label for="inp_address" class="form__label">Улица, дом, квартира</label>
                    <input type="text" name="address"  id="inp_address" value=""/>
                </div>
				<a href="#" class="btn btn--black save-link" style="margin-top: 15px;">Сохранить адрес</a>
			</div>

		</div>
	</div>

	<?/*<div class="personal-box">
		<div class="box-title">
			Паспортные данные <i class="label-icon label-help" data-text="Паспортные данные"></i>
		</div>

		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				Серия
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" name="UF_PASSPORT_SERIA" value="<?=$arResult["USER_PROPERTIES"]['DATA']['UF_PASSPORT_SERIA']['VALUE']?>" size="20">
			</div>
		</div>
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				Номер
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" name="UF_PASSPORT_NOMER" value="<?=$arResult["USER_PROPERTIES"]['DATA']['UF_PASSPORT_NOMER']['VALUE']?>" size="20">
			</div>
		</div>
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell valign-top">
				Выдан
			</div>
			<div class="emulate-cell input-cell">
				<textarea name="UF_PASSPORT_VYDAN"><?=$arResult["USER_PROPERTIES"]['DATA']['UF_PASSPORT_VYDAN']['VALUE']?></textarea>
			</div>
		</div>
	</div>
	<div class="personal-box">
		<div class="box-title">
			Банковские реквизиты <i class="label-icon label-help" data-text="Нужны для возврата денежных средств"></i>
		</div>

		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				БИК банка
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" name="UF_BANK_BIK" value="<?=$arResult["USER_PROPERTIES"]['DATA']['UF_BANK_BIK']['VALUE']?>" size="20">
			</div>
		</div>
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				Расчетный счет
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" name="UF_BANK_RS" value="<?=$arResult["USER_PROPERTIES"]['DATA']['UF_BANK_RS']['VALUE']?>" size="20">
			</div>
		</div>
		<div class="line-input emulate-table" data-description="* 16-значный номер. Находится на лицевой стороне карты.">
			<div class="emulate-cell name-cell">
				Номер карты
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" name="UF_BANK_CARDNUMBER" value="<?=$arResult["USER_PROPERTIES"]['DATA']['UF_BANK_CARDNUMBER']['VALUE']?>" size="20">
			</div>
		</div>
		<div class="line-input emulate-table" data-description="* 3-значный номер. Находится на обратной стороне карты.">
			<div class="emulate-cell name-cell">
				Идентификатор
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" name="UF_BANK_CVV" value="<?=$arResult["USER_PROPERTIES"]['DATA']['UF_BANK_CVV']['VALUE']?>" size="20">
			</div>
		</div>
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				Ф.И.О.
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" name="UF_BANK_FIO" value="<?=$arResult["USER_PROPERTIES"]['DATA']['UF_BANK_FIO']['VALUE']?>" size="20">
			</div>
		</div>
		<div class="line-input emulate-table">
			<div class="emulate-cell name-cell">
				Полное название банка
			</div>
			<div class="emulate-cell input-cell">
				<input type="text" name="UF_BANK_FULLNAME" value="<?=$arResult["USER_PROPERTIES"]['DATA']['UF_BANK_FULLNAME']['VALUE']?>" size="20">
			</div>
		</div>
	</div>*/?>
	<div class="personal-box" style="margin-top:15px;">
		<?/*<a href="#" class="btn btn-gray-dark big full mode2 icon-arrow-right">Сохранить настройки профиля</a>*/?>
		<input class="btn btn--black" type="submit" name="save" value="<?=(($arResult["ID"]>0) ? 'Сохранить настройки профиля' : GetMessage("MAIN_ADD"))?>">

		<?if ($arResult["strProfileError"] != ''):?>
			<div class="errors_cont">
				<p><?=$arResult["strProfileError"]?></p>
			</div>
		<?endif?>
		<?if ($arResult['DATA_SAVED'] == 'Y'):?>
			<div class="success_cont">
				<p><?=GetMessage('PROFILE_DATA_SAVED');?></p>
			</div>
		<?endif?>
	</div>
</form>

<?/*
<div class="bx-auth-profile">

<?ShowError($arResult["strProfileError"]);?>
<?
if ($arResult['DATA_SAVED'] == 'Y')
	ShowNote(GetMessage('PROFILE_DATA_SAVED'));
?>
<script type="text/javascript">
<!--
var opened_sections = [<?
$arResult["opened"] = $_COOKIE[$arResult["COOKIE_PREFIX"]."_user_profile_open"];
$arResult["opened"] = preg_replace("/[^a-z0-9_,]/i", "", $arResult["opened"]);
if (strlen($arResult["opened"]) > 0)
{
	echo "'".implode("', '", explode(",", $arResult["opened"]))."'";
}
else
{
	$arResult["opened"] = "reg";
	echo "'reg'";
}
?>];
//-->

var cookie_prefix = '<?=$arResult["COOKIE_PREFIX"]?>';
</script>
<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>" enctype="multipart/form-data">
<?=$arResult["BX_SESSION_CHECK"]?>
<input type="hidden" name="lang" value="<?=LANG?>" />
<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />

<div class="profile-link profile-user-div-link"><a title="<?=GetMessage("REG_SHOW_HIDE")?>" href="javascript:void(0)" onclick="SectionClick('reg')"><?=GetMessage("REG_SHOW_HIDE")?></a></div>
<div class="profile-block-<?=strpos($arResult["opened"], "reg") === false ? "hidden" : "shown"?>" id="user_div_reg">
<table class="profile-table data-table">
	<thead>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</thead>
	<tbody>
	<?
	if($arResult["ID"]>0)
	{
	?>
		<?
		if (strlen($arResult["arUser"]["TIMESTAMP_X"])>0)
		{
		?>
		<tr>
			<td><?=GetMessage('LAST_UPDATE')?></td>
			<td><?=$arResult["arUser"]["TIMESTAMP_X"]?></td>
		</tr>
		<?
		}
		?>
		<?
		if (strlen($arResult["arUser"]["LAST_LOGIN"])>0)
		{
		?>
		<tr>
			<td><?=GetMessage('LAST_LOGIN')?></td>
			<td><?=$arResult["arUser"]["LAST_LOGIN"]?></td>
		</tr>
		<?
		}
		?>
	<?
	}
	?>
	<tr>
		<td><?echo GetMessage("main_profile_title")?></td>
		<td><input type="text" name="TITLE" value="<?=$arResult["arUser"]["TITLE"]?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage('NAME')?></td>
		<td><input type="text" name="NAME" maxlength="50" value="<?=$arResult["arUser"]["NAME"]?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage('LAST_NAME')?></td>
		<td><input type="text" name="LAST_NAME" maxlength="50" value="<?=$arResult["arUser"]["LAST_NAME"]?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage('SECOND_NAME')?></font></td>
		<td><input type="text" name="SECOND_NAME" maxlength="50" value="<?=$arResult["arUser"]["SECOND_NAME"]?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage('EMAIL')?><?if($arResult["EMAIL_REQUIRED"]):?><span class="starrequired">*</span><?endif?></td>
		<td><input type="text" name="EMAIL" maxlength="50" value="<? echo $arResult["arUser"]["EMAIL"]?>" /></td>
	</tr>
	<tr>
		<td><?=GetMessage('LOGIN')?><span class="starrequired">*</span></td>
		<td><input type="text" name="LOGIN" maxlength="50" value="<? echo $arResult["arUser"]["LOGIN"]?>" /></td>
	</tr>
<?if($arResult["arUser"]["EXTERNAL_AUTH_ID"] == ''):?>
	<tr>
		<td><?=GetMessage('NEW_PASSWORD_REQ')?></td>
		<td><input type="password" name="NEW_PASSWORD" maxlength="50" value="" autocomplete="off" class="bx-auth-input" />
<?if($arResult["SECURE_AUTH"]):?>
				<span class="bx-auth-secure" id="bx_auth_secure" title="<?echo GetMessage("AUTH_SECURE_NOTE")?>" style="display:none">
					<div class="bx-auth-secure-icon"></div>
				</span>
				<noscript>
				<span class="bx-auth-secure" title="<?echo GetMessage("AUTH_NONSECURE_NOTE")?>">
					<div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
				</span>
				</noscript>
<script type="text/javascript">
document.getElementById('bx_auth_secure').style.display = 'inline-block';
</script>
		</td>
	</tr>
<?endif?>
	<tr>
		<td><?=GetMessage('NEW_PASSWORD_CONFIRM')?></td>
		<td><input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" value="" autocomplete="off" /></td>
	</tr>
<?endif?>
<?if($arResult["TIME_ZONE_ENABLED"] == true):?>
	<tr>
		<td colspan="2" class="profile-header"><?echo GetMessage("main_profile_time_zones")?></td>
	</tr>
	<tr>
		<td><?echo GetMessage("main_profile_time_zones_auto")?></td>
		<td>
			<select name="AUTO_TIME_ZONE" onchange="this.form.TIME_ZONE.disabled=(this.value != 'N')">
				<option value=""><?echo GetMessage("main_profile_time_zones_auto_def")?></option>
				<option value="Y"<?=($arResult["arUser"]["AUTO_TIME_ZONE"] == "Y"? ' SELECTED="SELECTED"' : '')?>><?echo GetMessage("main_profile_time_zones_auto_yes")?></option>
				<option value="N"<?=($arResult["arUser"]["AUTO_TIME_ZONE"] == "N"? ' SELECTED="SELECTED"' : '')?>><?echo GetMessage("main_profile_time_zones_auto_no")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td><?echo GetMessage("main_profile_time_zones_zones")?></td>
		<td>
			<select name="TIME_ZONE"<?if($arResult["arUser"]["AUTO_TIME_ZONE"] <> "N") echo ' disabled="disabled"'?>>
<?foreach($arResult["TIME_ZONE_LIST"] as $tz=>$tz_name):?>
				<option value="<?=htmlspecialcharsbx($tz)?>"<?=($arResult["arUser"]["TIME_ZONE"] == $tz? ' SELECTED="SELECTED"' : '')?>><?=htmlspecialcharsbx($tz_name)?></option>
<?endforeach?>
			</select>
		</td>
	</tr>
<?endif?>
	</tbody>
</table>
</div>
<div class="profile-link profile-user-div-link"><a title="<?=GetMessage("USER_SHOW_HIDE")?>" href="javascript:void(0)" onclick="SectionClick('personal')"><?=GetMessage("USER_PERSONAL_INFO")?></a></div>
<div id="user_div_personal" class="profile-block-<?=strpos($arResult["opened"], "personal") === false ? "hidden" : "shown"?>">
<table class="data-table profile-table">
	<thead>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?=GetMessage('USER_PROFESSION')?></td>
			<td><input type="text" name="PERSONAL_PROFESSION" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_PROFESSION"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_WWW')?></td>
			<td><input type="text" name="PERSONAL_WWW" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_WWW"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_ICQ')?></td>
			<td><input type="text" name="PERSONAL_ICQ" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_ICQ"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_GENDER')?></td>
			<td><select name="PERSONAL_GENDER">
				<option value=""><?=GetMessage("USER_DONT_KNOW")?></option>
				<option value="M"<?=$arResult["arUser"]["PERSONAL_GENDER"] == "M" ? " SELECTED=\"SELECTED\"" : ""?>><?=GetMessage("USER_MALE")?></option>
				<option value="F"<?=$arResult["arUser"]["PERSONAL_GENDER"] == "F" ? " SELECTED=\"SELECTED\"" : ""?>><?=GetMessage("USER_FEMALE")?></option>
			</select></td>
		</tr>
		<tr>
			<td><?=GetMessage("USER_BIRTHDAY_DT")?> (<?=$arResult["DATE_FORMAT"]?>):</td>
			<td><?
			$APPLICATION->IncludeComponent(
				'bitrix:main.calendar',
				'',
				array(
					'SHOW_INPUT' => 'Y',
					'FORM_NAME' => 'form1',
					'INPUT_NAME' => 'PERSONAL_BIRTHDAY',
					'INPUT_VALUE' => $arResult["arUser"]["PERSONAL_BIRTHDAY"],
					'SHOW_TIME' => 'N'
				),
				null,
				array('HIDE_ICONS' => 'Y')
			);

			//=CalendarDate("PERSONAL_BIRTHDAY", $arResult["arUser"]["PERSONAL_BIRTHDAY"], "form1", "15")
			?></td>
		</tr>
		<tr>
			<td><?=GetMessage("USER_PHOTO")?></td>
			<td>
			<?=$arResult["arUser"]["PERSONAL_PHOTO_INPUT"]?>
			<?
			if (strlen($arResult["arUser"]["PERSONAL_PHOTO"])>0)
			{
			?>
			<br />
				<?=$arResult["arUser"]["PERSONAL_PHOTO_HTML"]?>
			<?
			}
			?></td>
		<tr>
			<td colspan="2" class="profile-header"><?=GetMessage("USER_PHONES")?></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_PHONE')?></td>
			<td><input type="text" name="PERSONAL_PHONE" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_PHONE"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_FAX')?></td>
			<td><input type="text" name="PERSONAL_FAX" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_FAX"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_MOBILE')?></td>
			<td><input type="text" name="PERSONAL_MOBILE" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_MOBILE"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_PAGER')?></td>
			<td><input type="text" name="PERSONAL_PAGER" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_PAGER"]?>" /></td>
		</tr>
		<tr>
			<td colspan="2" class="profile-header"><?=GetMessage("USER_POST_ADDRESS")?></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_COUNTRY')?></td>
			<td><?=$arResult["COUNTRY_SELECT"]?></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_STATE')?></td>
			<td><input type="text" name="PERSONAL_STATE" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_STATE"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_CITY')?></td>
			<td><input type="text" name="PERSONAL_CITY" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_CITY"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_ZIP')?></td>
			<td><input type="text" name="PERSONAL_ZIP" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_ZIP"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage("USER_STREET")?></td>
			<td><textarea cols="30" rows="5" name="PERSONAL_STREET"><?=$arResult["arUser"]["PERSONAL_STREET"]?></textarea></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_MAILBOX')?></td>
			<td><input type="text" name="PERSONAL_MAILBOX" maxlength="255" value="<?=$arResult["arUser"]["PERSONAL_MAILBOX"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage("USER_NOTES")?></td>
			<td><textarea cols="30" rows="5" name="PERSONAL_NOTES"><?=$arResult["arUser"]["PERSONAL_NOTES"]?></textarea></td>
		</tr>
	</tbody>
</table>
</div>

<div class="profile-link profile-user-div-link"><a title="<?=GetMessage("USER_SHOW_HIDE")?>" href="javascript:void(0)" onclick="SectionClick('work')"><?=GetMessage("USER_WORK_INFO")?></a></div>
<div id="user_div_work" class="profile-block-<?=strpos($arResult["opened"], "work") === false ? "hidden" : "shown"?>">
<table class="data-table profile-table">
	<thead>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?=GetMessage('USER_COMPANY')?></td>
			<td><input type="text" name="WORK_COMPANY" maxlength="255" value="<?=$arResult["arUser"]["WORK_COMPANY"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_WWW')?></td>
			<td><input type="text" name="WORK_WWW" maxlength="255" value="<?=$arResult["arUser"]["WORK_WWW"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_DEPARTMENT')?></td>
			<td><input type="text" name="WORK_DEPARTMENT" maxlength="255" value="<?=$arResult["arUser"]["WORK_DEPARTMENT"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_POSITION')?></td>
			<td><input type="text" name="WORK_POSITION" maxlength="255" value="<?=$arResult["arUser"]["WORK_POSITION"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage("USER_WORK_PROFILE")?></td>
			<td><textarea cols="30" rows="5" name="WORK_PROFILE"><?=$arResult["arUser"]["WORK_PROFILE"]?></textarea></td>
		</tr>
		<tr>
			<td><?=GetMessage("USER_LOGO")?></td>
			<td>
			<?=$arResult["arUser"]["WORK_LOGO_INPUT"]?>
			<?
			if (strlen($arResult["arUser"]["WORK_LOGO"])>0)
			{
			?>
				<br /><?=$arResult["arUser"]["WORK_LOGO_HTML"]?>
			<?
			}
			?></td>
		</tr>
		<tr>
			<td colspan="2" class="profile-header"><?=GetMessage("USER_PHONES")?></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_PHONE')?></td>
			<td><input type="text" name="WORK_PHONE" maxlength="255" value="<?=$arResult["arUser"]["WORK_PHONE"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_FAX')?></font></td>
			<td><input type="text" name="WORK_FAX" maxlength="255" value="<?=$arResult["arUser"]["WORK_FAX"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_PAGER')?></font></td>
			<td><input type="text" name="WORK_PAGER" maxlength="255" value="<?=$arResult["arUser"]["WORK_PAGER"]?>" /></td>
		</tr>
		<tr>
			<td colspan="2" class="profile-header"><?=GetMessage("USER_POST_ADDRESS")?></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_COUNTRY')?></td>
			<td><?=$arResult["COUNTRY_SELECT_WORK"]?></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_STATE')?></td>
			<td><input type="text" name="WORK_STATE" maxlength="255" value="<?=$arResult["arUser"]["WORK_STATE"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_CITY')?></td>
			<td><input type="text" name="WORK_CITY" maxlength="255" value="<?=$arResult["arUser"]["WORK_CITY"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_ZIP')?></td>
			<td><input type="text" name="WORK_ZIP" maxlength="255" value="<?=$arResult["arUser"]["WORK_ZIP"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage("USER_STREET")?></td>
			<td><textarea cols="30" rows="5" name="WORK_STREET"><?=$arResult["arUser"]["WORK_STREET"]?></textarea></td>
		</tr>
		<tr>
			<td><?=GetMessage('USER_MAILBOX')?></td>
			<td><input type="text" name="WORK_MAILBOX" maxlength="255" value="<?=$arResult["arUser"]["WORK_MAILBOX"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage("USER_NOTES")?></td>
			<td><textarea cols="30" rows="5" name="WORK_NOTES"><?=$arResult["arUser"]["WORK_NOTES"]?></textarea></td>
		</tr>
	</tbody>
</table>
</div>
	<?
	if ($arResult["INCLUDE_FORUM"] == "Y")
	{
	?>

<div class="profile-link profile-user-div-link"><a title="<?=GetMessage("USER_SHOW_HIDE")?>" href="javascript:void(0)" onclick="SectionClick('forum')"><?=GetMessage("forum_INFO")?></a></div>
<div id="user_div_forum" class="profile-block-<?=strpos($arResult["opened"], "forum") === false ? "hidden" : "shown"?>">
<table class="data-table profile-table">
	<thead>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?=GetMessage("forum_SHOW_NAME")?></td>
			<td><input type="checkbox" name="forum_SHOW_NAME" value="Y" <?if ($arResult["arForumUser"]["SHOW_NAME"]=="Y") echo "checked=\"checked\"";?> /></td>
		</tr>
		<tr>
			<td><?=GetMessage('forum_DESCRIPTION')?></td>
			<td><input type="text" name="forum_DESCRIPTION" maxlength="255" value="<?=$arResult["arForumUser"]["DESCRIPTION"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('forum_INTERESTS')?></td>
			<td><textarea cols="30" rows="5" name="forum_INTERESTS"><?=$arResult["arForumUser"]["INTERESTS"]; ?></textarea></td>
		</tr>
		<tr>
			<td><?=GetMessage("forum_SIGNATURE")?></td>
			<td><textarea cols="30" rows="5" name="forum_SIGNATURE"><?=$arResult["arForumUser"]["SIGNATURE"]; ?></textarea></td>
		</tr>
		<tr>
			<td><?=GetMessage("forum_AVATAR")?></td>
			<td><?=$arResult["arForumUser"]["AVATAR_INPUT"]?>
			<?
			if (strlen($arResult["arForumUser"]["AVATAR"])>0)
			{
			?>
				<br /><?=$arResult["arForumUser"]["AVATAR_HTML"]?>
			<?
			}
			?></td>
		</tr>
	</tbody>
</table>
</div>

	<?
	}
	?>
	<?
	if ($arResult["INCLUDE_BLOG"] == "Y")
	{
	?>
<div class="profile-link profile-user-div-link"><a title="<?=GetMessage("USER_SHOW_HIDE")?>" href="javascript:void(0)" onclick="SectionClick('blog')"><?=GetMessage("blog_INFO")?></a></div>
<div id="user_div_blog" class="profile-block-<?=strpos($arResult["opened"], "blog") === false ? "hidden" : "shown"?>">
<table class="data-table profile-table">
	<thead>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?=GetMessage('blog_ALIAS')?></td>
			<td><input class="typeinput" type="text" name="blog_ALIAS" maxlength="255" value="<?=$arResult["arBlogUser"]["ALIAS"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('blog_DESCRIPTION')?></td>
			<td><input class="typeinput" type="text" name="blog_DESCRIPTION" maxlength="255" value="<?=$arResult["arBlogUser"]["DESCRIPTION"]?>" /></td>
		</tr>
		<tr>
			<td><?=GetMessage('blog_INTERESTS')?></td>
			<td><textarea cols="30" rows="5" class="typearea" name="blog_INTERESTS"><?echo $arResult["arBlogUser"]["INTERESTS"]; ?></textarea></td>
		</tr>
		<tr>
			<td><?=GetMessage("blog_AVATAR")?></td>
			<td><?=$arResult["arBlogUser"]["AVATAR_INPUT"]?>
			<?
			if (strlen($arResult["arBlogUser"]["AVATAR"])>0)
			{
			?>
				<br /><?=$arResult["arBlogUser"]["AVATAR_HTML"]?>
			<?
			}
			?></td>
		</tr>
	</tbody>
</table>
</div>
	<?
	}
	?>
	<?if ($arResult["INCLUDE_LEARNING"] == "Y"):?>
	<div class="profile-link profile-user-div-link"><a title="<?=GetMessage("USER_SHOW_HIDE")?>" href="javascript:void(0)" onclick="SectionClick('learning')"><?=GetMessage("learning_INFO")?></a></div>
	<div id="user_div_learning" class="profile-block-<?=strpos($arResult["opened"], "learning") === false ? "hidden" : "shown"?>">
	<table class="data-table profile-table">
		<thead>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?=GetMessage("learning_PUBLIC_PROFILE");?>:</td>
				<td><input type="checkbox" name="student_PUBLIC_PROFILE" value="Y" <?if ($arResult["arStudent"]["PUBLIC_PROFILE"]=="Y") echo "checked=\"checked\"";?> /></td>
			</tr>
			<tr>
				<td><?=GetMessage("learning_RESUME");?>:</td>
				<td><textarea cols="30" rows="5" name="student_RESUME"><?=$arResult["arStudent"]["RESUME"]; ?></textarea></td>
			</tr>

			<tr>
				<td><?=GetMessage("learning_TRANSCRIPT");?>:</td>
				<td><?=$arResult["arStudent"]["TRANSCRIPT"];?>-<?=$arResult["ID"]?></td>
			</tr>
		</tbody>
	</table>
	</div>
	<?endif;?>
	<?if($arResult["IS_ADMIN"]):?>
	<div class="profile-link profile-user-div-link"><a title="<?=GetMessage("USER_SHOW_HIDE")?>" href="javascript:void(0)" onclick="SectionClick('admin')"><?=GetMessage("USER_ADMIN_NOTES")?></a></div>
	<div id="user_div_admin" class="profile-block-<?=strpos($arResult["opened"], "admin") === false ? "hidden" : "shown"?>">
	<table class="data-table profile-table">
		<thead>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?=GetMessage("USER_ADMIN_NOTES")?>:</td>
				<td><textarea cols="30" rows="5" name="ADMIN_NOTES"><?=$arResult["arUser"]["ADMIN_NOTES"]?></textarea></td>
			</tr>
		</tbody>
	</table>
	</div>
	<?endif;?>
	<?// ********************* User properties ***************************************************?>
	<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
	<div class="profile-link profile-user-div-link"><a title="<?=GetMessage("USER_SHOW_HIDE")?>" href="javascript:void(0)" onclick="SectionClick('user_properties')"><?=strlen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></a></div>
	<div id="user_div_user_properties" class="profile-block-<?=strpos($arResult["opened"], "user_properties") === false ? "hidden" : "shown"?>">
	<table class="data-table profile-table">
		<thead>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
		</thead>
		<tbody>
		<?$first = true;?>
		<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
		<tr><td class="field-name">
			<?if ($arUserField["MANDATORY"]=="Y"):?>
				<span class="starrequired">*</span>
			<?endif;?>
			<?=$arUserField["EDIT_FORM_LABEL"]?>:</td><td class="field-value">
				<?$APPLICATION->IncludeComponent(
					"bitrix:system.field.edit",
					$arUserField["USER_TYPE"]["USER_TYPE_ID"],
					array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y"));?></td></tr>
		<?endforeach;?>
		</tbody>
	</table>
	</div>
	<?endif;?>
	<?// ******************** /User properties ***************************************************?>
	<p><?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></p>
	<p><input type="submit" name="save" value="<?=(($arResult["ID"]>0) ? GetMessage("MAIN_SAVE") : GetMessage("MAIN_ADD"))?>">&nbsp;&nbsp;<input type="reset" value="<?=GetMessage('MAIN_RESET');?>"></p>
</form>

<?
if($arResult["SOCSERV_ENABLED"])
{
	$APPLICATION->IncludeComponent("bitrix:socserv.auth.split", ".default", array(
			"SHOW_PROFILES" => "Y",
			"ALLOW_DELETE" => "Y"
		),
		false
	);
}
?>
</div>
*/?>