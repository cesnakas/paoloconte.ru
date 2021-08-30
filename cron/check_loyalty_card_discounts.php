<?
// Проверяем, скидки всех пользователей
// и обновляем их, если что-то поменялось

$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');


function update_discount($user_id, $discount_percent, $update_time) {
    $user_db = new CUser;
    $fields = Array(
        "UF_CARD_DISCOUNT" => $discount_percent,
        "UF_LOYALTY_CARD_DATE" => $update_time
    );
    $user_db->Update($user_id, $fields);
}


$datetime = new DateTime();
$update_time = $datetime->format('d.m.Y H:i:s');


$filter = array(
    "!UF_LOYALTY_CARD" => false,
    "UF_USE_LOYALTY_CARD" => 1
);
$rsUsers = CUser::GetList(($by="id"), ($order="desc"), $filter, array("SELECT" => array("ID", "UF_LOYALTY_CARD", "UF_CARD_DISCOUNT")));

$soap_client = \Citfact\Paolo::GetSoapClientLoyaltyCard();
$loyalty_cards = array();
while ($user = $rsUsers->Fetch()) {
    if (array_key_exists($user["UF_LOYALTY_CARD"], $loyalty_cards)) {
        if ($loyalty_cards[$user["UF_LOYALTY_CARD"]] == intval($user["UF_CARD_DISCOUNT"])) {
            continue;
        }

        update_discount($user["ID"], $loyalty_cards[$user["UF_LOYALTY_CARD"]], $update_time);
        continue;
    }
    try {
        $loyaltyCardInfo = \Citfact\Paolo::GetLoyaltyCardsInfo($user["UF_LOYALTY_CARD"], $soap_client);
        $discount_percent = intval($loyaltyCardInfo["DISCOUNT_PERCENT"]);
        $loyalty_cards[$user["UF_LOYALTY_CARD"]] = $discount_percent;
        if ($discount_percent != intval($user["UF_CARD_DISCOUNT"])) {
            \Citfact\Paolo::CreateCoupon($user["UF_LOYALTY_CARD"], $discount_percent, $user["ID"]);

            update_discount($user["ID"], $discount_percent, $update_time);
        }
    } catch (Exception $e) {
        // if ($e instanceof \Citfact\IncorrectLoyaltyCardException) {
        //     // TODO: отправлять сообщение об ошибке
        // } elseif ($e instanceof \Citfact\SaleDiscountCreateCouponException) {
        //     // TODO: отправлять сообщение об ошибке
        // } else {
        //     // TODO: отправлять сообщение об ошибке
        // }
    }
}