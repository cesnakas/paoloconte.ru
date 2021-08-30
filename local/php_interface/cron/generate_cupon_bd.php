<?
/**
 * Генерация купонов ко дню рождению пользователей
 * Задание на cron:
 * @daily /home/borisov/www/paoloconte/httpdocs/local/php_interface/cron/generate_cupon_bd.php
 * При переносе создать папку: /local/var/logs/сron/genetate_cupon_bd/
 */

$_SERVER['DOCUMENT_ROOT'] = str_replace('/local/php_interface/cron', '', __DIR__);
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $USER;
if (!is_object($USER)) {
    $USER = new CUser;
}

$arUserIDs = [];
$arFilter =
    [
        "ACTIVE" => 'Y',
        "PERSONAL_BIRTHDAY_DATE" => date("m-d", strtotime("+7 days"))
    ];

$rsUsers = \CUser::GetList(($by = "TIMESTAMP_X"), ($order = "DESC"), $arFilter);

while ($arUser = $rsUsers->GetNext()) {
    $arUserIDs[] = [
        "ID" => $arUser["ID"],
        "NAME" => $arUser["NAME"],
        "EMAIL" => $arUser["EMAIL"],
        "LAST_NAME" => $arUser["LAST_NAME"],
        "SECOND_NAME" => $arUser["SECOND_NAME"],
        "PERSONAL_BIRTHDAY" => $arUser["PERSONAL_BIRTHDAY"]
    ];
}

$DISCOUNT_ID = 56;
\Bitrix\Main\Diag\Debug::writeToFile("Найдено " . count($arUserIDs) . " пользователей", date("c"), "/local/var/logs/сron/genetate_cupon_bd/log_generate_cupons_" . date("Ymd") . ".log");
foreach ($arUserIDs as $value) {
    $arName = [];
    if ($value["LAST_NAME"]!='') {
        $arName[] = $value["LAST_NAME"];
    }
    if ($value["NAME"]!='') {
        $arName[] = $value["NAME"];
    }
    if ($value["SECOND_NAME"]!='') {
        $arName[] = $value["SECOND_NAME"];
    }

    $activeFrom = new \Bitrix\Main\Type\DateTime();
    $activeTo = new \Bitrix\Main\Type\DateTime();
    $activeTo = $activeTo->add("14 days"); //Период действия
    $coupon = \Bitrix\Sale\Internals\DiscountCouponTable::generateCoupon(true);
    $addDb = \Bitrix\Sale\Internals\DiscountCouponTable::add(
        [
            'DISCOUNT_ID' => $DISCOUNT_ID, //ID правила работы с корзиной
            'COUPON' => $coupon,
            'TYPE' => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_ONE_ORDER,
            'ACTIVE_FROM' => $activeFrom,
            'ACTIVE_TO' => $activeTo,
            'MAX_USE' => 1,
            'USER_ID' => $value["ID"],
            'DESCRIPTION' => ''
        ]);
    if ($addDb->isSuccess()) {
        \Bitrix\Main\Mail\Event::send(
            [
                "EVENT_NAME" => "BIRTHDATE_COUPON",
                "LID" => "s1",
                "C_FIELDS" => [
                    "COUPON" => $coupon,
                    "NAME" => implode(' ', $arName),
                    "EMAIL" => $value["EMAIL"]
                ],
            ]
        );
        \Bitrix\Main\Diag\Debug::writeToFile(
            "Купон: " . $coupon . " отправлен пользователю: " . $value["EMAIL"],
            date("c"),
            "/local/var/logs/сron/genetate_cupon_bd/log_generate_cupons_" . date("Ymd") . ".log"
        );
    } else {
        \Bitrix\Main\Diag\Debug::writeToFile($addDb->getErrorMessages(), date("c"), "/local/var/logs/сron/genetate_cupon_bd/log_generate_cupons_" . date("Ymd") . ".log");
    }
}
