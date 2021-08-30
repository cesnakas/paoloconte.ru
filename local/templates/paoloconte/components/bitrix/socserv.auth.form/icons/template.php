<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
//echo "<pre>"; print_r($arParams["~AUTH_SERVICES"]);
$arServices = [
    "VKontakte" => ["CLASS_A" => "vk", "CLASS_I" => "fa fa-vk", 'IMG_PATH' => 'vk.svg'],
    "Facebook" => ["CLASS_A" => "facebook", "CLASS_I" => "fa fa-facebook", 'IMG_PATH' => ''],
    "GoogleOAuth" => ["CLASS_A" => "google", "CLASS_I" => "fa fa-google", 'IMG_PATH' => 'google.svg'],
    "YandexOAuth" => ["CLASS_A" => "yandex", "CLASS_I" => "fa fa-yandex", 'IMG_PATH' => 'yandex.svg'],
    "Odnoklassniki" => ["CLASS_A" => "odnoklassniki", "CLASS_I" => "fa fa-odnoklassniki", 'IMG_PATH' => 'odnoklassniki-logo.png'],
    "MyMailRu" => ["CLASS_A" => "mymailru", "CLASS_I" => "fa fa-at", 'IMG_PATH' => ''],
];

foreach ($arParams["~AUTH_SERVICES"] as $service) {
    if (array_key_exists($service["ID"], $arServices)) { ?>
        <a href="javascript:void(0);"
           class="<?= $arServices[$service["ID"]]["CLASS_A"]; ?> black-icon"
           onclick="<?= $service["ONCLICK"]; ?>">
            <img src="/local/templates/paoloconte/images/icons/<?= $arServices[$service["ID"]]["IMG_PATH"]; ?>" alt=""><?/*TODO доделать вывод картинок соц , папка /local/templates/paoloconte/images/icons/*/?>
        </a>
    <? }
}
