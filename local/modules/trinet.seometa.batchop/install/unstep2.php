<?if(!check_bitrix_sessid()) return;?>
<?
$errors = $GLOBALS['errors'];

if( empty($errors) ) {
	CAdminMessage::ShowNote( 'Модуль успешно удалён' );
} else {
	CAdminMessage::ShowMessage(
		Array(
			"TYPE"=>"ERROR",
			"MESSAGE" =>'Ошибки удаления модуля',
			"DETAILS"=> implode('<br>', $errors) ,
			"HTML"=>true,
		)
	);
}

/* @var $APPLICATION CMain */
?>
<form action="<?echo $APPLICATION->GetCurPage()?>">
	<input type="hidden" name="lang" value="<?echo LANGUAGE_ID;?>">
	<input type="submit" name="" value="Назад">
<form>