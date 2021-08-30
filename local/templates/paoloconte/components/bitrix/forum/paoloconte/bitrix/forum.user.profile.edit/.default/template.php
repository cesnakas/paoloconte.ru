<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?
if (!$this->__component->__parent || empty($this->__component->__parent->__name)):
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/themes/blue/style.css');
	$GLOBALS['APPLICATION']->SetAdditionalCSS('/bitrix/components/bitrix/forum/templates/.default/styles/additional.css');
endif;
$path = str_replace(array("\\", "//"), "/", dirname(__FILE__)."/interface.php");
include_once($path);
// *****************************************************************************************
if (!empty($arResult["ERROR_MESSAGE"])): 
?>
<div class="forum-note-box forum-note-error">
	<div class="forum-note-box-text"><?=ShowError($arResult["ERROR_MESSAGE"], "forum-note-error");?></div>
</div>
<?
endif;
if (!empty($arResult["OK_MESSAGE"])): 
?>
<div class="forum-note-box forum-note-success">
	<div class="forum-note-box-text"><?=ShowNote($arResult["OK_MESSAGE"], "forum-note-success")?></div>
</div>
<?
endif;
/*?>
<div class="forum-header-box">
	<div class="forum-header-options">
		<span class="forum-option-profile">
			<a href="<?=$arResult["profile_view"]?>"><?=GetMessage("F_PROFILE")?></a>
		</span>
	</div>
	<div class="forum-header-title"><span><?=GetMessage("F_CHANGE_PROFILE")?></span></div>
</div>
<?*/
?>
<div class="forum-header-box">
	<div class="forum-header-title"><span>Изменение профиля</span></div>
</div>
<form method="post" name="form1" action="<?=POST_FORM_ACTION_URI?>" enctype="multipart/form-data" class="forum-form border">
	<input type="hidden" name="PAGE_NAME" value="profile" />
	<input type="hidden" name="Update" value="Y" />
	<input type="hidden" name="UID" value="<?=$arParams["UID"]?>" />
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="ACTION" value="EDIT" />
<?

$aTabs = array(
	array("DIV" => "forum_1", "TAB" => GetMessage("F_REG_INFO"), "TITLE" => GetMessage("F_REG_INFO")), 
	array("DIV" => "forum_2", "TAB" => GetMessage("F_PRIVATE_INFO"), "TITLE" => GetMessage("F_PRIVATE_INFO")),
	array("DIV" => "forum_3", "TAB" => GetMessage("F_WORK_INFO"), "TITLE" => GetMessage("F_WORK_INFO")),
	array("DIV" => "forum_4", "TAB" => GetMessage("F_FORUM_PROFILE"), "TITLE" => GetMessage("F_FORUM_PROFILE")),
);

// ********************* User properties ***************************************************
if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):
	$aTabs[] = array("DIV" => "forum_5", "TAB" => strLen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB"), "TITLE" => strLen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB"));
endif;
// ******************** /User properties ***************************************************

$tabControl = new CForumTabControl("forum_user", $aTabs);
?>
<?=$tabControl->Begin();?>
<?=$tabControl->BeginNextTab();?>
	<tr>
		<th>
			<div class="name-cell2">
			<?=GetMessage("F_NAME")?>
			</div>
		</th>
		<td><input type="text" name="NAME" size="40" maxlength="50" value="<?=$arResult["str_NAME"]?>"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_LAST_NAME")?></div></th>
		<td><input type="text" name="LAST_NAME" size="40" maxlength="50" value="<?=$arResult["str_LAST_NAME"]?>"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2">E-Mail <span class="starrequired">*</span></div></th>
		<td><input type="text" name="EMAIL" size="40" maxlength="50" value="<?=$arResult["str_EMAIL"]?>"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_LOGIN")?><span class="starrequired">*</span></div></th>
		<td><input type="text" name="LOGIN" size="30" maxlength="50" value="<?=$arResult["str_LOGIN"]?>"/><input type="hidden" name="OLD_LOGIN" value="<?=$arResult["str_LOGIN"]?>"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_NEW_PASSWORD")?></div></th>
		<td><input type="password" name="NEW_PASSWORD" size="20" maxlength="50" value="<?=$arResult["NEW_PASSWORD"]?>" autocomplete="off" placeholder="Введите новый пароль"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_PASSWORD_CONFIRM")?></div></th>
		<td><input type="password" name="NEW_PASSWORD_CONFIRM" size="20" maxlength="50" value="<?=$arResult["NEW_PASSWORD_CONFIRM"]?>" autocomplete="off" placeholder="Повторите новый пароль" /></td>
	</tr>
<?if($arResult["TIME_ZONE_ENABLED"] == true):?>
	<tr class="header">
		<th colspan="2"><b><?echo GetMessage("forum_profile_time_zones")?></b></th></tr>
	<tr>
	<tr>
		<th><?echo GetMessage("forum_profile_time_zones_auto")?></th>
		<td>
			<select name="AUTO_TIME_ZONE" onchange="this.form.TIME_ZONE.disabled=(this.value != 'N')">
				<option value=""><?echo GetMessage("forum_profile_time_zones_auto_def")?></option>
				<option value="Y"<?=($arResult["str_AUTO_TIME_ZONE"] == "Y"? ' SELECTED="SELECTED"' : '')?>><?echo GetMessage("forum_profile_time_zones_auto_yes")?></option>
				<option value="N"<?=($arResult["str_AUTO_TIME_ZONE"] == "N"? ' SELECTED="SELECTED"' : '')?>><?echo GetMessage("forum_profile_time_zones_auto_no")?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th><?echo GetMessage("forum_profile_time_zones_zones")?></th>
		<td>
			<select name="TIME_ZONE"<?if($arResult["str_AUTO_TIME_ZONE"] <> "N") echo ' disabled="disabled"'?>>
<?foreach($arResult["TIME_ZONE_LIST"] as $tz=>$tz_name):?>
				<option value="<?=htmlspecialcharsbx($tz)?>"<?=($arResult["str_TIME_ZONE"] == $tz? ' SELECTED="SELECTED"' : '')?>><?=htmlspecialcharsbx($tz_name)?></option>
<?endforeach?>
			</select>
		</td>
	</tr>
<?endif?>
	<tr><th colspan="2"><span class="starrequired">*</span> <?=GetMessage("F_REQUIED_FILEDS")?></th></tr>
<?=$tabControl->BeginNextTab();?>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_PROFESSION")?></div></th>
		<td><input type="text" name="PERSONAL_PROFESSION" size="45" maxlength="255" value="<?=$arResult["str_PERSONAL_PROFESSION"]?>"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_WWW_PAGE")?></div></th>
		<td><input type="text" name="PERSONAL_WWW" size="45" maxlength="255" value="<?=$arResult["str_PERSONAL_WWW"]?>" /></td>
	</tr>
	<tr>
		<th><div class="name-cell2">ICQ</div></th>
		<td><input type="text" name="PERSONAL_ICQ" size="45" maxlength="255" value="<?=$arResult["str_PERSONAL_ICQ"]?>"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_SEX")?></div></th>
		<td>
			<select name="PERSONAL_GENDER" id="PERSONAL_GENDER">
			<option value=""><?=GetMessage("F_SEX_NONE")?></option>
			<?if (is_array($arResult["arr_PERSONAL_GENDER"]["data"]) && !empty($arResult["arr_PERSONAL_GENDER"]["data"])):?>
				<?foreach ($arResult["arr_PERSONAL_GENDER"]["data"] as $value => $option):?>
			<option value="<?=$value?>" <?=(($arResult["arr_PERSONAL_GENDER"]["active"] == $value) ? "selected" : "")?>><?=$option?></option>
				<?endforeach?>
			<?endif;?>
			</select>
		</td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_BIRTHDATE")?> (<?=CLang::GetDateFormat("SHORT")?>)</div></th>
		<td><?
			$APPLICATION->IncludeComponent(
				"bitrix:main.calendar", 
				"", 
				array(
					"SHOW_INPUT" => "Y", 
					"FORM_NAME" => "form1",
					"INPUT_NAME" => "PERSONAL_BIRTHDAY",
					"INPUT_VALUE" => $arResult["~str_PERSONAL_BIRTHDAY"]),
				$component,
				array("HIDE_ICONS" => "Y"));
		?></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_PHOTO")?></div></th>
		<td><input name="PERSONAL_PHOTO" size="30" type="file" />
			<?if ($arResult["SHOW_DELETE_PERSONAL_PHOTO"] == "Y"):?>
			<br />
			<div class="check-box">
				<input type="checkbox" name="PERSONAL_PHOTO_del" value="Y" id="PERSONAL_PHOTO_del" />
				<label class="label" for="PERSONAL_PHOTO_del"><?=GetMessage("FILE_DELETE")?></label>
			</div>
			<br/>
				<?=$arResult["str_PERSONAL_PHOTO_IMG"]?>
			<?endif;?>
		</td>
	</tr>
	<tr class="header">
		<th colspan="2"><div class="name-cell2"><b><?=GetMessage("F_LOCATION")?></b></div></th></tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_COUNTRY")?></div></th>
		<td>
			<select name="PERSONAL_COUNTRY" id="PERSONAL_COUNTRY">
			<option value=""><?=GetMessage("F_COUNTRY_NONE")?></option>
			<?if (is_array($arResult["arr_PERSONAL_COUNTRY"]["data"]) && !empty($arResult["arr_PERSONAL_COUNTRY"]["data"])):?>
				<?foreach ($arResult["arr_PERSONAL_COUNTRY"]["data"] as $value => $option):?>
			<option value="<?=$value?>" <?=(($arResult["arr_PERSONAL_COUNTRY"]["active"] == $value) ? "selected" : "")?>><?=$option?></option>
				<?endforeach?>
			<?endif;?>
			</select>
		</td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_REGION")?></div></th>
		<td><input type="text" name="PERSONAL_STATE" size="45" maxlength="255" value="<?=$arResult["str_PERSONAL_STATE"]?>"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_CITY")?></div></th>
		<td><input type="text" name="PERSONAL_CITY" size="45" maxlength="255" value="<?=$arResult["str_PERSONAL_CITY"]?>"/></td>
	</tr>
<?=$tabControl->BeginNextTab();?>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_COMPANY_NAME")?></div></th>
		<td><input type="text" name="WORK_COMPANY" size="45" maxlength="255" value="<?=$arResult["str_WORK_COMPANY"]?>"/></td>
	</tr>		
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_WWW_PAGE")?></div></th>
		<td><input type="text" name="WORK_WWW" size="45" maxlength="255" value="<?=$arResult["str_WORK_WWW"]?>"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_COMPANY_DEPARTMENT")?></div></th>
		<td><input type="text" name="WORK_DEPARTMENT" size="45" maxlength="255" value="<?=$arResult["str_WORK_DEPARTMENT"]?>"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_COMPANY_ROLE")?></div></th>
		<td><input type="text" name="WORK_POSITION" size="45" maxlength="255" value="<?=$arResult["str_WORK_POSITION"]?>"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_COMPANY_ACT")?></div></th>
		<td><textarea name="WORK_PROFILE" cols="35" rows="5"><?=$arResult["str_WORK_PROFILE"]?></textarea></td>
	</tr>
	<tr class="header">
		<th colspan="2"><div class="name-cell2"><b><?=GetMessage("F_COMPANY_LOCATION")?></b></div></th>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_COUNTRY")?></div></th>
		<td>
			<select name="WORK_COUNTRY" id="WORK_COUNTRY">
			<option value=""><?=GetMessage("F_COUNTRY_NONE")?></option>
			<?if (is_array($arResult["arr_WORK_COUNTRY"]["data"]) && !empty($arResult["arr_WORK_COUNTRY"]["data"])):?>
				<?foreach ($arResult["arr_WORK_COUNTRY"]["data"] as $value => $option):?>
			<option value="<?=$value?>" <?=(($arResult["arr_WORK_COUNTRY"]["active"] == $value) ? "selected" : "")?>><?=$option?></option>
				<?endforeach?>
			<?endif;?>
			</select>
		</td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_REGION")?></div></th>
		<td><input type="text" name="WORK_STATE" size="45" maxlength="255" value="<?=$arResult["str_WORK_STATE"]?>"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_CITY")?></div></th>
		<td><input type="text" name="WORK_CITY" size="45" maxlength="255" value="<?=$arResult["str_WORK_CITY"]?>"/></td>
	</tr>
<?=$tabControl->BeginNextTab();?>
	<tr><th><div class="name-cell2"><?=GetMessage("F_SETTINGS")?></div></th>
		<td>
	<?if (CForumUser::IsAdmin()):?>
	<div class="check-box">
		<input type="checkbox" name="FORUM_ALLOW_POST" id="FORUM_ALLOW_POST" value="Y" <?
			if ($arResult["str_FORUM_ALLOW_POST"] == "Y"):
				?> checked="checked" <?
			endif;
		?> /> <label class="label" for="FORUM_ALLOW_POST"><?=GetMessage("F_ALLOW_POST")?></label>
	</div>

	<?endif;?>
		<div class="check-box">
			<input type="checkbox" name="FORUM_SHOW_NAME" id="FORUM_SHOW_NAME" value="Y" <?
				if ($arResult["str_FORUM_SHOW_NAME"] == "Y"):
						?> checked="checked" <?
				endif;
			?> /> <label class="label" for="FORUM_SHOW_NAME"><?=GetMessage("F_SHOW_NAME")?></label>
		</div>	

		<div class="check-box">
			<input type="checkbox" name="FORUM_HIDE_FROM_ONLINE" id="FORUM_HIDE_FROM_ONLINE" value="Y" <?
				if ($arResult["str_FORUM_HIDE_FROM_ONLINE"] == "Y"):
					?> checked="checked" <?
				endif;
			?> /> <label class="label" for="FORUM_HIDE_FROM_ONLINE"><?=GetMessage("F_HIDE_FROM_ONLINE")?></label>
		</div>	

		<div class="check-box">
			<input type="checkbox" name="FORUM_SUBSC_GET_MY_MESSAGE" id="FORUM_SUBSC_GET_MY_MESSAGE" value="Y" <?
			if ($arResult["str_FORUM_SUBSC_GET_MY_MESSAGE"] == "Y")
			{
				?> checked="checked" <?
			} 
			?> /> <label class="label" for="FORUM_SUBSC_GET_MY_MESSAGE"><?=GetMessage("F_SUBSC_GET_MY_MESSAGE")?></label>
		</div>
			</td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_DESCR")?></div></th>
		<td><input type="text" name="FORUM_DESCRIPTION" size="45" maxlength="255" value="<?=$arResult["str_FORUM_DESCRIPTION"]?>"/></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_INTERESTS")?></div></th>
		<td><textarea name="FORUM_INTERESTS" rows="3" cols="35"><?=$arResult["str_FORUM_INTERESTS"];?></textarea></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_SIGNATURE")?></div></th>
		<td><textarea name="FORUM_SIGNATURE" rows="3" cols="35"><?=$arResult["str_FORUM_SIGNATURE"]?></textarea></td>
	</tr>
	<tr>
		<th><div class="name-cell2"><?=GetMessage("F_AVATAR")?></div></th>
		<td>
			<?
			foreach (array('AVATAR_SIZE', 'AVATAR_H', 'AVATAR_V') as $prop)
				$arResult[$prop] = intval($arResult[$prop]);			
			$arResult['AVATAR_SIZE'] = CFile::FormatSize($arResult['AVATAR_SIZE']);
			if ((!empty($arResult['AVATAR_H'])) && (!empty($arResult['AVATAR_V'])) && (!empty($arResult['AVATAR_SIZE'])))
			{	
			?>
			<?=str_replace(array("#SIZE#", "#SIZE_BITE#"), array($arResult["AVATAR_H"]."x".$arResult["AVATAR_V"], $arResult["AVATAR_SIZE"]), 
				GetMessage("F_SIZE_AVATAR"))?><br/>
			<?}?>
			<input name="FORUM_AVATAR" size="30" type="file" />
			<?if ($arResult["SHOW_DELETE_FORUM_AVATAR"] == "Y"):?>
			<br/><input type="checkbox" name="FORUM_AVATAR_del" value="Y" id="FORUM_AVATAR_del" /> 
				<label for="FORUM_AVATAR_del"><?=GetMessage("FILE_DELETE")?></label>
			<br/>
				<?=$arResult["str_FORUM_AVATAR_IMG"]?>
			<?endif;?>
		</td>
	</tr>
<?// ********************* User properties ***************************************************?>
<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
	<?=$tabControl->BeginNextTab();?>
	<?$first = true;?>
	<?foreach ($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
	<tr><th>
		<?if ($arUserField["MANDATORY"]=="Y"):?>
			<span class="required">*</span>
		<?endif;?>
		<?=$arUserField["EDIT_FORM_LABEL"]?>:</th>
		<td>
			<?$APPLICATION->IncludeComponent(
				"bitrix:system.field.edit", 
				$arUserField["USER_TYPE"]["USER_TYPE_ID"], 
				array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField), null, array("HIDE_ICONS"=>"Y"));?>
		</td>
	</tr>
	<?endforeach;?>
<?endif;?>
<?$tabControl->End();?>
<div class="forum-clear-float"></div>
<div class="forum-reply-buttons forum-user-edit-buttons">
	<div class="btn ok2 btn-green mode2 icon-arrow-right" onclick="this.firstElementChild.click();">
		<input class="ok-btn2" type="submit" name="save" value="<?=GetMessage("F_SAVE")?>" id="save"/>
	</div>
	<div class="btn ok2 btn-gray-dark mode2 icon-arrow-right" onclick="this.firstElementChild.click();">
		<input class="ok-btn2" type="submit" value="<?=GetMessage("F_CANCEL")?>" name="cancel" id="cancel" />
	</div>
</div>

</form>
<br>