<? use Citfact\UserBasket\UserBasketHelper;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "корзина, оформление, покупка");
$APPLICATION->SetPageProperty("keywords", "женская обувь, мужская обувь, купить обувь, купить аксессуары, интернет-магазин");
$APPLICATION->SetPageProperty("description", "Корзина покупателя в интернет-магазине Paolo Conte");
if (empty($_REQUEST['ORDER_ID'])) {
    $APPLICATION->SetTitle("Корзина");
} else {
    $APPLICATION->SetTitle("Спасибо за заказ");
}
?>
    <div class="basket">

        <? $_SESSION['COUPONS'] = array(); ?>
        <?include("basket.php");?>
        <? $_SESSION['COUPONS'] = array(); ?>
        <? $APPLICATION->IncludeComponent(
            "bitrix:sale.order.ajax",
            "paoloconte",
            Array(
                "PAY_FROM_ACCOUNT" => "N",
                "ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
                "COUNT_DELIVERY_TAX" => "N",
                "ALLOW_AUTO_REGISTER" => "Y",
                "SEND_NEW_USER_NOTIFY" => "Y",
                "DELIVERY_NO_AJAX" => "N",
                "DELIVERY_NO_SESSION" => "Y",
                "TEMPLATE_LOCATION" => "popup",
                "DELIVERY_TO_PAYSYSTEM" => "d2p",
                "USE_PREPAYMENT" => "N",
                "PROP_1" => array(),
                "PROP_2" => array(),
                "ALLOW_NEW_PROFILE" => "N",
                "SHOW_PAYMENT_SERVICES_NAMES" => "Y",
                "SHOW_STORES_IMAGES" => "N",
                "PATH_TO_BASKET" => "/cabinet/basket/",
                "PATH_TO_PERSONAL" => "/cabinet/orders/",
                "PATH_TO_PAYMENT" => "/cabinet/basket/payment/",
                "PATH_TO_AUTH" => "/auth/",
                "SET_TITLE" => "N",
                "DISABLE_BASKET_REDIRECT" => "Y",
                "PRODUCT_COLUMNS" => array("PROPERTY_RAZMER")
            )
        ); ?>
    </div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>