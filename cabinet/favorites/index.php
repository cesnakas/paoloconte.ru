<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "отложенные товары, список желаний, заказ");
$APPLICATION->SetPageProperty("keywords", "женская обувь, мужская обувь, заказ, купить обувь, купить аксессуары, интернет-магазин");
$APPLICATION->SetPageProperty("description", "Поправившаяся обувь и аксессуары в интернет-магазине Paolo Conte.");
$APPLICATION->SetTitle("Cписок желаний");
?>

            В списке желаний интернет-магазин Paolo Conte хранит обувь и аксессуары, которые Вы отметили значком «сердечко». Он поможет Вам не потерять понравившиеся модели, упростит выбор и покупку.
            <div class="cabinet-wrap">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:sale.basket.basket",
                    "favorites",
                    Array(
                        "COLUMNS_LIST" => array("NAME","DELETE","DELAY","TYPE","PRICE"),
                        "PATH_TO_ORDER" => "/cabinet/basket/",
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
    

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>