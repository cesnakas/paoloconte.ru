<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "доставка, контакты, заказ");
$APPLICATION->SetPageProperty("keywords", "женская обувь, мужская обувь, личный кабинет, заказ, купить обувь, купить аксессуары, адрес доставки, личные данные, контакты");
$APPLICATION->SetPageProperty("description", "Ваши контактные данные в интернет-магазине Paolo Conte: телефон, email, адрес доставки, номер карты.");
$APPLICATION->SetTitle("Личный кабинет - персональные данные");
?>

            <div class="cabinet-wrap">
                <div class="anti-margin-box">
                    <?$APPLICATION->IncludeComponent(
                        "citfact:main.profile",
                        "",
                        Array(
                            "AJAX_MODE" => "N",
                            "AJAX_OPTION_JUMP" => "N",
                            "AJAX_OPTION_STYLE" => "Y",
                            "AJAX_OPTION_HISTORY" => "N",
                            "SET_TITLE" => "Y",
                            "USER_PROPERTY" => array(
                                "UF_SUBSCRIBE",
                                "UF_LOYALTY_CARD",
                                "UF_USE_LOYALTY_CARD",
                                "UF_PASSPORT_SERIA",
                                "UF_PASSPORT_NOMER",
                                "UF_PASSPORT_VYDAN",
                                "UF_BANK_BIK",
                                "UF_BANK_RS",
                                "UF_BANK_CARDNUMBER",
                                "UF_BANK_CVV",
                                "UF_BANK_FIO",
                                "UF_BANK_FULLNAME",
                            ),
                            "SEND_INFO" => "N",
                            "CHECK_RIGHTS" => "N",
                            "USER_PROPERTY_NAME" => "Допсвойства"
                        )
                    );?>
                </div>
            </div>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>