<?

use Citfact\ProductAvailabilityBuy;

$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$lock = new \Citfact\Lock(\Citfact\Lock::ACTIVATE_OFFERS);
if (!$lock->lock()) {
    echo 'Не закончена предыдущая обработка';
    die();
}

try {
    file_put_contents(
        $_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log",
        print_r('START --- -- -- - ', true)
    );


    \Citfact\ProductActivation::activateSkuCron();
    \Citfact\ProductActivation::activateProductsByOffersActivityCron();
    set_time_limit(36000);
    \Citfact\ProductActivation::deActivateSkuCron();
    $productAvailabilityBuy = new ProductAvailabilityBuy();
    $productAvailabilityBuy->activeOffersSection();
} catch (\Exception $e) {
}

$lock->unlock();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");