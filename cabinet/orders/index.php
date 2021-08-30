<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetPageProperty("tags", "заказ");
$APPLICATION->SetPageProperty("keywords", "женская обувь, мужская обувь, заказ, купить обувь, купить аксессуары, интернет-магазин");
$APPLICATION->SetPageProperty("description", "Ваши заказы в интернет-магазине обуви и аксессуаров Paolo Conte.");
$APPLICATION->SetTitle("Личный кабинет - заказы");
?>

            <div class="cabinet-wrap">
                <? $APPLICATION->IncludeComponent(
                    "bitrix:sale.personal.order",
                    "paoloconte",
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
                        "PATH_TO_PAYMENT" => "/cabinet/orders/payment.php",
                        "PATH_TO_BASKET" => "/cabinet/basket/",
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
                        "STATUS_COLOR_PSEUDO_CANCELLED" => "red",
                        "CAN_BE_CANCELED_AT_STATUSES" => array("N", "C", "P")
                    )
                ); ?>
                <div data-retailrocket-markup-block="5677d98b9872e525b08896a4"></div>
            </div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");