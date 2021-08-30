<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Cписок желаний");
?>
<div class="container">
	<div class="top-text">
		В списке желаний интернет-магазин Paolo Conte хранит обувь и аксессуары, которые Вы отметили значком «сердечко». Он поможет Вам не потерять понравившиеся модели, упростит выбор и покупку.
	</div>
	<div class="cabinet-wrap">
		<?$APPLICATION->IncludeComponent(
			"bitrix:sale.basket.basket",
			"favorites_mobile",
			Array(
				"COLUMNS_LIST" => array("NAME","DELETE","DELAY","TYPE","PRICE"),
				"PATH_TO_ORDER" => "/paoloconte_app/cabinet/basket/",
				"HIDE_COUPON" => "N",
				"PRICE_VAT_SHOW_VALUE" => "N",
				"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
				"USE_PREPAYMENT" => "N",
				"QUANTITY_FLOAT" => "N",
				"SET_TITLE" => "N",
				"ACTION_VARIABLE" => "action",
				"OFFERS_PROPS" => array("RAZMER")
			)
		);?>
	</div>
</div>

<script>
	app.setPageTitle({"title" : "Cписок желаний"});
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>