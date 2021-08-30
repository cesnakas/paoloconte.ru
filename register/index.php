<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Регистрация");
?><div class="container">
	<div class="cabinet-wrap">
		<div class="anti-margin-box">
			 <?$APPLICATION->IncludeComponent(
				"citfact:register.ajax",
				"",
				Array(
					"USER_FIELDS_CODE" => array('UF_FRIEND_EMAIL'),
					"REDIRECT_TO" => '/cabinet/',
					"ADD_SECTIONS_CHAIN" => 'Y',
                    "SHOW_FRIEND_EMAIL" => 'N'
				)
			);?>
		</div>
	</div>
</div>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>