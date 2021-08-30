<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Sale\Internals;

Loader::includeModule('sale');
$request = Bitrix\Main\Context::getCurrent()->getRequest();
$orderId = $request->get("idOrder");
$orderPropertyId = $request->get("idProp");
$idPropValue = $request->get("idPropValue");

if(empty($orderId) || empty($orderPropertyId)){
    return;
}

$order = Bitrix\Sale\Order::load($orderId);
$propertyCollection = $order->getPropertyCollection();
$somePropValue = $propertyCollection->getItemByOrderPropertyId($orderPropertyId);
$somePropValue->setValue($idPropValue);
$order->save();
echo nl2br(htmlspecialcharsbx($somePropValue->getValue()));