<?php
?>

<div data-basket-order>
    <? $APPLICATION->IncludeComponent(
        "bitrix:sale.basket.basket",
        "paoloconte",
        Array(
            "COLUMNS_LIST" => array("NAME", "DELETE", "DELAY", "TYPE", "QUANTITY", "PRICE", "DISCOUNT", "SUM"),
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
    ); ?>
</div >