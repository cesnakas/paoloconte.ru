<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Восстановление пароля");
?>

<div class="container inforgot">
	<?if ( $_GET['change_password'] == 'yes' && $_GET['USER_CHECKWORD'] != '' || $_GET['forgot_password']=='yes' ):?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:main.profile",
			"",
			Array()
		);
		?>
	<?else:?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:system.auth.forgotpasswd",
			"",
			Array()
		);
		?>
	<?endif;?>
</div>

<script>
	app.setPageTitle({"title" : "Восстановление пароля"});
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>