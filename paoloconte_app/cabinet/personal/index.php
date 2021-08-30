<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет - персональные данные");
?>
<div class="cabinet-wrap">
	<div class="container">
		<?$APPLICATION->IncludeComponent(
			"citfact:main.profile",
			"mobile",
			Array(
				"AJAX_MODE" => "Y",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"SET_TITLE" => "Y",
				"USER_PROPERTY" => array(
					"UF_SUBSCRIBE",
					"UF_CARDNUMBER",
					"UF_PASSPORT_SERIA",
					"UF_PASSPORT_NOMER",
					"UF_PASSPORT_VYDAN",
					"UF_BANK_BIK",
					"UF_BANK_RS",
					"UF_BANK_CARDNUMBER",
					"UF_BANK_CVV",
					"UF_BANK_FIO",
					"UF_BANK_FULLNAME",
				),
				"SEND_INFO" => "N",
				"CHECK_RIGHTS" => "N",
				"USER_PROPERTY_NAME" => "Допсвойства"
			)
		);?>
	</div>
</div>

<script>
	app.setPageTitle({"title" : "Личный кабинет - персональные данные"});
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>