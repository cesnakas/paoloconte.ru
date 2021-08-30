<?
if (PHP_SAPI != 'cli') {
    die('cli only');
}

use Citfact\Lock;
use Citfact\ProductAvailability;

$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

try {
    $productAvailabilityService = new ProductAvailability();
    $productAvailabilityService->setAvailabilityProductsExec('update_product_availability');
} catch (Exception $e) {
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
