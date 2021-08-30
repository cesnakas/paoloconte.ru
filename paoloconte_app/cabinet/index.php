<?
define('NEED_AUTH', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет");
?>
<div class="cabinet-wrap">
	<div class="top-text">
		Рады приветствовать Вас, Вы зарегистрированы в системе под логином <?=$USER->GetLogin();?>
	</div>

	<div class="image">
		<img src="<?=SITE_TEMPLATE_PATH?>/images/content/cabinet-1.jpg">
	</div>
</div>

<script>
	app.setPageTitle({"title" : "Личный кабинет"});
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>