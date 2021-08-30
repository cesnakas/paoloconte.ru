<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет - заказы");
?>
<div class="cabinet-wrap">
	<div class="container">
		<?$APPLICATION->IncludeComponent(
			"bitrix:sale.personal.order",
			"paoloconte_mobile",
			Array(
				"COMPONENT_TEMPLATE" => ".default",
				"PROP_1" => array(""),
				"PROP_2" => array(""),
				"ACTIVE_DATE_FORMAT" => "d.m.Y",
				"SEF_MODE" => "N",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "3600",
				"CACHE_GROUPS" => "Y",
				"ORDERS_PER_PAGE" => "20",
				"PATH_TO_PAYMENT" => "payment.php",
				"PATH_TO_BASKET" => "/paoloconte_app/cabinet/basket/",
				"SET_TITLE" => "N",
				"SAVE_IN_SESSION" => "N",
				"NAV_TEMPLATE" => "paolo_modern",
				"CUSTOM_SELECT_PROPS" => array(""),
				"HISTORIC_STATUSES" => array("F"),
				"STATUS_COLOR_N" => "green",
				"STATUS_COLOR_C" => "gray",
				"STATUS_COLOR_P" => "yellow",
				"STATUS_COLOR_A" => "gray",
				"STATUS_COLOR_D" => "gray",
				"STATUS_COLOR_k" => "gray",
				"STATUS_COLOR_F" => "gray",
				"STATUS_COLOR_PSEUDO_CANCELLED" => "red"
			)
		);?>
	</div>
</div>

<script>
	app.setPageTitle({"title" : "Личный кабинет - заказы"});
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>