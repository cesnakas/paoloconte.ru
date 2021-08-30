<?
$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$lock = new \Citfact\Lock(\Citfact\Lock::ACTIVATE_TRADE_OFFERS);
if (!$lock->lock()) {
    echo 'Не закончена предыдущая обработка';
    die();
}

try {
    \Citfact\ProductActivation::activateTradeOffersAndStoreAmount();
} catch (\Exception $e) {
}

$lock->unlock();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");