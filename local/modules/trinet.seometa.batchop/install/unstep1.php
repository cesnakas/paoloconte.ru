<? /* @var $APPLICATION CMain */
$module = new trinet_seometa_batchop();
?>
<form action="<?=$APPLICATION->GetCurPage()?>">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?echo LANGUAGE_ID;?>">
	<input type="hidden" name="id" value="<?=$module->MODULE_ID?>">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<? CAdminMessage::ShowMessage("Внимание!<br>Модуль '{$module->MODULE_NAME}' будет удалён из системы");?>
	<p><label><input type="checkbox" name="savedata" value="Y" checked>Сохранить данные модуля</label></p>
	<input type="submit" name="inst" value="Удалить модуль">
</form>