<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
//echo "<pre>"; print_r($arParams["~AUTH_SERVICES"]);
$arServices = [
    "VKontakte" => ["CLASS_A" => "vk", "CLASS_I" => "fa fa-vk"],
    "Facebook" => ["CLASS_A" => "facebook", "CLASS_I" => "fa fa-facebook"],
    "GoogleOAuth" => ["CLASS_A" => "google", "CLASS_I" => "fa fa-google"],
    "YandexOAuth" => ["CLASS_A" => "yandex", "CLASS_I" => "fa fa-yandex"],
    "Odnoklassniki" => ["CLASS_A" => "odnoklassniki", "CLASS_I" => "fa fa-odnoklassniki"],
    "MyMailRu" => ["CLASS_A" => "mymailru", "CLASS_I" => "fa fa-at"],
];

foreach ($arParams["~AUTH_SERVICES"] as $service) {
    if (array_key_exists($service["ID"], $arServices)) { ?>
        <a href="javascript:void(0);"
           class="<?= $arServices[$service["ID"]]["CLASS_A"]; ?> black-icon"
           onclick="<?= $service["ONCLICK"]; ?>">
            <i class="<?= $arServices[$service["ID"]]["CLASS_I"]?>"></i>
        </a>
    <? }
}
?>


