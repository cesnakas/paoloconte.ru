<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет - купоны");
?>
<div class="cabinet-wrap">
	<div class="container">
		<div class="top-text">
			Текст описания страницы купонов.
		</div>

		<?$APPLICATION->IncludeComponent(
			"citfact:coupons.list",
			"cabinet_mobile",
			Array()
		);?>
	</div>
</div>

<script>
	app.setPageTitle({"title" : "Личный кабинет - купоны"});
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>