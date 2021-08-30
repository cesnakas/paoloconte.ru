<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?>
<div class="cabinet-wrap">

	<?$_SESSION['COUPONS'] = array();?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:sale.basket.basket",
		"mobile_paoloconte",
		Array(
			"COLUMNS_LIST" => array("NAME","DELETE","DELAY","TYPE","QUANTITY","PRICE","DISCOUNT","SUM"),
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

	<?$_SESSION['COUPONS'] = array();?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:sale.order.ajax",
		"paoloconte_mobile",
		Array(
			"PAY_FROM_ACCOUNT" => "N",
			"ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
			"COUNT_DELIVERY_TAX" => "N",
			"ALLOW_AUTO_REGISTER" => "Y",
			"SEND_NEW_USER_NOTIFY" => "Y",
			"DELIVERY_NO_AJAX" => "N",
			"DELIVERY_NO_SESSION" => "Y",
			"TEMPLATE_LOCATION" => ".default",
			"DELIVERY_TO_PAYSYSTEM" => "d2p",
			"USE_PREPAYMENT" => "N",
			"PROP_1" => array(),
			"PROP_2" => array(),
			"ALLOW_NEW_PROFILE" => "N",
			"SHOW_PAYMENT_SERVICES_NAMES" => "Y",
			"SHOW_STORES_IMAGES" => "N",
			"PATH_TO_BASKET" => "/paoloconte_app/cabinet/basket/",
			"PATH_TO_PERSONAL" => "/paoloconte_app/cabinet/orders/",
			"PATH_TO_PAYMENT" => "/paoloconte_app/cabinet/basket/payment/",
			"PATH_TO_AUTH" => "/paoloconte_app/auth/",
			"SET_TITLE" => "N",
			"DISABLE_BASKET_REDIRECT" => "Y",
			"PRODUCT_COLUMNS" => array("PROPERTY_RAZMER")
		)
	);?>

	<script>
		app.setPageTitle({"title" : "Корзина"});
	</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>