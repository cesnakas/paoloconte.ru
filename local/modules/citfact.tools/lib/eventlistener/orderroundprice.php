<?php

/*
 * This file is part of the Studio Fact package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Citfact\EventListener;

use Bitrix\Main\Event;
use Bitrix\Sale\BasketItem;
use Bitrix\Sale\Compatible\EventCompatibility;
use Bitrix\Main\EventResult;

class OrderRoundPrice
{
    public static $refresh = null;
    public static $updateShipmentStatus = null;

    // coupons
    public static $propertyCouponCode = 'coupons_order';
    public static $propertyCouponID = 0;

    // is use event once
    public static $isMyOnSaleOrderBeforeSaved = false;
    public static $isRoundBasket = false;
    public static $isRoundOrder = false;
    public static $isSendMailRoundOrder = false;

    // basket
    public static $roundPriceBasketAll = 0;
    public static $arBasketPrices = array();

    /**
     * is Refrech Order
     * @return bool|null
     */
    function isRefresh () {
        if (self::$refresh === null) {
            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
            self::$refresh = (
                $request->getPost('refresh_data_and_save') == 'Y' // кнопка "Пересчитать заказ"
                || $request->getPost('action') == 'addCoupons' // при добавлении купона
            ) ? true : false;
        }
        return self::$refresh;
    }


    /**
     * OnBeforeSaleOrderFinalAction
     * для заказа удаляем кастомные цены (округление цены), чтобы можно было купоны применять
     * @param Event $event
     */
    function OnBeforeSaleOrderFinalAction(Event $event)
    {
        global $isShipmentUpdateHandler;
        if (
            self::$isMyOnSaleOrderBeforeSaved == true
            || !self::isRefresh()
            || $isShipmentUpdateHandler
        )
            return;


        self::$isMyOnSaleOrderBeforeSaved = true;
        $order = $event->getParameter("ENTITY");

        $basket = $order->getBasket();
        if (!$basket)
            return;
        if (DEBUG_LOG_EVENT_ORDER == 'Y') {
            \Bitrix\Main\Diag\Debug::writeToFile(
                array(
                    "ORDER_ID" => $order->getId(),
                    "Сумма заказа" => $order->getPrice(),
                    "Размер скидки" => $order->getDiscountPrice(),
                    "Стоимость доставки" => $order->getDeliveryPrice()
                ),
                'Файл: ' . __FILE__ . 'Строка: ' . __LINE__ . ' Метод: ' . __METHOD__,
                "/local/var/logs/order/logs_order.log");
        }
        $basketItems = $basket->getBasketItems();
        foreach ($basketItems as $basketItem) {
            if (DEBUG_LOG_EVENT_ORDER == 'Y') {
                \Bitrix\Main\Diag\Debug::writeToFile(
                    array(
                        "Название" => $basketItem->getField('NAME'),
                        "Финальная цена" => $basketItem->getFinalPrice(),
                    ),
                    'Файл: ' . __FILE__ . 'Строка: ' . __LINE__ . ' Метод: ' . __METHOD__,
                    "/local/var/logs/order/logs_order.log");
            }
            $basketItem->setField('CUSTOM_PRICE', 'N');
            $basketItem->save();
        }

        foreach ($order->getShipmentCollection() as $shipment)
        {
            if (!$shipment->isSystem()) {
                $shipment->setField('CUSTOM_PRICE_DELIVERY', 'N');
            }
        }

    }

    /**
     * OnAfterSaleOrderFinalAction
     * для заказа округляем его суммы. Чтобы не было проблем с платежками
     * @param Event $event
     * @return \Bitrix\Main\EventResult|void
     */
    function RoundBasket (Event $event)
    {
        global $isShipmentUpdateHandler;
        if (
               self::isRefresh()
            || self::$isRoundBasket == true
            || $isShipmentUpdateHandler
        )
            return;

        $order = $event->getParameter("ENTITY");
        $basket = $order->getBasket();
        if (!$basket)
            return;

        self::$isRoundBasket = true;

        self::$roundPriceBasketAll = 0;
        self::$arBasketPrices = [];
        $basketItems = $basket->getBasketItems();
        if (DEBUG_LOG_EVENT_ORDER == 'Y') {
            \Bitrix\Main\Diag\Debug::writeToFile(
                array(
                    "ORDER_ID" => $order->getId(),
                    "Сумма заказа" => $order->getPrice(),
                    "Размер скидки" => $order->getDiscountPrice(),
                    "Стоимость доставки" => $order->getDeliveryPrice()
                ),
                'Файл: ' . __FILE__ . 'Строка: ' . __LINE__ . ' Метод: ' . __METHOD__,
                "/local/var/logs/order/logs_order.log");
        }
        foreach ($basketItems as $key => $basketItem) {
            $keyBasketItem = (intval($basketItem->getID()) > 0) ? $basketItem->getID() : $key ;
            $roundPriceBasketItem = round($basketItem->getPrice());
            self::$arBasketPrices[$keyBasketItem] = $roundPriceBasketItem;
            self::$roundPriceBasketAll = self::$roundPriceBasketAll + $roundPriceBasketItem;
            if (DEBUG_LOG_EVENT_ORDER == 'Y') {
                \Bitrix\Main\Diag\Debug::writeToFile(
                    array(
                        "Название" => $basketItem->getField('NAME'),
                        "Финальная цена" => $basketItem->getFinalPrice(),
                    ),
                    'Файл: ' . __FILE__ . 'Строка: ' . __LINE__ . ' Метод: ' . __METHOD__,
                    "/local/var/logs/order/logs_order.log");
            }
        }
    }


    /**
     * OnSaleOrderSaved
     * для заказа округляем его суммы. Чтобы не было проблем с платежками
     * также сохраняем купоны в свойство
     * @param Event $event
     */
    function RoundOrder(Event &$event)
    {
        global $isShipmentUpdateHandler;
        if (
               self::isRefresh()
            || self::$isRoundBasket != true
            || self::$isRoundOrder == true
            || empty(self::$roundPriceBasketAll)
            || empty(self::$arBasketPrices)
            || $isShipmentUpdateHandler
        )
            return;

        $order = $event->getParameter("ENTITY");

        // for new order - send mail
        if (
            $order->isNew()
            && !self::$isSendMailRoundOrder
        ) {
            EventCompatibility::onOrderNewSendEmail($event);
            self::$isSendMailRoundOrder = true;
        }

        $hasOrder = (intval($order->getID())>0) ? true : false ;
        if (!$hasOrder)
            return;

        $basket = $order->getBasket();
        if (!$basket)
            return;

        self::$isRoundOrder = true;

        self::addPropsCoupons($order);

        $roundPriceBasketAll = 0;
        $basketItems = $basket->getBasketItems();
        foreach ($basketItems as $key => $basketItem) {
            $roundPriceBasketItem = round($basketItem->getPrice());
            $roundPriceQuantityBasketItem = round($basketItem->getPrice()) * $basketItem->getQuantity();
            $roundPriceBasketAll = $roundPriceBasketAll + $roundPriceQuantityBasketItem;
            if ($roundPriceBasketItem > 0) {
                $basketItem->setPrice($roundPriceBasketItem, true);
                $basketItem->save();
            }
            if (DEBUG_LOG_EVENT_ORDER == 'Y') {
                \Bitrix\Main\Diag\Debug::writeToFile(
                    array(
                        "Название" => $basketItem->getField('NAME'),
                        "Финальная цена" => $basketItem->getFinalPrice(),
                    ),
                    'Файл: ' . __FILE__ . 'Строка: ' . __LINE__ . ' Метод: ' . __METHOD__,
                    "/local/var/logs/order/logs_order.log");
            }
        }

        $roundPriceDelivery = round($order->getDeliveryPrice());
        $allSum = $roundPriceDelivery + $roundPriceBasketAll;
        $paymentCollection = $order->getPaymentCollection();
        foreach ($paymentCollection as $onePayment) {
            $onePayment->setField('SUM', $allSum);
        }
        foreach ($order->getShipmentCollection() as $shipment)
        {
            if (!$shipment->isSystem()) {
                $shipment->setField('CUSTOM_PRICE_DELIVERY', 'Y');
                $shipment->setFieldNoDemand('PRICE_DELIVERY', $roundPriceDelivery);
                break;
            }
        }

        $order->setFieldNoDemand('PRICE_DELIVERY', $roundPriceDelivery);
        $order->setFieldNoDemand('PRICE', $allSum);
        if (DEBUG_LOG_EVENT_ORDER == 'Y') {
            \Bitrix\Main\Diag\Debug::writeToFile(
                array(
                    "ORDER_ID" => $order->getId(),
                    "Сумма заказа" => $order->getPrice(),
                    "Размер скидки" => $order->getDiscountPrice(),
                    "Стоимость доставки" => $order->getDeliveryPrice()
                ),
                'Файл: ' . __FILE__ . 'Строка: ' . __LINE__ . ' Метод: ' . __METHOD__,
                "/local/var/logs/order/logs_order.log");
        }
        $order->save();
    }


    /**
     * add to order prop apply coupons
     * @param $order
     */
    function addPropsCoupons ($order) {
        if (empty($order))
            return;

        $arApplyCoupons = [];
        if ($discountData = $order->getDiscount()->getApplyResult()) {
            foreach ($discountData['COUPON_LIST'] as $coupon => $dataCoupon) {
                if ($dataCoupon['APPLY'] == 'Y') {
                    $arApplyCoupons[] = $dataCoupon['COUPON'];
                }
            }
        }

        if (!empty($arApplyCoupons)) {
            $propertyCollection = $order->getPropertyCollection();
            $arPropertyCollection = $propertyCollection->getArray();
            foreach ($arPropertyCollection['properties'] as $property) {
                if ($property['CODE'] == self::$propertyCouponCode) {
                    self::$propertyCouponID = $property['ID'];
                    break;
                }
            }
            $somePropValue = $propertyCollection->getItemByOrderPropertyId(self::$propertyCouponID);
            if ($somePropValue) {
                $oldValueCoupon = $somePropValue->getValue();
                if (empty($oldValueCoupon))
                    $oldValueCoupon = array();

                $somePropValue->setValue(array_unique(array_merge($oldValueCoupon, $arApplyCoupons)));
            }
        }
        if (DEBUG_LOG_EVENT_ORDER == 'Y') {
            \Bitrix\Main\Diag\Debug::writeToFile(
                array(
                    "ORDER_ID" => $order->getId(),
                    "Сумма заказа" => $order->getPrice(),
                    "Размер скидки" => $order->getDiscountPrice(),
                    "Стоимость доставки" => $order->getDeliveryPrice()
                ),
                'Файл: ' . __FILE__ . 'Строка: ' . __LINE__ . ' Метод: ' . __METHOD__,
                "/local/var/logs/order/logs_order.log");
        }
    }
}