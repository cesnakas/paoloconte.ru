<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

return; //#83353# отключен функционал "Отложить в магазине"

$artNum = $arResult["PROPERTIES"]["CML2_ARTICLE"]["VALUE"];

if (!empty($arResult['CATALOG_IMG']['PHOTO'])) {
    $productPhoto = $arResult['CATALOG_IMG']['PHOTO'][0]['BIG'];
} else {
    $productPhoto = $arResult['NOPHOTO'];
}

$isSizeExists = !empty($arResult["SIZES"]);
?>

<div class="modal fade" id="reserve" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>

            <div class="modal-body">
                <div class="title-1">Отложить в магазине</div>
                <? if (!empty($arResult['SHOPS']) && !empty($arResult['STORES_AMOUNT'])) { ?>
                    <div class="reserve-choose-wrap">
                        <? if ($isSizeExists) { ?>
                            <div class="tooltip">Чтобы отложить товар, выберите размер в подходящем магазине.</div>
                        <? } ?>
                        <div class="city-select">
                            <? $APPLICATION->IncludeComponent(
                                "articul.geolocation.city_current",
                                "reserve",
                                array(),
                                false
                            ); ?>

                            <div>
                                <a href="javascript:void(0)" class="js-get-inreserve-modal-and-show">Выбрать другой город</a>
                            </div>


                        </div>
                    </div>

                    <div class="emulate-table shop-store-wrap full">
                        <?
                        $arStoresAllowed = array(
                            51396,  //Магазин в ТЦ Подсолнух, г. Нижневартовск
                            51404,  //Магазин в ТЦ Аура, г. Сургут
                            51378, //Магазин в ТЦ Космопорт, г. Самара
                            162369, //Магазин в ТЦ Амбар, г. Самара
                            51377, //ТРЦ Парк Хаус, Paolo Conte обувь, г. Самара
                            51469, //ТЦ Мега ИКЕА, Paolo Conte обувь, Самара
                            46653, //ТЦ Гостиный двор, Paolo Conte обувь, г. Магнитогорск
                            89508, //ТРЦ Тау Галерея, Paolo Conte обувь, г.Саратов
                            51370, //Торгово-развлекательный центр КОЛИЗЕЙ, Paolo Conte обувь
                            51367, //СБС Мега Молл, Paolo Conte обувь
                            51395, //Торгово-развлекательный центр РИО, Paolo Conte обувь
                            242200, //ТРК "Горизонт" Ростов-на-Дону, Paolo Conte обувь
                            51391, //Магазин в ТЦ РИО, г. Калуга 
                            51374, //Магазин в ТЦ Малина, г. Рязань 
                            51373, //Магазин в ТЦ Малина, г. Рязань 
                            51375, //Торгово-развлекательный центр ПРЕМЬЕР, Paolo Conte обувь, Рязань
                            187832, //Магазин в ТЦ Метрополис, г. Москва 
                            51347, //Магазин в ТЦ Охотный ряд, г. Москва
                            51348, //Магазин в ТЦ Принц Плаза, г. Москва
                            264308, //Магазин в ТЦ Авеню, г. Москва
                            254636, //Магазин в ТЦ Саларис, г. Москва
                            272352, //Магазин в ТЦ Калейдоскоп, г. Москва 
                            111722, //Магазин в ТЦ Круг, г.Москва 
                            244296, //Магазин Аутлет-Центр Белая дача, Котельники
                            51410, //Уфа Торгово-развлекательный комплекс СЕМЬЯ, Paolo Conte обувь
                            51400, //Магазин в ТЦ Центральный, г. Уфа
                            51401, //Магазин в ТЦ Мега, г. Уфа
                            51402, //Магазин в ТЦ Планета, г. Уфа
                            51353, //ТРЦ COLUMBUS, Paolo Conte обувь, Москва
                            51390, //ТРЦ 21 век, Paolo Conte обувь, г. Калуга 
                            51369, //Магазин в ТЦ Фантастика, г. Нижний Новгород
                            51408, //Магазин в ТЦ Седьмое небо, г. Нижний Новгород
                            46654, //СТЦ Мега, Paolo Conte обувь, г. Омск
                            51360, //ТЦ Июнь, Paolo Conte обувь, Красноярск
                            71577, //ТРЦ Кристалл, Paolo Conte обувь, г. Тюмень
                            51388, //ТЦ Талисман, Paolo Conte обувь, г. Ижевск
                            51393, //ТЦ Мадагаскар, Paolo Conte обувь, г. Чебоксары
                            70786, //МЦ Красная площадь, Paolo Conte обувь, г. Анапа
                            46650, //МЦ Красная площадь, Paolo Conte обувь, г. Армавир
                            55016, //Мегацентр «Красная Площадь»,  Paolo Conte обувь, г. Краснодар
                            51368, //OZ МОЛЛ, Paolo Conte обувь, г. Краснодар
                            51384, //ТРЦ Красная Площадь, Paolo Conte обувь, г. Новороссийск
                            235948, //ТРК Европолис, Paolo Conte обувь, г. Санкт-Петербург
                            93360, //ТМК ГРИНН, Paolo Conte обувь,  г.Орел
                            51415, //Торгово-развлекательный центр ГРАНД-ПАРК, г. Грозный
                            51371, //Торгово-развлекательный комплекс СЕМЬЯ, Paolo Conte обувь, г. Пермь
                            51392, //Торгово-развлекательный центр ГУДВИН Paolo Conte Обувь, г. Тюмень
                            218954, //ТРЦ Пушкино Парк, Paolo Conte обувь, г. Пушкино
                            168924, //МЕГА Белая Дача, Paolo Conte обувь, г. Котельники
                            89644, //ТРЦ Алмаз, Paolo Conte обувь, Челябинск
                            99634, //ТЦ Родник, Paolo Conte обувь, Челябинск
                            70759, //ТРЦ Галерея, магазин обуви Paolo Conte, Новосибирск
                            51364, //Торгово-развлекательный центр ТАНДЕМ, Paolo Conte обувь, Казань
                            51354, //ТЦ Галерея, Санкт-Петербург
                            314855, //ТРЦ Семеновский, Paolo Conte обувь, Москва
                            51362, //ТРЦ Гринвич, Екатеринбург
                        );
                        foreach ($arResult['SHOPS'] as $arShop) {
                            if (!in_array($arShop['ID'], $arStoresAllowed)){
                                continue;
                            }
                            $storeId = $arShop['PROPERTY_STORE_ID_VALUE'];
                            $shopId = $arShop["ID"];
                            $shopName = $arShop['NAME'];
                            $arStore = $arResult['STORES_AMOUNT'][$storeId];
                            $address = $arShop['PROPERTY_ADDRESS_VALUE'];
                            $graphick = $arShop['PROPERTY_GRAPHICK_VALUE']['TEXT'];
                            $phone = $arShop['PROPERTY_PHONE_VALUE'];
                            ?>
                            <? if (!empty($arStore)) { ?>
                                <div class="shop-store emulate-row">
                                    <div class="emulate-cell shop-cell valign-middle">
                                        <div class="action-modal-wrap hover-mod">
                                            <div class="shop-name get-modal"><?= $arShop['NAME'] ?></div>
                                            <div class="action-modal position-right detail-size-info"
                                                 data-shop-id="<?= $arShop["ID"]; ?>">
                                                <div class="title"><?= $arShop['NAME'] ?></div>

                                                <ul>
                                                    <li><i class="fa fa-map-marker"></i> <?= $address ?><br>
                                                        <a target="_blank"
                                                           href="/shops/<?= $arShop['PROPERTY_CITY_CODE'] ?>/<?= $arShop['CODE'] ?>/">
                                                            Посмотреть на карте
                                                        </a>
                                                    </li>

                                                    <? if ($graphick != '') { ?>
                                                        <li><i class="fa fa-clock-o"></i> <?= $graphick ?></li>
                                                    <? } ?>

                                                    <? if ($phone != '') { ?>
                                                        <li><i class="fa fa-phone"></i> <?= $phone ?></li>
                                                    <? } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <?
                                    $sizes = [];
                                    foreach ($arStore as $offer_id) {
                                        $sizes[] = $arResult['SIZES'][$offer_id];
                                    }
                                    $sizes = array_filter($sizes);
                                    sort($sizes, SORT_NUMERIC);
                                    ?>
                                    <? if (!empty($sizes)) { ?>
                                        <div class="emulate-cell valign-middle size-box">
                                            <? foreach ($sizes as $size) { ?>
                                                <span class="size" data-store-id="<?= $storeId; ?>"
                                                      data-shop-id="<?= $shopId; ?>"
                                                      data-size="<?= $size; ?>"><?= $size; ?></span>
                                            <? } ?>
                                        </div>
                                    <? } else { ?>
                                        <div class="emulate-cell valign-middle size-box no-padding">
                            <span class="size without-size" data-store-id="<?= $storeId; ?>"
                                  data-shop-id="<?= $shopId; ?>"><i class="fa fa-check" aria-hidden="true"></i></span>
                                        </div>

                                        <div class="emulate-cell valign-middle choose-shop">
                                            <span>Выбрать магазин</span>
                                        </div>
                                    <? } ?>

                                    <div class="emulate-cell text valign-middle">
                                        <a href="javascript:void(0);"
                                           class="btn btn-reserve fa mode1 fa-clock-o"
                                           data-store-id="<?= $storeId; ?>"
                                           data-artnum="<?= $artNum; ?>"
                                           data-shop-name="<?= $shopName; ?>"
                                           data-shop-id="<?= $shopId; ?>"
                                        >
                                            <span>Отложить на 24 часа</span>
                                        </a>
                                    </div>
                                </div>
                            <? } ?>
                        <? } ?>
                    </div>
                    <div class="message">Скидки интернет-магазина не действуют в розничных магазинах. Цены в магазинах могу отличаться от цен на сайте.</div>
                <? } else { ?>
                    <div class="reserve no-shops-msg">
                        К сожалению, данная модель недоступна в розничных магазинах,<br>
                        но Вы можете заказать её в нашем интернет-магазине с доставкой до ближайшего пункта
                        самовывоза<br>
                        или курьером до дома.<br><br>
                        Подробности о доставке — <a href="/help/oplata-i-dostavka" target="_blank">по ссылке</a>.<br>
                        Рассчитать стоимость доставки можно в корзине перед оформлением заказа.<br>
                        При онлайн-оплате — <a href="/events/besplatnaya-dostavka/" target="_blank">бесплатная
                            доставка!</a>
                    </div>
                <? } ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="resultModalInReserve" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <div class="modal-body">
                <div class="modal-title title-1" id="title-in-reserve"></div>
                <div id="msg-in-reserve"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade fastOrderModal" id="reserveForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>

            <div class="modal-body">
                <div class="fast-order-wrap emulate-table full">
                    <div class="emulate-cell valign-bottom image">
                        <img src="<?= $productPhoto ?>">
                    </div>

                    <div class="emulate-cell valign-top form">
                        <div class="modal-title-form" id="reserve-product_name">
                            <?= $arResult['NAME']; ?>
                        </div>

                        <div class="modal-title-desc-form">
                            <? if ($isSizeExists) { ?>
                                <div class="emulate-row">
                                    <div class="emulate-cell text valign-top">
                                        Размер:
                                    </div>

                                    <div id="form-reserve-size" class="emulate-cell text valign-top"></div>
                                </div>
                            <? } ?>

                            <div class="emulate-row">
                                <div class="emulate-cell text valign-top">
                                    Магазин:
                                </div>

                                <div id="form-reserve-shop-name" class="emulate-cell text valign-top"></div>
                            </div>
                        </div>

                        <form action="#" id="form_reserve">
                            <?= bitrix_sessid_post() ?>
                            <input type="text" name="yarobot" value="" class="hide">
                            <input type="text" name="form_reserve_product_id" value="<?= $arResult["ID"]; ?>"
                                   class="hide">

                            <div class="line">
                                <input id="form_reserve_name" autocomplete="off" type="text" name="form_reserve_name"
                                       class="style2 required" placeholder="Введите ваше имя">
                            </div>

                            <div class="line">
                                <input id="form_reserve_phone" autocomplete="off" type="text" name="form_reserve_phone"
                                       class="style2 required mask-phone" placeholder="Введите ваш телефон">
                            </div>

                            <div class="oferta">
                                <? $APPLICATION->IncludeFile(
                                    SITE_DIR . "/include/oferta_reserve.php",
                                    Array(),
                                    Array("MODE" => "text")
                                ); ?>
                            </div>

                            <div class="line">
                                <a href="javascript:void(0);" id="form_reserve_submit"
                                   class="btn full btn-gray-dark fa mode1 fa-clock-o">
                                    <span>Отложить на 24 часа</span>
                                </a>
                            </div>

                            <div class="errors_cont"></div>
                            <div class="success_cont"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
  var ajaxPath = '<?= $this->GetFolder() . '/ajax/'; ?>';
</script>