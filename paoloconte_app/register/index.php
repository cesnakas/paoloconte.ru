<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");
?><div class="container">
	<div class="cabinet-wrap">
		<div class="anti-margin-box">
			 <?$APPLICATION->IncludeComponent(
				"citfact:register.ajax",
				"mobile",
				Array(
					"USER_FIELDS_CODE" => array('UF_FRIEND_EMAIL'),
					"REDIRECT_TO" => '/paoloconte_app/cabinet/',
					"ADD_SECTIONS_CHAIN" => 'Y'
				)
			);?>
		</div>
	</div>
</div>

<script>
	app.setPageTitle({"title" : "Регистрация"});
</script>

<br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>