<?
if (PHP_SAPI != 'cli') {
    die('cli only');
}


use Citfact\ProductAvailabilityBuy;

$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$productAvailabilityBuy = new ProductAvailabilityBuy();
$productAvailabilityBuy->activeOffersSection();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
