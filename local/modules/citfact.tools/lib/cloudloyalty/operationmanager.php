<?php

namespace Citfact\CloudLoyalty;

use Bitrix\Main\Type\DateTime;
use Bitrix\Sale\Basket;
use Bitrix\Sale\BasketItem;
use Bitrix\Sale\Fuser;
use Bitrix\Sale\Order;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PropertyValue;

/**
 * Class OperationManager
 * @package Citfact\CloudLoyalty
 */
class OperationManager {

    //TODO: убрать дефолтного пользователя
    const DEFAULT_SHOP_ID = 12345678;
    const DEFAULT_EMAIL = 'shop@paoloconte.ru';
    const DEFAULT_PHONE = '+79268717960';
    const DEFAULT_SHOP_CODE = 'paoloconte.ru';
    const DEFAULT_SHOP_NAME = 'paoloconte.ru';
    const FIRST_CARD_NUMBER = 777788000001;
    const MIN_PRICE_BASKET_FOR_PROMOCODE = 5499;
    const KEY_SESSION_DATA = 'CLOUD_LOYALTY_OPERATION_MANAGER';

    protected static function setParamSession($param, $value) {
        $_SESSION[static::KEY_SESSION_DATA][$param] = $value;
    }

    public static function getPromoCodeDiscount() {
        return (float) $_SESSION[static::KEY_SESSION_DATA]['PromoCodeDiscount'];
    }

    public static function setPromoCodeDiscount($value) {
        $_SESSION[static::KEY_SESSION_DATA]['PromoCodeDiscount'] = $value;
    }

    protected static function getParamSession($param) {
        if (isset($_SESSION[static::KEY_SESSION_DATA][$param])) {
            return $_SESSION[static::KEY_SESSION_DATA][$param];
        }
        return null;
    }

    public static function addPromoCode($code) {
        $promoCodes = static::getParamSession('promoCodes');
        $promoCodes[] = $code;
        static::setParamSession('promoCodes', array_unique($promoCodes));
    }

    public static function deletePromoCodes() {
        static::setParamSession('promoCodes', []);
    }

    public static function deletePromoCode($code) {
        $promoCodes = static::getParamSession('promoCodes');
        foreach ($promoCodes as $key => $value) {
            if ($value == $code) {
                unset($promoCodes[$key]);
            }
        }
        static::setParamSession('promoCodes', $promoCodes);
    }

    public static function getLastAppliedPromoCode() {
        $promoCodes = static::getParamSession('promoCodes');
        return $promoCodes[count($promoCodes) - 1];
    }

    public static function getPromoCodes() {
        $promoCodes = static::getParamSession('promoCodes');
        reset($promoCodes);
        return $promoCodes;
    }

    /**
     * @param $userId
     * @return array|null
     * @throws \Bitrix\Main\ObjectException
     */
    public static function getUserParametersById($userId) {
        global $USER;

        $arParams = [
            'SELECT' => [
                'UF_*'
            ],
        ];

        $arFilter = ['ID' => $userId];
        $dbUser = \CUser::GetList($by = 'ID', $order = 'ASC', $arFilter, $arParams);
        if ($arUser = $dbUser->Fetch()) {
            $arSendUser = [];
            foreach ($arUser as $field => $val) {
                if (!$val) {
                    continue;
                }

                switch ($field) {
                    case 'NAME':
                        $arSendUser['name'] = $val;
                        break;
                    case 'LAST_NAME':
                        $arSendUser['surname'] = $val;
                        break;
                    case 'SECOND_NAME':
                        $arSendUser['patronymicName'] = $val;
                        break;
                    case 'PERSONAL_GENDER':
                        switch ($val) {
                            case 'F':
                                $gender = 2;
                                break;
                            case 'M':
                                $gender = 1;
                                break;
                            default:
                                $gender = 0;
                        }
                        $arSendUser['gender'] = $gender;
                        break;
                    case 'UF_LOYALTY_CARD':
                        $arSendUser['card'] = $val;
                        break;
                    case 'PERSONAL_BIRTHDAY':
                        $dateTime = new DateTime($val);
                        $timeZone = new \DateTimeZone('UTC');
                        $dateTime->setTimeZone($timeZone);
                        $arSendUser['birthdate'] = $dateTime->format(\DateTime::RFC3339);
                        break;
                    case 'PERSONAL_PHONE':
                        $arSendUser['phoneNumber'] = $val;
                        break;
                    case 'EMAIL':
                        $arSendUser['email'] = $val;
                        break;
                }
            }

            return [
                'client' => $arSendUser,
            ];
        }
        return null;
    }

    /**
     * @param $userId
     * @return array|null
     * @throws \Bitrix\Main\ObjectException
     */
    public static function getUserUpdateParametersById($userId, $useExternalId = false, $removeExternalId = false) {
        $data = [];
        $data = static::getUserParametersById($userId);
        $data['client']['extraFields'] = ['noWelcomeBonus' => 1];
        if ($removeExternalId)
            $data['client']['externalId'] = (string) md5($userId);
        if ($data) {
            if ($useExternalId) {
                if (empty(DataLoyalty::getInstance()->getCardId())) {
                    $data['externalId'] = (string) Fuser::getId(true);
                } else {
                    $data['card'] = DataLoyalty::getInstance()->getCardId();
                }
            } else {
                if ($data['client']['card'] && Events::checkUserInCloudloyalty($data['client']['card'])) {
                    $data['card'] = $data['client']['card'];
                } elseif ($data['client']['phoneNumber']) {
                    $data['phoneNumber'] = $data['client']['phoneNumber'];
                }
            }
            return $data;
        }
        return null;
    }

    public static function getOrderParams($order, $arBuyback = [], $useExternalId = false) {
        $basket = $order->getBasket();
        /** @var DateTime $dateInsert */
        $dateTime = $order->getDateInsert();

        $timeZone = new \DateTimeZone('UTC');
        $dateTime->setTimeZone($timeZone);

        $paymentCollection = $order->getPaymentCollection();

        $applyAmount = 0;
        foreach ($paymentCollection as $payment) {
            /**
             * @var $payment \Bitrix\Sale\Payment
             */
            if ($payment->getPaymentSystemId() == Events::getCloudLoyaltyPaySystemId()) {
                $applyAmount = $payment->getSum();
                break;
            };
        }

        //-Если количество баллов к применению есть в заказе, берется оттуда
        $propertyCollection = $order->getPropertyCollection();
        foreach ($propertyCollection as $orderProp) {
            if ($orderProp->getField("CODE") == "CL_SCORES_TO_ADD") {
                $orderClApplyAmount = $orderProp->getValue();
            }
        }
        LoyaltyLogger::log(DataLoyalty::getInstance()->getDataAll(), 'DataLoyalty');
        if ($orderClApplyAmount > 0) {
            $applyAmount = $orderClApplyAmount;
        } else if (DataLoyalty::getInstance()->getUseCloudScore() == "Y") {
            //-Иначе, если оно есть в сессии и больше нуля, берется из сессии
            $clApplyNum = intval(DataLoyalty::getInstance()->getCloudScoreApplied());
            if ($clApplyNum > 0) {
                $applyAmount = intval(DataLoyalty::getInstance()->getCloudScoreApplied());
                foreach ($propertyCollection as $orderProp) {
                    if ($orderProp->getField("CODE") == "CL_SCORES_TO_ADD") {
                        LoyaltyLogger::log('Set CL_SCORES_TO_ADD(Будет списано бонусных баллов Cloud Loyalty): '
                            . $applyAmount . ' OrderId: '. $order->getId());
                        $orderProp->setValue($applyAmount);
                        $orderProp->save();
                        break;
                    }
                }
                ////$order->save();
            }
        }
        $arSendOrder = array(
            'client' => array(),
            'order' =>
            array(
                'id' => (string) $order->getId(),
                'executedAt' => $dateTime->format(\DateTime::RFC3339),
                'shopCode' => 'paoloconte.ru',
                'shopName' => 'paoloconte.ru',
                'totalAmount' => floatval($basket->getPrice()),
                'loyalty' =>
                array(
                    'action' => 'apply-collect',
                    'applyBonuses' => intval($applyAmount),
                ),
                'items' => []
            ),
        );

        if (!empty($arBuyback)) {
            foreach ($arBuyback['units'] as $unit) {
                $unit['itemCount'] = 1;
            }
            $arSendOrder['order']['items'] = $arBuyback['units'];
        } else {
            $arSendOrder['order']['items'] = static::getBasketItemsParams($basket);
        }
        $arUserSend = static::getUserParametersById($order->getUserId());
        if ($useExternalId) {
            if (empty(DataLoyalty::getInstance()->getCardId())) {
                $arSendOrder['client']['externalId'] = (string) Fuser::getId(true);
            } else {
                $arSendOrder['client']['card'] = DataLoyalty::getInstance()->getCardId();
            }
        } else {
            if ($arUserSend) {
                if ($arUserSend['client']['phoneNumber']) {
                    $arSendOrder['client']['phoneNumber'] = $arUserSend['client']['phoneNumber'];
                } elseif ($arUserSend['client']['card']) {
                    $arSendOrder['client']['card'] = $arUserSend['client']['card'];
                }
            }
        }

        $priceBasket = 0;
        foreach ($basket as $itemBasket) {
            $priceBasket += $itemBasket->getFinalPrice();
        }

        if (static::checkApplyPromoCode($order->getUserId(), $priceBasket)) {
            $arSendOrder['order']['promocode'] = static::getLastAppliedPromoCode();
        }

        return $arSendOrder;
    }

    public static function checkApplyPromoCode($idUser, $sumPriceBasket) {
        if (static::getLastAppliedPromoCode()
//            $sumPriceBasket > static::MIN_PRICE_BASKET_FOR_PROMOCODE
//            && !Events::existOrderInHistory($idUser)
        ) {

            return true;
        }

        return false;
    }

    public static function checkCardNumberOfflineStore($number) {
        return preg_match("/^333[0-9]{10}$/", $number);
    }

    public static function getBasketItemsParams(Basket $basket, $isForReturn = false, $applyAmount = 0, $finalItemArticles = []) {
        $basketItems = $basket->getBasketItems();
        $items = [];

        if (count($basketItems) == 0) {
            return $items;
        }

        $offerIdToArticle = $offerIds = $productIds = [];
        /** @var BasketItem $basketItem */
        foreach ($basketItems as $basketItem) {
            if ($basketItem->getQuantity() <= 0) {
                continue;
            }
            if (!$basketItem->canBuy()) {
                continue;
            }
            $offerIds[] = $basketItem->getProductId();
        }
        if (empty($offerIds)) {
            return [];
        }
        $res = \CIBlockElement::GetList([], ['ID' => $offerIds], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_CML2_LINK']);
        while ($element = $res->fetch()) {
            if ($element['IBLOCK_ID'] == IBLOCK_SKU) {
                $productIds[$element['ID']] = $element['PROPERTY_CML2_LINK_VALUE'];
            } else {
                $productIds[$element['ID']] = $element['ID'];
            }
        }
        $res = \CIBlockElement::GetList([], ['IBLOCK_ID' => IBLOCK_CATALOG, 'ID' => array_values($productIds)], false, false, ['ID', 'NAME', 'IBLOCK_ID', 'PROPERTY_CML2_ARTICLE']);
        while ($element = $res->Fetch()) {
            foreach ($productIds as $offerId => $productId) {
                if ($productId == $element['ID']) {
                    $offerIdToArticle[$offerId] = [
                        'ARTICLE' => $element['PROPERTY_CML2_ARTICLE_VALUE'],
                        'NAME' => $element['NAME'],
                    ];
                }
            }
        }
        if ($isForReturn) {
            $itemsCount = 0;
            foreach ($basketItems as $basketItem) {
                if ($basketItem->getQuantity() <= 0) {
                    continue;
                }
                if (!$basketItem->canBuy()) {
                    continue;
                }
                if (!in_array($offerIdToArticle[$basketItem->getProductId()]['ARTICLE'], $finalItemArticles)) {
                    $itemsCount++;
                }
            }

            $tmpCount = 0;
            $tmpSum = 0;
            //оплаченное пользователем за товары
            $discountVal = $basket->getPrice() - $applyAmount;
        }

        foreach ($basketItems as $basketItem) {
            if ($basketItem->getQuantity() <= 0) {
                continue;
            }
            if (!$basketItem->canBuy()) {
                continue;
            }
            $tmpArticle = $offerIdToArticle[$basketItem->getProductId()]['ARTICLE'];
            if ($isForReturn) {
                if (in_array($tmpArticle, $finalItemArticles))
                    continue;
                $tmpCount++;
            }
            $temp = [
                'sku' => $tmpArticle,
                'itemTitle' => $offerIdToArticle[$basketItem->getProductId()]['NAME'],
                'itemCount' => $basketItem->getQuantity(),
                'buyingPrice' => $basketItem->getBasePrice(),
                'price' => $basketItem->getBasePrice(),
            ];
            if ($isForReturn) {
                if ($tmpCount == $itemsCount) {
                    $percentageOfTotal = ($basketItem->getPrice() * 100) / $basket->getPrice();
                    $temp['amount'] = ceil(($percentageOfTotal / 100) * $discountVal);
                    if (($tmpSum + $temp['amount']) > $discountVal)
                        $temp['amount'] = $temp['amount'] - (($tmpSum + $temp['amount']) - $discountVal);
                    if (empty($finalItemArticles) && $temp['amount'] < $discountVal)
                        $temp['amount'] = $temp['amount'] + (($tmpSum + $temp['amount']) - $discountVal);
                } else {
                    $percentageOfTotal = ($basketItem->getPrice() * 100) / $basket->getPrice();
                    $temp['amount'] = ceil(($percentageOfTotal / 100) * $discountVal);
                    $tmpSum += $temp['amount'];
                }
            }
            $items[] = $temp;
        }

        return $items;
    }

    public static function getPurchaseParams($basket, $isArrayBasket = false, $arUserSend = [], $useCloudScore = 'Y') {
        global $USER;

        $offerIdToArticle = $offerIds = $productIds = [];

        /** @var BasketItem $basketItem */
        $basketItems = $isArrayBasket ? $basket : $basket->getBasketItems();
        foreach ($basketItems as $basketItem) {
            if ($basketItem->getQuantity() <= 0) {
                continue;
            }
            if (!$isArrayBasket && !$basketItem->canBuy()) {
                continue;
            }
            if ($basketItem->getField("DELAY") == "Y") {
                continue;
            }
            $offerIds[] = $basketItem->getProductId();
        }

        if (empty($offerIds)) {
            return null;
        }


        $productsIds = array();
        $offersData = array();

        foreach ($offerIds as $offerId) {
            $productInfo = \CCatalogSku::GetProductInfo($offerId);
            if (!$productInfo) {
                $offersData[$offerId]["PRODUCT"] = $offerId;
            } else {
                $offersData[$offerId]["PRODUCT"] = $productInfo["ID"];
            }
        }


        $data = [];
        if ($arUserSend) {
            if ($arUserSend['client']['phoneNumber']) {
                $data['client']['phoneNumber'] = $arUserSend['client']['phoneNumber'];
            } elseif ($arUserSend['client']['card']) {
                $data['client']['card'] = $arUserSend['client']['card'];
            }
        } else {
            $data['client'] = static::getBalanceParameters($USER->GetID());
        }
        $data['shop'] = [
            'code' => static::DEFAULT_SHOP_CODE,
            'name' => static::DEFAULT_SHOP_NAME,
        ];

        $res = \CIBlockElement::GetList([], ['ID' => $offerIds], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_CML2_LINK']);
        while ($element = $res->fetch()) {
            if ($element['IBLOCK_ID'] == IBLOCK_SKU) {
                $productIds[$element['ID']] = $element['PROPERTY_CML2_LINK_VALUE'];
            } else {
                $productIds[$element['ID']] = $element['ID'];
            }
        }

        $res = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => IBLOCK_CATALOG,
                'ID' => array_values($productIds),
                "!SECTION_ID" => $_SESSION["cloyalty_excluded_sections"],
                "!PROPERTY_STIL_VALUE" => "A.S."
            ], false, false, ['ID', 'NAME', 'IBLOCK_ID', 'PROPERTY_CML2_ARTICLE']
        );
        $validProdIds = array();
        while ($element = $res->Fetch()) {
            foreach ($offersData as $key => $value) {
                if ($value["PRODUCT"] == $element["ID"]) {
                    $validProdIds[] = intval($key);
                }
            }
            foreach ($productIds as $offerId => $productId) {
                if ($productId == $element['ID']) {
                    $offerIdToArticle[$offerId] = [
                        'ARTICLE' => $element['PROPERTY_CML2_ARTICLE_VALUE'],
                        'NAME' => $element['NAME'],
                    ];
                }
            }
        }

        $rows = [];
        $_SESSION["CL_CART_TOTAL_PRICE"] = 0;
        $_SESSION["CL_CART_BASKET_IDS"] = array();

        $priceBasket = 0;
        foreach ($basketItems as $basketItem) {
            if ($basketItem->getQuantity() <= 0) {
                continue;
            }
            if (!$isArrayBasket && !$basketItem->canBuy()) {
                continue;
            }
            if ($basketItem->getField("DELAY") == "Y") {
                continue;
            }

            $basketId = intval($basketItem->getProductId());
            if (!in_array($basketId, $validProdIds)) {
                continue;
            }

            $_SESSION["CL_CART_TOTAL_PRICE"] += $basketItem->getPrice();
            $_SESSION["CL_CART_BASKET_IDS"][] = (string) $basketItem->getId();

            $rows[] = [
                'id' => (string) $basketItem->getId(),
                'qty' => $basketItem->getQuantity(),
                'product' => [
                    'title' => $offerIdToArticle[$basketItem->getProductId()]['NAME'],
                    'sku' => $offerIdToArticle[$basketItem->getProductId()]['ARTICLE'],
                    'blackPrice' => $basketItem->getPrice()
                ]
            ];
            $priceBasket += $basketItem->getFinalPrice();
        }

        $data['rows'] = $rows;
        if ($useCloudScore == 'Y')
            $data['applyBonuses'] = 'auto';
        if (static::checkApplyPromoCode($USER->GetID(), $priceBasket)) {
            $data['promocode'] = static::getLastAppliedPromoCode();
        }
        $calculationQuery = ['calculationQuery' => $data];
        $_SESSION["CL_CART_MAX_APPLY"] = intval($_SESSION["CL_CART_TOTAL_PRICE"] * 0.333);

        return $calculationQuery;
    }

    public static function getBalanceParameters($userId = null) {
        return static::getClientParameters($userId);
    }

    protected static function getClientParameters($userId = null) {
        $parameters = $client = [];
        if (!$userId) {
            if (empty(DataLoyalty::getInstance()->getCardId())) {
                $client['externalId'] = (string) Fuser::getId(true);
            } else {
                $client['card'] = DataLoyalty::getInstance()->getCardId();
            }
        } else {
            $parameters = static::getUserParametersById($userId);
            if ($parameters) {
                if ($parameters['client']['card']) {
                    $client['card'] = $parameters['client']['card'];
                } elseif ($parameters['client']['phoneNumber']) {
                    $client['phoneNumber'] = $parameters['client']['phoneNumber'];
                } else {
                    if (empty(DataLoyalty::getInstance()->getCardId())) {
                        $client['externalId'] = (string) Fuser::getId(true);
                    } else {
                        $client['card'] = DataLoyalty::getInstance()->getCardId();
                    }
                }
            }
        }

        return $client;
    }

    public static function getHistoryParameters($userId = null, $limit = 20, $offset = 0) {
        $client = static::getClientParameters($userId);

        return [
            'client' => $client,
            'pagination' => [
                'limit' => $limit,
                'offset' => $offset,
            ]
        ];
    }

    /**
     * @param Order $order
     * @param array $items
     * @return array
     */
    public static function getApplyReturnParams(order $order, $arBuyback = [], $buybackIterator = 1, $buybackBonuses = 0, $applyAmount = 0) {
        $basket = $order->getBasket();
        /** @var DateTime $dateInsert */
        $dateTime = $order->getDateInsert();

        $timeZone = new \DateTimeZone('UTC');
        $dateTime->setTimeZone($timeZone);

        $orderId = (string) $order->getId();
        $arSendOrder = [
            'transaction' => [
                'id' => 'return-' . $orderId . '-' . $buybackIterator,
                'executedAt' => $dateTime->format(\DateTime::RFC3339),
                'purchaseId' => $orderId,
                'refundAmount' => 0,
                'shopCode' => 'paoloconte.ru',
                'shopName' => 'paoloconte.ru',
                'items' => []
            ]
        ];

        $arUserSend = static::getUserParametersById($order->getUserId());

        $arSendOrder['transaction']['items'] = static::getBasketItemsParams($basket, true, $applyAmount - $buybackBonuses, array_column($arBuyback['units'], 'sku'));
        $arSendOrder['transaction']['refundAmount'] = 0;
        foreach ($arSendOrder['transaction']['items'] as $item) {
            if ($item['amount'])
                $arSendOrder['transaction']['refundAmount'] += $item['amount'];
            else
                $arSendOrder['transaction']['refundAmount'] += $item['price'];
        }
        if ($arUserSend) {
            if ($arUserSend['client']['phoneNumber']) {
                $arSendOrder['transaction']['phoneNumber'] = $arUserSend['client']['phoneNumber'];
            } elseif ($arUserSend['client']['card']) {
                $arSendOrder['transaction']['card'] = $arUserSend['client']['card'];
            }
        }

        return $arSendOrder;
    }

    /**
     * @return int
     */
    public static function generateCardNumber() {
        $number = (int) \COption::GetOptionString('main', 'last_cloud_loyalty_card_number');
        if (!$number) {
            \COption::SetOptionString('main', 'last_cloud_loyalty_card_number', self::FIRST_CARD_NUMBER);
            $number = self::FIRST_CARD_NUMBER;
        } else {
            \COption::SetOptionString('main', 'last_cloud_loyalty_card_number', ++$number);
        }
        $controlDigit = 0;
        $evenNumber = 0; //четное
        $oddNumber = 0; //нечетное
        $arrNumber = str_split(strval($number));
        foreach ($arrNumber as $key => $digit) {
            if ($key % 2 == 0) {
                $evenNumber += $digit;
            } else {
                $oddNumber += $digit;
            }
        }
        $controlDigit = ($oddNumber * 3 + $evenNumber) % 10;
        if ($controlDigit != 0) {
            $controlDigit = 10 - $controlDigit;
        }
        return $number . $controlDigit;
    }

    public static function getPendingBonuses($userId) {
        \Bitrix\Main\Loader::includeModule('sale');
        $parameters = [
            'filter' => [
                'USER_ID' => $userId,
                '!=STATUS_ID' => 'F',
                'CANCELED' => 'N',
            ],
        ];
        $dbRes = \Bitrix\Sale\Order::getList($parameters);
        $sum = 0;
        while ($order = $dbRes->fetch()) {
            $obProps = \Bitrix\Sale\Internals\OrderPropsValueTable::getList(array('filter' => array('ORDER_ID' => $order['ID'], 'CODE' => Events::PROPERTY_CLOUD_LOYALTY_SCORES_CODE)));
            if ($prop = $obProps->Fetch()) {
                $sum += (int) $prop['VALUE'];
            }
        }
        return $sum;
    }

}
