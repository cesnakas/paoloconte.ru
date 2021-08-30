<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * addEventHandler
 * sale OnSaleShipmentEntitySaved
 * onSaleShipmentEntitySavedHandler
 */


return;
global $USER;
if (!$USER->IsAdmin()) {
    return;
}


global $isShipmentUpdateHandler;
$isShipmentUpdateHandler = true;


IncludeModuleLangFile(__FILE__);

if (!CModule::IncludeModule('sale')) return;

use Bitrix\Sale\Order;
use Bitrix\Sale\BusinessValue;
use Bitrix\Sale\Cashbox\CheckManager;

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/sberbank.ecom/config.php');
require_once('rbs.php');


// $event OnSaleShipmentEntitySaved in onSaleShipmentEntitySavedHandler() init.php
if (!isset($event)) {
    return;
}


$shipment = $event->getParameter('ENTITY');
$arShipmentOldValues = $event->getParameter('VALUES');
$arShipmentValues = $shipment->getFields()->getValues();

$order = Order::load($arShipmentValues['ORDER_ID']);
$orderId = $order->getId();

if ($arShipmentOldValues['DEDUCTED'] != 'N' && $arShipmentValues['DEDUCTED'] != 'Y') {
    return;
}

log_payment("\n" . '----------  ' . date('Y-m-d H:i:s') . '  $orderId - ' . $orderId);

$SBER_PAY_SYSTEMS_ID;
$payment = getPayment($order, $SBER_PAY_SYSTEMS_ID);
$paymentCollection = $order->getPaymentCollection();

$shipment = $order->getShipmentCollection()->current();

if (!$payment || !$shipment) {
    log_payment('Order does not have correct $payment or $shipment');
    return;
}


$paymentCollection = $order->getPaymentCollection();
$paySystemId = $payment->getPaymentSystemId();
$paymentIndex = $paymentCollection->getIndexById($paySystemId);
$consumerKey = 'PAYSYSTEM_' . $paySystemId;

$twoStage = (BusinessValue::get('TWO_STAGE', $consumerKey) == 'Y') ? true : false;
$testMode = (BusinessValue::get('TEST_MODE', $consumerKey) == 'Y') ? true : false;
$logging = (BusinessValue::get('LOGGING', $consumerKey) == 'Y') ? true : false;
$login = BusinessValue::get('USER_NAME', $consumerKey);
$password = BusinessValue::get('PASSWORD', $consumerKey);
$twoStage = true;/******debug******debug******debug******debug******debug******debug******debug******debug******debug******debug******/
if (!$twoStage) {
    log_payment('The module works in oneStage mode');
    return;
}


$rbs = new RBS($login, $password, $twoStage, $testMode, $logging);

$rbsOrderId = $order->getPropertyCollection()->getItemByOrderPropertyId($PROPERTY_PS_ORDER_ID)->getValue();
$rbsOrder = $rbs->get_order_status_by_orderId($rbsOrderId);
log_payment('getOrderStatusExtended.do');
log_payment('  $rbsOrderId - ' . $rbsOrderId . ' | orderNumber - ' . $rbsOrder['orderNumber']);
log_payment('  orderStatus - ' . $rbsOrder['orderStatus'] . ' | paymentState - ' . $rbsOrder['paymentAmountInfo']['paymentState']);
log_payment('  errorCode - ' . $rbsOrder['errorCode'] . ' | errorMessage - ' . $rbsOrder['errorMessage']);
foreach ($rbsOrder['orderBundle']['cartItems']['items'] as $item) {
    log_payment('  positionId - ' . $item['positionId'] . ' | name - ' . $item['name']
        . ' | quantity - ' . $item['quantity']['value'] . ' ' . $item['quantity']['measure']
        . ' | itemAmount - ' . $item['itemAmount'] . ' | itemCode - ' . $item['itemCode']);
}

if ($rbsOrder['orderStatus'] != $rbs->orderStatus['APPROVED']) {
    log_payment('Order has an incorrect status in sberbank');
    return;
}


$measureList = array();
$dbMeasure = CCatalogMeasure::getList();
while ($arMeasure = $dbMeasure->GetNext()) {
    $measureList[$arMeasure['ID']] = $arMeasure['MEASURE_TITLE'];
}

$itemsCnt = 1;
$orderBasket = CSaleBasket::GetList(array(), array('ORDER_ID' => $order->getId()));
while ($basketItem = $orderBasket->Fetch()) {
    $arProduct = CCatalogProduct::GetByID($basketItem['PRODUCT_ID']);
    $measure = $measureList[$arProduct['MEASURE']];

    $itemPrice = recount($basketItem['PRICE']);
    $depositItemAmount = $itemPrice * $basketItem['QUANTITY'];

    $basketItems[] = array(
        'positionId' => $itemsCnt++,
        'name' => $basketItem['NAME'],
        'quantity' => array(
            'value' => $basketItem['QUANTITY'],
            'measure' => $measure,
        ),
        'itemAmount' => $depositItemAmount,
        'itemCode' => $basketItem['PRODUCT_ID'],
        'itemPrice' => $itemPrice
    );
}

$deliveryPrice = $shipment->getPrice();
if ($deliveryPrice > 0) {
    $basketItems[] = array(
        'positionId' => $itemsCnt++,
        'name' => GetMessage('RBS_PAYMENT_DELIVERY_TITLE'),
        'quantity' => array(
            'value' => 1,
            'measure' => GetMessage('RBS_PAYMENT_MEASURE_DEFAULT'),
        ),
        'itemAmount' => recount($deliveryPrice),
        'itemCode' => $orderId . "_DELIVERY",
        'itemPrice' => recount($deliveryPrice)
    );
}

$rbsItems = $rbsOrder['orderBundle']['cartItems']['items'];

foreach ($rbsItems as $rbsItem) {
    $found = false;

    foreach ($basketItems as $key => $basketItem) {
        if ($basketItem['itemCode'] == $rbsItem['itemCode']) {
            $basketItems[$key]['positionId'] = $rbsItem['positionId'];
            $found = true;
        }
    }

    if (!$found) {
        $basketItems[] = array(
            'positionId' => $rbsItem['positionId'],
            'name' => $rbsItem['name'],
            'quantity' => array(
                'value' => $rbsItem['quantity']['value'],
                'measure' => $rbsItem['quantity']['measure'],
            ),
            'itemCode' => $rbsItem['itemCode'],
            'itemAmount' => 0,
            'itemPrice' => 0
        );
    }
}
log_payment('$basketItems');
foreach ($basketItems as $item) {
    log_payment('  positionId - ' . $item['positionId'] . ' | name - ' . $item['name']
        . ' | quantity - ' . $item['quantity']['value'] . ' ' . $item['quantity']['measure']
        . ' | itemAmount - ' . $item['itemAmount'] . ' | itemPrice - ' . $item['itemPrice']
        . ' | itemCode - ' . $item['itemCode']);
}

$orderSum = recount($order->getPrice());
$paymentSum = recount($payment->getSum());
log_payment('$orderSum - ' . $orderSum . ' | $paymentSum - ' . $paymentSum);

$canBeDeposited = compareOrderBundles($rbsItems, $basketItems);
if ($canBeDeposited) {
    $deposit = $rbs->deposit($rbsOrderId, $orderSum, $basketItems);
    log_payment('$deposit - errorCode - ' . $deposit['errorCode'] . ' | errorMessage - ' . $deposit['errorMessage']);

    if ($deposit['errorCode'] != 0) {
        return;
    }

    if ($orderSum != $paymentSum) {
        $shipment->setField('DEDUCTED', 'N');
        $savedCurrentValues = setOldBasketValues($order, $rbsItems, $shipment);


        $order = Order::Load($orderId);
        $shipment = $order->getShipmentCollection()->current();
        log_payment('sellreturn $orderSum - ' . $order->getPrice() . ' | $paymentSum - ' . $payment->getSum());
        $checkReturn = CheckManager::addByType([$payment, $shipment], 'sellreturn');

        if (!empty($checkReturn->getErrors())) {
            foreach ($checkReturn->getErrors() as $error) {
                log_payment('$checkReturn $error - ' . $error->getMessage());
            }
            $checkReturnFail = true;
        } else {
            log_payment('Print sellreturn check');
        }

        returnBasketValues($order, $savedCurrentValues, $shipment);

        $order = Order::Load($orderId);
        $shipment = $order->getShipmentCollection()->current();
        $shipment->setField('DEDUCTED', 'Y');
        $order->doFinalAction(true);
        $order->save();

        if ($checkReturnFail) {
            return;
        }

        $payment = getPayment($order, $SBER_PAY_SYSTEMS_ID);
        $paymentCollection = $order->getPaymentCollection();
        $payment->setField('PAID', 'N');
        $payment->setField('SUM', intval($orderSum / 100));
        $payment->setField('PAID', 'Y');
        $order->doFinalAction(true);
        $order->save();

        log_payment('sell $orderSum - ' . $order->getPrice() . ' | $paymentSum - ' . $payment->getSum());
        $checkSell = CheckManager::addByType([$payment, $shipment], 'sell');
        if (!empty($checkSell->getErrors())) {
            foreach ($checkSell->getErrors() as $error) {
                log_payment('$checkSell $error ' . $error->getMessage());
            }

            return;
        } else {
            log_payment('Print sell check');
        }
    }
} else {
    //$reverse = $rbs->reverse($rbsOrderId);
    $deposit = $rbs->deposit($rbsOrderId);
    $refund = $rbs->refund($rbsOrderId);
    log_payment('$deposit - errorCode - ' . $deposit['errorCode'] . ' | errorMessage - ' . $deposit['errorMessage']);
    log_payment('$refund - errorCode - ' . $refund['errorCode'] . ' | errorMessage - ' . $refund['errorMessage']);

    if ($deposit['errorCode'] != 0 || $refund['errorCode'] != 0) {
        return;
    }

    $shipment->setField('DEDUCTED', 'N');
    $savedCurrentValues = setOldBasketValues($order, $rbsItems, $shipment);

    log_payment('sellreturn $orderSum - ' . $order->getPrice() . ' | $paymentSum - ' . $payment->getSum());
    $checkReturn = CheckManager::addByType([$payment, $shipment], 'sellreturn');

    if (!empty($checkReturn->getErrors())) {
        foreach ($checkReturn->getErrors() as $error) {
            log_payment('$checkReturn $error - ' . $error->getMessage());
        }
        $checkReturnFail = true;
    } else {
        log_payment('Print sellreturn check');
    }

    returnBasketValues($order, $savedCurrentValues, $shipment);

    $order = Order::Load($orderId);
    $shipment = $order->getShipmentCollection()->current();
    $shipment->setField('DEDUCTED', 'Y');
    $order->doFinalAction(true);
    $order->save();

    if ($checkReturnFail) {
        return;
    }

    $payment = getPayment($order, $SBER_PAY_SYSTEMS_ID);
    $paymentCollection = $order->getPaymentCollection();
    $payment->setField('PAID', 'N');
    $payment->setField('SUM', intval($orderSum / 100));
    $paymentCollection->save();
    $order->doFinalAction(true);
    $order->save();
}
log_payment('OnSaleShipmentEntitySaved finished');


/**
 * @param Order $order
 * @param $rbsItems
 * @param \Bitrix\Sale\Shipment $shipment
 * @return mixed
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ArgumentNullException
 * @throws \Bitrix\Main\ArgumentOutOfRangeException
 * @throws \Bitrix\Main\NotSupportedException
 * @throws \Bitrix\Main\ObjectNotFoundException
 */
function setOldBasketValues(\Bitrix\Sale\Order &$order, $rbsItems, \Bitrix\Sale\Shipment &$shipment)
{
    $basket = $order->getBasket();
    $basketItems = $basket->getBasketItems();

    foreach ($rbsItems as $rbsBasketItem) {
        $found = false;
        foreach ($basketItems as $basketItem) {
            if ($basketItem->getField('PRODUCT_ID') == $rbsBasketItem['itemCode']) {
                $found = true;

                $productId = $basketItem->getField('PRODUCT_ID');

                $curPrice = $basketItem->getField('PRICE');
                $curQuantity = $basketItem->getField('QUANTITY');

                $oldPrice = intval(($rbsBasketItem['itemAmount'] / $rbsBasketItem['quantity']['value']) / 100);
                $oldQuantity = $rbsBasketItem['quantity']['value'];

                if ($curPrice != $oldPrice || $curQuantity != $oldQuantity) {
                    $basketItem->setPrice($oldPrice, true);
                    $basketItem->setField('QUANTITY', $oldQuantity);
                    log_payment('Set old values - id ' . $productId
                        . ' | price ' . $curPrice . ' to ' . $oldPrice . ' | quantity ' . $curQuantity . ' to ' . $oldQuantity);

                    $savedValues['CHANGED_ITEMS'][] = $productId;
                    $savedValues['CHANGED_VALUES'][$productId] = array(
                        'PRICE' => $curPrice,
                        'QUANTITY' => $curQuantity
                    );
                }
            }
        }

        if (!$found && strpos($rbsBasketItem['itemCode'], 'DELIVERY') === false) {
            $itemPrice = intval(($rbsBasketItem['itemAmount'] / $rbsBasketItem['quantity']['value']) / 100);

            $newItem = $basket->createItem('catalog', $rbsBasketItem['itemCode']);
            $newItem->setFields(array(
                'NAME' => $rbsBasketItem['name'],
                'QUANTITY' => $rbsBasketItem['quantity']['value'],
                'CURRENCY' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                'LID' => 's1',
                'PRICE' => $itemPrice,
                'CUSTOM_PRICE' => 'Y',
                'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider'
            ));

            $shipment->getShipmentItemCollection()->createItem($newItem);

            log_payment('Add old item to basket - id ' . $rbsBasketItem['itemCode']
                . ' | price ' . $itemPrice . ' | quantity ' . $rbsBasketItem['quantity']['value']);

            $savedValues['ADDED_ITEMS'][] = $rbsBasketItem['itemCode'];
        }

        if (strpos($rbsBasketItem['itemCode'], 'DELIVERY') !== false) {
            $curDeliveryPrice = $shipment->getField('PRICE_DELIVERY');
            $oldDeliveryPrice = intval($rbsBasketItem['itemAmount'] / 100);

            if ($curDeliveryPrice != $oldDeliveryPrice) {
                $shipment->setField('PRICE_DELIVERY', $oldDeliveryPrice);
                $shipment->setField('BASE_PRICE_DELIVERY', $oldDeliveryPrice);

                log_payment('Set old delivery price - ' . $curDeliveryPrice . ' to ' . $oldDeliveryPrice);

                $savedValues['DELIVERY'] = $curDeliveryPrice;
            }
        }
    }

    $basket->save();
    $shipment->save();
    $shipment->getShipmentItemCollection()->save();
    $order->doFinalAction(true);
    $order->save();

    return $savedValues;
}

/**
 * @param Order $order
 * @param $savedValues
 * @param \Bitrix\Sale\Shipment $shipment
 * @throws \Bitrix\Main\ArgumentOutOfRangeException
 * @throws \Bitrix\Main\NotImplementedException
 * @throws \Bitrix\Main\NotSupportedException
 * @throws \Bitrix\Main\ObjectNotFoundException
 */
function returnBasketValues(\Bitrix\Sale\Order &$order, $savedValues, \Bitrix\Sale\Shipment &$shipment)
{
    $orderId = $order->getId();
    $basket = $order->getBasket();

    $changedItemsFields = $basket->getList(['filter' => ['ORDER_ID' => $orderId, 'PRODUCT_ID' => $savedValues['CHANGED_ITEMS']]]);
    while ($itemFields = $changedItemsFields->fetch()) {
        $basketItem = $basket->getItemById($itemFields['ID']);

        $curPrice = $savedValues['CHANGED_VALUES'][$itemFields['PRODUCT_ID']]['PRICE'];
        $curQuantity = $savedValues['CHANGED_VALUES'][$itemFields['PRODUCT_ID']]['QUANTITY'];

        $oldPrice = $itemFields['PRICE'];
        $oldQuantity = $itemFields['QUANTITY'];

        $basketItem->setPrice($curPrice, true);
        $basketItem->setField('QUANTITY', $curQuantity);
        log_payment('Return values - id ' . $itemFields['PRODUCT_ID']
            . ' | price ' . $oldPrice . ' to ' . $curPrice . ' | quantity ' . $oldQuantity . ' to ' . $curQuantity);

        $basketItem->save();
    }

    $addedItemsFields = $basket->getList(['filter' => ['ORDER_ID' => $orderId, 'PRODUCT_ID' => $savedValues['ADDED_ITEMS']]]);
    while ($addedItemFields = $addedItemsFields->fetch()) {
        $basket->getItemById($addedItemFields['ID'])->delete();

        log_payment('Remove old item - id ' . $addedItemFields['PRODUCT_ID']);
    }

    if (!empty($savedValues['DELIVERY'])) {
        $shipment->setField('PRICE_DELIVERY', $savedValues['DELIVERY']);
        $shipment->setField('BASE_PRICE_DELIVERY', $savedValues['DELIVERY']);
        log_payment('Return delivery price - ' . $savedValues['DELIVERY']);
    }

    $basket->save();
    $order->doFinalAction(true);
    $order->save();
}

/**
 * @param Order $order
 * @param $SBER_PAY_SYSTEMS_ID
 * @return bool|mixed
 */
function getPayment(Order $order, $SBER_PAY_SYSTEMS_ID)
{
    $payment = false;

    $paymentCollection = $order->getPaymentCollection();
    foreach ($paymentCollection as $paymentItem) {
        $fields = $paymentItem->getFields();
        if ($fields['PAID'] == 'Y' && in_array($fields['PAY_SYSTEM_ID'], $SBER_PAY_SYSTEMS_ID)) {
            $payment = $paymentItem;
        }
    }

    return $payment;
}

/**
 * @param $etalon
 * @param $compared
 * @return bool
 */
function compareOrderBundles($etalon, $compared)
{
    $countCheck = count($etalon) == count($compared);

    $codesCheck = true;
    $numbersCheck = true;
    $namesCheck = true;
    $quantityCheck = true;
    $priceCheck = true;

    foreach ($etalon as $etalonItem) {
        $found = false;
        foreach ($compared as $comparedItem) {
            if ($etalonItem['itemCode'] == $comparedItem['itemCode']) {
                $numbersCheck = $numbersCheck ? ($etalonItem['positionId'] == $comparedItem['positionId']) : false;
                $namesCheck = $namesCheck ? ($etalonItem['positionId'] == $comparedItem['positionId']) : false;
                $quantityCheck = $quantityCheck ? ((int)$etalonItem['quantity']['value'] >= (int)$comparedItem['quantity']['value']) : false;
                $priceCheck = $priceCheck ? ($etalonItem['itemAmount'] >= $comparedItem['itemAmount']) : false;
                $found = true;
            }
        }
        if (!$found) {
            $codesCheck = false;
            break;
        }
    }

    return $countCheck && $codesCheck && $numbersCheck && $namesCheck && $quantityCheck && $priceCheck;
}

function recount($val)
{
    $val = $val * 100;

    if (is_float($val)) {
        $val = ceil($val);
    }

    return $val;
}

function log_payment($text = '')
{
    file_put_contents(
        $_SERVER['DOCUMENT_ROOT'] . '/local/var/logs/payment_completion.log',
        $text . "\n",
        FILE_APPEND | LOCK_EX
    );
}