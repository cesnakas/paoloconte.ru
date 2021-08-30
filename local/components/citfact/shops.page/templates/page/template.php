<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
$pathToYmaps = 'https://api-maps.yandex.ru/2.1/?apikey=' .
    \COption::GetOptionString("main", "map_yandex_keys") . '&lang=ru_RU';
if($arParams['NOMAPS']!='Y')
    $APPLICATION->AddHeadString('<script src="'.$pathToYmaps.'" type="text/javascript"></script>');
?>
<script>
    BX.message({
        SHOPS_TO_MAP: <? echo CUtil::PhpToJSObject($arResult['SHOPS']) ?>,
        CITY_CURRENT: <? echo CUtil::PhpToJSObject($arResult['CITY_CURRENT']) ?>
    });
</script>
<div class="container">

    <ul class="shops-nav tablist 11">
        <li class="shops-nav__link">
            <a href="#tab1" role="tab" aria-controls="tab1" data-toggle="tab">Карта</a>
        </li>
        <li class="shops-nav__link active">
            <a href="#tab2" role="tab" aria-controls="tab2" data-toggle="tab">Список городов</a>
        </li>
    </ul>
</div>

<div class="tab-content">
    <div id="tab1" role="tabpanel" class="tab-pane fade">
        <div id="shop-page-map" class="map-container"></div>
    </div>
    <div id="tab2" role="tabpanel" class="tab-pane fade in active">
        <div class="container">
            <div class="map-list-wrap">
                <select name="map-list">
                    <option value="/shops/">
                        Выберите город
                    </option>
                    <? foreach ($arResult['CITIES'] as $arCity) { ?>
                        <option <?= $arResult["CITY_CURRENT"][0]["CODE"] == $arCity['CODE'] ? 'selected' : '' ?>
                                value="/shops/<?= $arCity['CODE'] ?>/" data-city-id="<?= $arCity['ID'] ?>">
                            <?= $arCity['NAME'] ?>
                        </option>
                    <? } ?>
                </select>
            </div>
            <? if (!empty($arResult['SHOPS_CURRENT'])) { ?>
                <div class="map-link-list">
                    <div class="list-wrap shops-table">
                        <div class="shops-table__header">
                            <div class="shops-table__item">
                                Фото
                            </div>
                            <div class="shops-table__item">
                                Название магазина
                            </div>
                            <div class="shops-table__item">
                                Ближайшее метро
                            </div>
                            <div class="shops-table__item">
                                Адрес
                            </div>
                            <div class="shops-table__item">
                                Время работы
                            </div>
                            <div class="shops-table__item">
                                Телефон
                            </div>


                        </div>
                        <div class="city-cont shops-table__body">
                            <? foreach ($arResult['SHOPS_CURRENT'] as $arShop): ?>
                                <a href="/shops/<?= $arShop['PROPERTY_CITY_CODE'] ?>/<?= $arShop['CODE'] ?>/"
                                   class="list-item collapsed clear-after">
                                    <div class="shops-table__item shops-table__img ">
                                        <?= $arShop["PROPERTY_IMAGES_VALUE"][0] ?>
                                    </div>
                                    <div class="shops-table__item">
                                        <?= $arShop['NAME'] ?>
                                    </div>
                                    <div <?= $arShop["PROPERTY_NEAREST_METRO_VALUE"] == "" ? "style='text-align: center;'" : '' ?>
                                            class="shops-table__item">
                                        <?= $arShop["PROPERTY_NEAREST_METRO_VALUE"] != "" ? $arShop["PROPERTY_NEAREST_METRO_VALUE"] : "-" ?>
                                    </div>
                                    <div class="shops-table__item">
                                        <?= $arShop['PROPERTY_ADDRESS_VALUE'] ?>
                                    </div>
                                    <div class="shops-table__item">
                                        <? if ($arShop['~PROPERTY_GRAPHICK_VALUE']['TEXT'] != ''): ?><?= $arShop['~PROPERTY_GRAPHICK_VALUE']['TEXT'] ?>
                                            <br><? endif ?>
                                    </div>
                                    <div class="shops-table__item">
                                        <? if ($arShop['PROPERTY_PHONE_VALUE'] != ''): ?> <?= $arShop['PROPERTY_PHONE_VALUE'] ?><? endif; ?>
                                    </div>
                                </a>
                            <? endforeach ?>
                        </div>
                    </div>
                </div>
            <? } ?>
        </div>
    </div>
</div>
