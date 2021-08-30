<?
require($_SERVER["DOCUMENT_ROOT"]."/paoloconte_app/headers.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
?>

<div class="app-banner-wrap image-full">
    <?$APPLICATION->IncludeComponent(
        "citfact:elements.list",
        "app_banner_index",
        Array(
            "IBLOCK_ID" => IBLOCK_BANNERS_INDEX_MOBILE,
            "PROPERTY_CODES" => array('IMAGE', 'LINK'),
        )
    );?>
</div>

<div class="main_container">
    <?$APPLICATION->IncludeComponent(
        "citfact:elements.list",
        "main_catalog_page",
        Array(
            "IBLOCK_ID" => IBLOCK_MENU_LEFT_MOBILE,
            "IBLOCK_CATALOG_ID" => 10,
            "PROPERTY_CODES" => array('CATALOG_SECTION', 'LINK'),
        )
    );?>
</div>

<div class="container">
    <?
    global $arrFilter_top;
    $arrFilter_top = array(
        '>CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID'] => 0,
        '>PROPERTY_OFFERS_AMOUNT' => 0,
    );
    ?>
    <?$APPLICATION->IncludeComponent(
        "bitrix:catalog.top",
        "paoloconte",
        array(
            "COMPONENT_TEMPLATE" => "paoloconte",
            "IBLOCK_TYPE" => "catalog",
            "IBLOCK_ID" => "10",
            "ELEMENT_SORT_FIELD" => "shows",
            "ELEMENT_SORT_ORDER" => "desc",
            "ELEMENT_SORT_FIELD2" => "id",
            "ELEMENT_SORT_ORDER2" => "desc",
            "FILTER_NAME" => "arrFilter_top",
            "HIDE_NOT_AVAILABLE" => "N",
            "ELEMENT_COUNT" => "6",
            "LINE_ELEMENT_COUNT" => "2",
            "PROPERTY_CODE" => array(
                0 => "",
                1 => "",
            ),
            "OFFERS_LIMIT" => "0",
            "VIEW_MODE" => "SECTION",
            "SHOW_DISCOUNT_PERCENT" => "N",
            "SHOW_OLD_PRICE" => "Y",
            "SHOW_CLOSE_POPUP" => "N",
            "MESS_BTN_BUY" => "Купить",
            "MESS_BTN_ADD_TO_BASKET" => "В корзину",
            "MESS_BTN_DETAIL" => "Подробнее",
            "MESS_NOT_AVAILABLE" => "Нет в наличии",
            "SECTION_URL" => "",
            "DETAIL_URL" => "",
            "SECTION_ID_VARIABLE" => "SECTION_ID",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "36000000",
            "CACHE_GROUPS" => "Y",
            "CACHE_FILTER" => "N",
            "ACTION_VARIABLE" => "action",
            "PRODUCT_ID_VARIABLE" => "id",
            "PRICE_CODE" => $_SESSION['GEO_PRICES']['TO_PARAMS'],
            "USE_PRICE_COUNT" => "N",
            "SHOW_PRICE_COUNT" => "1",
            "PRICE_VAT_INCLUDE" => "Y",
            "CONVERT_CURRENCY" => "N",
            "BASKET_URL" => "/personal/basket.php",
            "USE_PRODUCT_QUANTITY" => "N",
            "ADD_PROPERTIES_TO_BASKET" => "Y",
            "PRODUCT_PROPS_VARIABLE" => "prop",
            "PARTIAL_PRODUCT_PROPERTIES" => "N",
            "PRODUCT_PROPERTIES" => array(
                0 => "RAZMER",
            ),
            "ADD_TO_BASKET_ACTION" => "ADD",
            "DISPLAY_COMPARE" => "N",
            "TEMPLATE_THEME" => "blue",
            "MESS_BTN_COMPARE" => "Сравнить",
            "OFFERS_FIELD_CODE" => array(
                0 => "",
                1 => "",
            ),
            "OFFERS_PROPERTY_CODE" => array(
                0 => "RAZMER",
                1 => "",
            ),
            "OFFERS_SORT_FIELD" => "sort",
            "OFFERS_SORT_ORDER" => "asc",
            "OFFERS_SORT_FIELD2" => "id",
            "OFFERS_SORT_ORDER2" => "desc",
            "PRODUCT_DISPLAY_MODE" => "N",
            "ADD_PICT_PROP" => "-",
            "LABEL_PROP" => "-",
            "OFFERS_CART_PROPERTIES" => array(
            ),
            "PRODUCT_QUANTITY_VARIABLE" => "quantity"
        ),
        false
    );?>
</div>
<?$APPLICATION->IncludeComponent(
    "citfact:form.ajax",
    "promo_index_mobile",
    Array(
        "IBLOCK_ID" => 38,
        "SHOW_PROPERTIES" => array(
            'EMAIL' => array('type' => 'text', 'placeholder' => 'Введите ваш email', 'required'=>'Y'),
            'FROM_PROMO' => array('type' => 'hidden', 'value'=>'Y'),
        ),
        //"EVENT_MESSAGE_ID" => Array("32"), // тут id шаблона, а не события
        "EVENT_NAME" => 'INDEX_PROMO_FORM',
        "SUCCESS_MESSAGE" => 'Спасибо, адрес принят!',
        "ELEMENT_ACTIVE" => 'Y',
        "GENERATE_COUPON" => 'Y'
    )
);?>

<script>
    app.setPageTitle({"title" : "Paoloconte"});
</script>

<?//push sign up if user is autorized?>
<?global $USER;?>
<?if($USER->IsAuthorized()):?>
    <script type="text/javascript">
        BX.ready(function(){
            DEV.getToken();
        });

        DEV = {
            getToken : function (){
                var _this = this,
                    dt = "APPLE";

                if (platform != "ios")
                    dt = "GOOGLE";

                var params = {
                    callback: function (token){
                        var postData = {
                            action: "save_device_token",
                            device_name: device.name,
                            uuid: device.uuid,
                            device_token: token,
                            device_type: dt,
                            sessid: BX.bitrix_sessid()
                        };

                        BX.ajax({
                            timeout:   30,
                            method:   'POST',
                            dataType: 'json',
                            url:       '/paoloconte_app/pull.php',
                            data:      postData
                        });
                    }
                };

                return app.exec("getToken", params);
            }
        };
    </script>
<?endif?>
<?//end push sign up?>

<?=$APPLICATION->ShowViewContent('city_name');?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>