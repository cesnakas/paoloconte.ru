<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?

ShowMessage($arParams["~AUTH_RESULT"]);

?>
<form name="bform" method="post" target="_top" class="form" action="<?=$arResult["AUTH_URL"]?>">
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

<div class="bx-forgotpass-table">
    <div class="title-4"><?=GetMessage("AUTH_GET_CHECK_STRING")?></div>
    <div class="form__item">
        <label for="USER_EMAIL" class="form__label"><?=GetMessage("AUTH_EMAIL")?></label>
        <input type="text" name="USER_EMAIL" id="USER_EMAIL" maxlength="255" />
    </div>

    <input type="submit"
           class="btn btn--black"
           name="send_account_info"
           value="<?=GetMessage("AUTH_SEND")?>"
           style="margin-top:15px;margin-right:15px;"/>
    <a href="#" class="get-modal" data-toggle="modal" data-target="#enterModal"><?=GetMessage("AUTH_AUTH")?></a>
</div>
</form>
<script type="text/javascript">
document.bform.USER_EMAIL.focus();
</script>
