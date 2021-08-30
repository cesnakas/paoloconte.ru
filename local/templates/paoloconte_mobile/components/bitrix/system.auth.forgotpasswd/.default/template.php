<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

ShowMessage($arParams["~AUTH_RESULT"]);

?>
<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
<?
if (strlen($arResult["BACKURL"]) > 0)
{
?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?
}
?>
	<input type="hidden" name="AUTH_FORM" value="Y">
	<input type="hidden" name="TYPE" value="SEND_PWD">
	<p>
	<?=GetMessage("AUTH_FORGOT_PASSWORD_1")?>
	</p>

<table class="data-table bx-forgotpass-table">
	<thead>
		<tr> 
			<td colspan="2" class="title-forgot"><b><?=GetMessage("AUTH_GET_CHECK_STRING")?></b></td>
		</tr>
	</thead>
	<tbody>
		<?/*<tr>
			<td><?=GetMessage("AUTH_LOGIN")?> </td>
			<td><input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" />&nbsp;<?=GetMessage("AUTH_OR")?>
			</td>
		</tr>*/?>
		<tr> 
			<td><?=GetMessage("AUTH_EMAIL")?>&nbsp;&nbsp;</td>
			<td>
				<input type="text" name="USER_EMAIL" maxlength="255" />
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td></td>
			<td>
				<br>
				<input type="submit" class="btn full btn-gray-dark  mode2 icon-arrow-right" name="send_account_info" value="<?=GetMessage("AUTH_SEND")?>" />
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<br>
				<?/*<a href="#" class="get-modal" data-toggle="modal" data-target="#enterModal"><?=GetMessage("AUTH_AUTH")?></a>*/?>
				<a href="#" class="get-modal" data-type="getMovedPanel" data-target="#side-login"><?=GetMessage("AUTH_AUTH")?></a>
			</td>
		</tr>
	</tfoot>
</table>
</form>
<script type="text/javascript">
document.bform.USER_LOGIN.focus();
</script>
