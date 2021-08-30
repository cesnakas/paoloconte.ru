<?

use Citfact\Entity\MarkingCodeGroup\MarkingCodeGroupEntity;
use Citfact\Lock;
use Citfact\ProductAvailabilityBuy;

$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$lock = new Lock(Lock::ACTIVATE_AFTER_EXCHANGE);
if (!$lock->lock()) {
    echo 'Не закончена предыдущая обработка';
    die();
}

try {
    file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r('START --- -- -- - ', true));
    echo \CModule::includeModule('citfact.tools');
    \Citfact\ProductActivation::activateTradeOffersAndStoreAmount();
    \Citfact\ProductActivation::activateSkuCron();
    \Citfact\ProductActivation::activateProductsByOffersActivityCron();
    \Citfact\ProductActivation::deActivateSkuCron();

    $arSections = array(
        'obuv',
        'obuv_1',
        'obuv_2',
        'zhenskaya-obuv-2',
        'muzhskaya-obuv-2',
    );
    $propertyToUpdate = array(
        'CODE' => 'BLOK_TOVARA',
        'NAME' => 'Обувь',
        'VALUE_ID' => '149',
    );
    \Citfact\ProductActivation::setProductsPropertyBySection(IBLOCK_CATALOG, $arSections, $propertyToUpdate);

    $idFootwear = (new MarkingCodeGroupEntity())->getIdFootwear();
    $setMarkingProduct = new \Citfact\SetMarkingProduct();
    $setMarkingProduct->setMarkingBySectionCode($arSections, $idFootwear);
    $productAvailabilityBuy = new ProductAvailabilityBuy();
    $productAvailabilityBuy->activeOffersSection();
} catch (Exception $e) {
}

$lock->unlock();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");