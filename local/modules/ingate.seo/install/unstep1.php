<?
use \Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid())
	return;

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/admin/module_admin.php");
Loc::loadMessages(__FILE__);
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
<?=bitrix_sessid_post() // скрытое поле с ID сессии?>
	<input type="hidden" name="lang" value="<?echo LANGUAGE_ID?>">
	<input type="hidden" name="id" value="ingate.seo">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<?echo CAdminMessage::ShowMessage(Loc::getMessage("MOD_UNINST_WARN"))?>
	<p><?echo Loc::getMessage("MOD_UNINST_SAVE")?></p>
	<p><input type="checkbox" name="savedata" id="savedata" value="Y" checked><label for="savedata"><?echo Loc::getMessage("MOD_UNINST_SAVE_TABLES")?></label></p>
	<input type="submit" name="" value="<?echo Loc::getMessage("MOD_UNINST_DEL")?>">
</form>