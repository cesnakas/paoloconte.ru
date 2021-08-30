<?
namespace Citfact\CloudLoyalty;
use Bitrix\Main;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Bitrix\Sale\Order;
use Bitrix\Sale\Internals;
use Bitrix\Sale\PaySystem\Manager;
use Bitrix\Sale\PropertyValue;
use Citfact\Tools;


class Events
{
	const PROPERTY_NEED_INNER_PAYMENT_CODE = 'NEED_CLOUD_LOYALTY_PAYMENT';
	const PAYMENT_CLOUD_LOYALTY_PAYMENT_CODE = 'CLOUD_LOYALTY_PAYMENT';
	const PROPERTY_BUY_BACK_CODE = 'BUYBACK';
	const PROPERTY_LOYALTY_CARD_CODE = 'LOYALTY_CARD';
	const PROPERTY_BUY_BACK_ITERATOR = 'BUYBACK_ITERATOR';
    const PROPERTY_BUY_BACK_BONUSES = 'BUYBACK_BONUSES';
	const PROPERTY_CLOUD_LOYALTY_SCORES_CODE = 'CLOUD_LOYALTY_SCORES';
	const PROPERTY_USER_LOYALTY_CARD_CODE = 'USER_LOYALTY_CARD';
	const PROPERTY_PHONE_CODE = 'PHONE';
	const ORDER_FINAL_STATUS_ID = 'F';
	const MAX_PERCENT_INNER = 50.0;

	const TYPE_OPERATION_HISTORY_APPLIED = 'OPERATION_APPLIED';

	static $payCloudLoyaltyPaymentOrderId = 0;
	static $setOrderOrderId = 0;
	static $paymentCloudLoyaltyId = 0;

	protected static $promoCodeResponse = false;

    public static function getPromoCodeResponse() {
	    return static::$promoCodeResponse;
    }

    protected static function log($ob)
    {
        LoyaltyLogger::log($ob, 'Events','/local/var/log/LoyaltyLogger/events');
    }

    public static function createExternalInCloudLoyalty(){
        global $USER;
        if (!$USER->IsAuthorized()) {
            $operationName = Operation::OPERATION_NEW_CLIENT;
            $parameters = [
                'client' => [
                    'externalId' => (string)Fuser::getId(true)
                ],
            ];
            $operation = new Operation($parameters, $operationName);
            $operation->send();
            $response = json_decode($operation->getResponse(), 1);
            return $response;
        }
        return false;
    }
    /**
     * регистрируем пользователя в ситеме лояльности CloudLoyalty по FUSER_ID
     * @param Main\Event $event
     */
    function registrationUsersInCloudLoyalty(Main\Event $event){
        global $USER;
        if (!$USER->IsAuthorized()) {
            return; //#74101# отключение регистрации для новых пользователей

            $operationName = Operation::OPERATION_NEW_CLIENT;
            $parameters = [
                'client' => [
                    'externalId' => (string)Fuser::getId(true),
                    'extraFields' =>
                    [
                        'noWelcomeBonus' => 1,
                    ]
                ],
            ];
            $operation = new Operation($parameters, $operationName);
            Operation::log('registrationUsersInCloudLoyalty ' . date('Y-m-d H:i:s'));
            $operation->send();

            $response = json_decode($operation->getResponse(), 1);

            if (!empty($response['client']['externalId'])){
                $operationName = Operation::OPERATION_ADJUST_BALANCE;
                $parameters = [
                    'client' => [
                        'externalId' => $response['client']['externalId']
                    ],
                    'balanceAdjustment' => [
                        'amountDelta' => -300,
                        'comment' => 'Корректировка баланса'
                    ]
                ];
                $operation = new Operation($parameters, $operationName);
                Operation::log('OnAfterUserBalanceAdjust ' . date('Y-m-d H:i:s'));
                $operation->send();
            }

            $_SESSION['addCloudLoyaltyUser'] = 'Y';
        }
    }


//    function OnBeforeUserAddHandler(&$arFields){
//        if(empty($arFields["UF_LOYALTY_CARD"])){
//            $arFields["UF_LOYALTY_CARD"] = OperationManager::generateCardNumber();
//        }
//    }


	/**
     * при добавлении пользователя обновляем данные в CloudLoyalty по FUSER_ID
	 * @param $arFields
	 * @throws Main\ObjectException
	 */
	function OnAfterUserAddHandler(&$arFields)
	{
		if($arFields["ID"]>0) {
            /**
             * разделяем на регистрацию при уже созданном пользователем в CL по FUSER_ID и обычную
             */
            $operationName = Operation::OPERATION_UPDATE_CLIENT;
            if ($_SESSION['addCloudLoyaltyUser'] == 'Y') {
                $parameters = OperationManager::getUserUpdateParametersById($arFields["ID"], true);
            } else {
                $parameters = OperationManager::getUserUpdateParametersById($arFields["ID"]);
                if (!self::checkUserInCloudloyalty($parameters['client']['card'])) {
                    $operationName = Operation::OPERATION_NEW_CLIENT;
                }
            }
            $operation = new Operation($parameters, $operationName);
            Operation::log('OnAfterUserAddHandler ' . date('Y-m-d H:i:s'));
            $operation->send();
		}
	}

	/**
	 * @param $arFields
	 * @throws Main\ObjectException
	 */
    function OnAfterUserUpdateHandler(&$arFields)
    {
        if ($arFields["RESULT"]) {
            $operationName = Operation::OPERATION_UPDATE_CLIENT;
            if ($_SESSION['addCloudLoyaltyUser'] == 'Y') {
                unset($_SESSION['addCloudLoyaltyUser']);
                $parameters = OperationManager::getUserUpdateParametersById($arFields["ID"], true,true);
            } else {
                $parameters = OperationManager::getUserUpdateParametersById($arFields["ID"]);
                if(!self::checkUserInCloudloyalty($parameters['client']['card']) &&
                    !self::checkUserInCloudloyaltyByPhone($parameters['phoneNumber'])){
                    $operationName = Operation::OPERATION_NEW_CLIENT;
                }
            }
            DataLoyalty::getInstance()->deleteCardId();
            if(!empty($parameters['client']['card'])){
                $operation = new Operation($parameters, $operationName);
                Operation::log('OnAfterUserUpdateHandler ' . date('Y-m-d H:i:s'));
                $operation->send();
            }

        }
	}

	/**
	 * @param Order $order
	 * @return bool
	 * @throws Main\ArgumentException
	 * @throws Main\NotImplementedException
	 */
	public static function needCloudLoyaltyPaySystem(Order $order) {
		$propertyCollection = $order->getPropertyCollection();

		/** @var \Bitrix\Sale\PropertyValue $property */
		foreach ($propertyCollection as $property) {
			if ($property->getField('CODE') == static::PROPERTY_NEED_INNER_PAYMENT_CODE) {
				return  $property->getValue() == 'Y';
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
		return false;

	}

	/**
	 * @param Order $order
	 * @return bool
	 */
	public static function hasCloudLoyaltyPaySystem(Order $order) {
		$paymentCollection = $order->getPaymentCollection();

		foreach ($paymentCollection as $payment) {
			/**
			 * @var $payment \Bitrix\Sale\Payment
			 */
			if ($payment->getPaymentSystemId() == static::getCloudLoyaltyPaySystemId()) {
				return true;
			};
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
		return false;
	}

	/**
	 * @return int
	 * @throws Main\ArgumentException
	 * @throws Main\ObjectPropertyException
	 * @throws Main\SystemException
	 */
	public static function getCloudLoyaltyPaySystemId() {
		if (static::$paymentCloudLoyaltyId === false) {
			return static::$paymentCloudLoyaltyId;
		}
		if (static::$paymentCloudLoyaltyId !== 0) {
			return static::$paymentCloudLoyaltyId;
		}
		$paySystemParams = [
			'filter' => [
				'CODE' => self::PAYMENT_CLOUD_LOYALTY_PAYMENT_CODE,
			]
		];
		$arPaySystem = Internals\PaySystemActionTable::getList($paySystemParams)->fetch();

		if ($arPaySystem) {
			static::$paymentCloudLoyaltyId = $arPaySystem['ID'];
		} else {
			static::$paymentCloudLoyaltyId = false;
		}
		return static::$paymentCloudLoyaltyId;
	}

	/**
	 * @param Main\Event $event
	 * @throws Main\ArgumentException
	 * @throws Main\ArgumentOutOfRangeException
	 * @throws Main\NotImplementedException
	 * @throws Main\ObjectPropertyException
	 * @throws Main\SystemException
	 */
	public static function addCloudLoyaltyPaySystemIfNeeded(Main\Event $event)
	{
	    LoyaltyLogger::log('OnSaleOrderBeforeSaved');
	    LoyaltyLogger::log('Раньше здесь устанавливался NEED_CLOUD_LOYALTY_PAYMENT(Заказ будет оплачен бонусами)');
	    return;
        Operation::log('addCloudLoyaltyPaySystemIfNeeded ' . date('Y-m-d H:i:s'));
		if (static::getCloudLoyaltyPaySystemId() === false) {
			return;
		}
		/** @var \Bitrix\Sale\Order $order */
		$order = $event->getParameter('ENTITY');
		$request = Main\Application::getInstance()->getContext()->getRequest();
		$needInnerPayment = $request->getPost(Events::PROPERTY_NEED_INNER_PAYMENT_CODE) == 'Y';
        $propertyCollection = $order->getPropertyCollection();
        foreach ($propertyCollection as $property) {
            if(Events::PROPERTY_LOYALTY_CARD_CODE == $property->getField('CODE')){
                $cardId = $property->getValue();
            }
        }

        //проставляем свойство для оплаты с внутренего счета
		if ($needInnerPayment) {
			$propertyCollection = $order->getPropertyCollection();
			/** @var \Bitrix\Sale\PropertyValue $property */
			foreach ($propertyCollection as $property) {
				if ($property->getField('CODE') == static::PROPERTY_NEED_INNER_PAYMENT_CODE) {
					$property->setValue('Y');
				}
			}
		}
        //предотвращение зацикливания
        if ($_SESSION['orderEventsNoLoops'] == 'Y') {
            unset($_SESSION['orderEventsNoLoops']);
            return;
           } else {
            $_SESSION['orderEventsNoLoops'] = 'Y';
        }
		if (!$needInnerPayment) {
			$needInnerPayment = static::needCloudLoyaltyPaySystem($order);
		}
		if (!$needInnerPayment) {
			return;
		}
		$paymentCollection = $order->getPaymentCollection();

		$hasAlreadyCloudLoyaltyPaySystem = static::hasCloudLoyaltyPaySystem($order);

		if ($hasAlreadyCloudLoyaltyPaySystem) {
			return;
		}
		/**
		 * если НЕ сохранение заказа и НЕ указаны бонусы, пропускаем шаг
		 */
		global $USER;
        if(!empty($cardId) && self::checkUserInCloudloyalty($cardId)){
            DataLoyalty::getInstance()->setCardId(str_replace('-','', $cardId));
        }
		$sumBonus = Events::getMaxToApplyByBasket($order->getBasket());
		if (!$sumBonus || $sumBonus < 0) {
			return;
		}
		/**
		 * считаем разрешенную суммму оплаты бонусами
		 */
		$allSumPay = 0;
		$paymentCollection = $order->getPaymentCollection();
		foreach ($paymentCollection as $payment) {
			/**
			 * @var $payment \Bitrix\Sale\Payment
			 */
			$allSumPay += (float)$payment->getField('SUM');
		}
		$allSumPayWithoutDelivery = $allSumPay-$order->getDeliveryPrice();
		/**
		 * у первой оплаты уменьшаем сумму оплаты на количество бонусов
		 */
//		foreach ($paymentCollection as $payment) {
//            if ($payment->getPaymentSystemId() == static::getCloudLoyaltyPaySystemId()) {
//                continue;
//            }
//			/**
//			 * @var $payment \Bitrix\Sale\Payment
//			 */
//			$payment->setField('SUM', $allSumPay - $sumBonus);
//			break;
//		}

		/**
		 * добавляем оплату по внутреннему счету
		 */
		$payment = $paymentCollection->createItem(
			Manager::getObjectById(
				self::getCloudLoyaltyPaySystemId()
			)
		);
		$payment->setField("SUM", $sumBonus);
		$payment->setField("CURRENCY", $order->getCurrency());

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

	public static function getMaxBonusSumForTotal($allSumPay, $sumBonus = 0) {
		$percentBonus = $sumBonus / ($allSumPay / 100);

		/**
		 * если оплата бонусами превышает разрешенную сумму, выкидываем ошибку
		 */
		if ($percentBonus > static::MAX_PERCENT_INNER) {
			$sumBonus = ceil($allSumPay *  (static::MAX_PERCENT_INNER /100));
			$percentBonus = static::MAX_PERCENT_INNER;
		}

		return $sumBonus;
	}

	/**
	 * @param Main\Event $event
	 * @throws Main\ArgumentException
	 * @throws Main\ArgumentNullException
	 * @throws Main\ArgumentOutOfRangeException
	 * @throws Main\ObjectNotFoundException
	 */
	public static function payCloudLoyaltyPayment(Main\Event $event) {
	    return;
		/** @var \Bitrix\Sale\Order $order */
		$order = $event->getParameter('ENTITY');
		$isNew = $event->getParameter('IS_NEW');
        Operation::log('payCloudLoyaltyPayment ' . date('Y-m-d H:i:s'));
        Operation::log(self::$payCloudLoyaltyPaymentOrderId);
        Operation::log($order->getId());
        Operation::log($isNew);
		if (self::$payCloudLoyaltyPaymentOrderId == $order->getId()) {
			return;
		}
		static::$payCloudLoyaltyPaymentOrderId = $order->getId();
		$paymentCollection = $order->getPaymentCollection();
		if ($isNew) {
			return;
		}
		$allPaymentsArePaid = true;

		foreach ($paymentCollection as $payment) {
			/**
			 * @var $payment \Bitrix\Sale\Payment
			 */
			if ($payment->getPaymentSystemId() == static::getCloudLoyaltyPaySystemId()) {
				continue;
			}
			if (!$payment->isPaid()) {
				$allPaymentsArePaid = false;
			}
		}

		if ($allPaymentsArePaid === false) {
			return;
		}
		foreach ($paymentCollection as $payment) {
			/**
			 * @var $payment \Bitrix\Sale\Payment
			 */
			if (($payment->getPaymentSystemId() == static::getCloudLoyaltyPaySystemId())
				&& !$payment->isPaid()) {
				/** Пополняем внутренний счёт на величину оплаты, чтобы сразу же снять */
				if (\CSaleUserAccount::UpdateAccount(
					$order->getUserId(),
					($payment->getSum()),
					$order->getCurrency(),
					"CLOUD_LOYALTY",
					$order->getId()
				)) {
					$payment->setPaid('Y');
					$order->save();
				}
			};
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

    public static function checkCardUserInBitrix($card){
        $filter = Array("UF_LOYALTY_CARD" => $card);
        $rsUsers = \CUser::GetList(($by = "NAME"), ($order = "desc"), $filter);
        while ($arUser = $rsUsers->Fetch()) {
            return true;
        }
        return false;
    }

    public static function checkUserInCloudloyaltyCardAndPhone($card, $phone){
        $operationName = Operation::OPERATION_GET_BALANCE;
        $parameters['card'] = strval($card);
        $operation = new Operation($parameters, $operationName);
        $operation->send();
        $response = $operation->getResponse();
        $arResponse = json_decode($response, true);
        $phone = str_replace(array(' ', '(', ')', '-'), "", $phone);
        if($arResponse['client']['phoneNumber'] == $phone)
        {
            return true;
        }
        return false;
    }

    public static function getCardIdForPhone($phone){
        $phone = str_replace(array(' ', '(', ')', '-'), "", $phone);
        $operationName = Operation::OPERATION_GET_BALANCE;
        $parameters['phoneNumber'] = $phone;
        $operation = new Operation($parameters, $operationName);
        $operation->send();
        $response = $operation->getResponse();
        $arResponse = json_decode($response, true);
        return $arResponse['client']['card'];
    }

    public static function checkUserInCloudloyalty($card){
        $card = str_replace('-', "", $card);
        $operationName = Operation::OPERATION_GET_BALANCE;
        $parameters['card'] = $card;
        $operation = new Operation($parameters, $operationName);
        $operation->send();
        $response = $operation->getResponse();
        $arResponse = json_decode($response, true);
        if(!empty($arResponse['client']))
        {
            return true;
        }
        return false;
    }

    public static function checkUserInCloudloyaltyByPhone($phone, $isCardNeed = false){
        $operationName = Operation::OPERATION_GET_BALANCE;
        $parameters['phoneNumber'] = $phone;
        $operation = new Operation($parameters, $operationName);
        $operation->send();
        $response = $operation->getResponse();
        $arResponse = json_decode($response, true);

        if(!empty($arResponse['client']))
        {
            if ($isCardNeed)
            {
                $card = strval($arResponse['client']['card']);
                if (strlen($card) > 0) return $card;
            }
            return true;
        }
        return false;
    }

	/**
	 * @param Main\Event $event
	 * @throws Main\ArgumentException
	 * @throws Main\ArgumentNullException
	 * @throws Main\ArgumentOutOfRangeException
	 * @throws Main\NotImplementedException
	 */
	public static function setOrderAndBuyback(Main\Event $event)
	{
		/** @var \Bitrix\Sale\Order $order */
		$order = $event->getParameter('ENTITY');
		$isNew = $event->getParameter('IS_NEW');

        LoyaltyLogger::log('OnSaleOrderSaved in Events->setOrderAndBuyback(); OrderID: ' . $order->getId());

        //предотвращаем зацикливание
		if (self::$setOrderOrderId != $order->getId() && $isNew) {
            self::$setOrderOrderId = $order->getId();
            $propertyCollection = $order->getPropertyCollection();
            //строка с финальным составом заказа (при возврате)
            $strBuyback = null;
            $userPhone = '';
            $userCard = '';
            /** @var PropertyValue $property */
            foreach ($propertyCollection as $property) {
                if ($property->getField('CODE') == static::PROPERTY_BUY_BACK_CODE) {
                    if ($property->getValue()) {
                        $strBuyback = trim($property->getValue());
                    }
                    continue;
                }
                if ($property->getField('CODE') == static::PROPERTY_PHONE_CODE) {
                    if (!empty($property->getValue())) {
                        $userPhone = $property->getValue();
                    }
                    continue;
                }
                if ($property->getField('CODE') == static::PROPERTY_LOYALTY_CARD_CODE) {
                    if (!empty($property->getValue())) {
                        $userCard = $property->getValue();
                    }
                    continue;
                }
            }

            if (empty($userCard)) {
                $userCard = self::getCardIdForPhone($userPhone);
                if (!empty($userCard)) {
                    DataLoyalty::getInstance()->setCardId(strval($userCard));
                }
            }

            if ($order->getField('STATUS_ID') != static::ORDER_FINAL_STATUS_ID) {
                LoyaltyLogger::log('Если заказ не в статусе выполнен');
                if ($_SESSION['addCloudLoyaltyUser'] == 'Y') {
                    $parameters = OperationManager::getOrderParams($order, [], true);
                } else {
                    $parameters = OperationManager::getOrderParams($order);
                }

                if (empty($parameters['order']['items'])) {

                    //return;
                } else {
                    $operationName = Operation::OPERATION_SET_ORDER;
                    $operation = new Operation($parameters, $operationName);
                    Operation::log('setOrderAndBuyback ' . date('Y-m-d H:i:s'));
                    $operation->send();
                    $response = $operation->getResponse();
                    $arResponse = json_decode($response, true);
                    if (array_key_exists('collectedBonuses', $arResponse['operationResult'])) {
                        $collectedBonuses = $arResponse['operationResult']['collectedBonuses'];

                        /** @var PropertyValue $property */
                        foreach ($propertyCollection as $property) {
                            if ($property->getField('CODE') == static::PROPERTY_CLOUD_LOYALTY_SCORES_CODE) {
                                if ($property->getValue() !== $collectedBonuses) {
                                    $property->setValue($collectedBonuses);
                                    $property->save();
                                    break;
                                }
                            }
                        }
                        ////$order->save();
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
            } elseif ($strBuyback) {
                $arBuyback = json_decode($strBuyback, true);
                if (is_array($arBuyback)) {
                    $buybackIterator = 1;
                    $buybackBonuses = 0;
                    /** @var PropertyValue $property */
                    foreach ($propertyCollection as $property) {
                        if ($property->getField('CODE') == static::PROPERTY_BUY_BACK_ITERATOR) {
                            if ($property->getValue()) {
                                $buybackIterator = trim($property->getValue());
                            }
                        }
                        if ($property->getField('CODE') == static::PROPERTY_BUY_BACK_BONUSES) {
                            $buybackBonuses = trim($property->getValue());
                        }
                    }

                    $basket = $order->getBasket();
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

                    $paymentCollection = $order->getPaymentCollection();

                    $applyAmount = 0;
                    foreach ($paymentCollection as $payment) {
                        /**
                         * @var $payment \Bitrix\Sale\Payment
                         */
                        if ($payment->getPaymentSystemId() == Events::getCloudLoyaltyPaySystemId()) {
                            $applyAmount = $payment->getSum();
                        };
                    }

                    $parameters = OperationManager::getApplyReturnParams($order, $arBuyback, $buybackIterator, $buybackBonuses, $applyAmount);
                }
                $operationName = Operation::OPERATION_APPLY_RETURN;
                $operation = new Operation($parameters, $operationName);
                Operation::log('ApplyReturn ' . date('Y-m-d H:i:s'));
                $operation->send();
                $response = $operation->getResponse();
                $arResponse = json_decode($response, true);
                //после успешного возврата обновим итератор возврата
                if (array_key_exists('confirmation', $arResponse)) {
                    /** @var PropertyValue $property */
                    foreach ($propertyCollection as $property) {
                        if ($property->getField('CODE') == static::PROPERTY_BUY_BACK_ITERATOR) {
                            $tmpIterator = (int)$property->getValue();
                            $tmpIterator++;
                            $property->setValue($tmpIterator);
                        }
                        if ($property->getField('CODE') == static::PROPERTY_BUY_BACK_CODE) {
                            $property->setValue('');
                        }
                        if ($applyAmount && $property->getField('CODE') == static::PROPERTY_BUY_BACK_BONUSES) {
                            $property->setValue($buybackBonuses + (int)$arResponse['confirmation']['recoveredBonuses']);
                        }
                    }
                    foreach ($basketItems as $basketItem) {
                        if ($basketItem->getQuantity() <= 0) {
                            continue;
                        }
                        if (!$basketItem->canBuy()) {
                            continue;
                        }
                        if (!in_array($offerIdToArticle[$basketItem->getProductId()]['ARTICLE'], array_column($arBuyback['units'], 'sku'))) {
                            $basketItem->delete();
                        }
                    }
                    $order->save();
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
        }
	}

	public static function getBalance($userId = null, $full = false) {

		$parameters = OperationManager::getBalanceParameters($userId);
		$operationName = Operation::OPERATION_GET_BALANCE;
		$operation = new Operation($parameters, $operationName);
		$operation->send();
		$response = $operation->getResponse();
		$arResponse = json_decode($response, true);

		if ($full) {
		    return $arResponse;
        } else {
            $bonusAmount = $arResponse['client']['bonuses'] ?: 0;
            return $bonusAmount;
        }
	}

	public static function getBalanceForPage($userId = null, $full = false) {
	    $name = 'event_getBalanceForPage';
	    if(!isset($GLOBALS[$name])) {
            $GLOBALS[$name] = static::getBalance($userId, $full);
        }

	    return $GLOBALS[$name];
	}

    public static function getHistory($userId = null, $limit = 20, $offset = 0) {
        $parameters = OperationManager::getHistoryParameters($userId, $limit, $offset);
        $operationName = Operation::OPERATION_GET_HISTORY;
        $operation = new Operation($parameters, $operationName);
        $operation->send();
        $response = $operation->getResponse();
        $arResponse = json_decode($response, true);

        return $arResponse;
    }

    public static function existOrderInHistory($userId = null) {
        $limit = 20;
        $offset = 0;
        $list = [];
        do {
            $history = Events::getHistory($userId, $limit, $offset);
            $count = 0;
            if(is_array($history['history'])) {
                foreach ($history['history'] as $item) {
                    if ($item['operation'] == static::TYPE_OPERATION_HISTORY_APPLIED) {
                        return true;
                    }
                }
                $count = count($history['history']);
            }
            $list = array_merge($list, $history['history']);
            $offset += $limit;
        } while($count >= $limit);

        return false;
    }

	public static function calculatePurchase($format = false, $useCloudScore = 'Y') {
        global $USER;
        Events::createExternalInCloudLoyalty();
		$basket = Basket::loadItemsForFUser(Fuser::getId(true), 's1');
		$parameters = OperationManager::getPurchaseParams($basket,false, [], $useCloudScore);
		$operationName = Operation::OPERATION_CALCULATE_PURCHASE;
		$operation = new Operation($parameters, $operationName);
		$operation->send();
		$response = $operation->getResponse();
		$arResponse = json_decode($response, true);

//        pre($parameters, true);
//        pre($arResponse, true);
        if (isset($arResponse['calculationResult']) && !empty($arResponse['calculationResult'])) {
            if(!isset($arResponse['calculationResult']['promocode']) || !$arResponse['calculationResult']['promocode']['applied']){
                OperationManager::deletePromoCode(OperationManager::getLastAppliedPromoCode());
            }
            if(isset($arResponse['calculationResult']['promocode'])) {
                static::$promoCodeResponse = $arResponse['calculationResult']['promocode'];
                OperationManager::setPromoCodeDiscount($arResponse['calculationResult']['summary']['discounts']['promocode']);
            } else {
                static::$promoCodeResponse = false;
                OperationManager::setPromoCodeDiscount(0);
            }
        }
        if(!$USER->IsAuthorized()) {
            if(!isset($arResponse['calculationResult']['promocode']) || !$arResponse['calculationResult']['promocode']['applied']){
                OperationManager::deletePromoCode(OperationManager::getLastAppliedPromoCode());
            }
        }

		if ($format) {
			$titles = ['бонус', 'бонуса', 'бонусов'];
			if (array_key_exists('applied', $arResponse['calculationResult']['bonuses'])) {
				$arResponse['calculationResult']['bonuses']['applied'] = 'Использовано ' . Tools::declension($arResponse['calculationResult']['bonuses']['applied'], $titles);
			}
			if (array_key_exists('collected', $arResponse['calculationResult']['bonuses'])) {
				$arResponse['calculationResult']['bonuses']['collected'] = 'Накоплено всего ' . Tools::declension($arResponse['calculationResult']['bonuses']['collected'], $titles);
			}
			if (array_key_exists('maxToApply', $arResponse['calculationResult']['bonuses'])) {
				$arResponse['calculationResult']['bonuses']['maxToApply'] = 'Доступно ' . Tools::declension($arResponse['calculationResult']['bonuses']['maxToApply'], $titles);
			}
			if(array_key_exists('maxToApply', $arResponse['calculationResult']['bonuses'])) {
                $maxToApplyForThisOrder = $arResponse['calculationResult']['bonuses']['maxToApply'];
                if ($_SESSION["CL_CART_MAX_APPLY"] > 0 && $maxToApplyForThisOrder > $_SESSION["CL_CART_MAX_APPLY"])
                {
                    $maxToApplyForThisOrder = $_SESSION["CL_CART_MAX_APPLY"];
                }

				$arResponse['calculationResult']['bonuses']['maxToApplyForThisOrder'] = 'Доступно для текущего заказа ' . Tools::declension($maxToApplyForThisOrder, $titles);
			}
		} else {
            if(array_key_exists('maxToApply', $arResponse['calculationResult']['bonuses'])) {
                $arResponse['calculationResult']['bonuses']['maxToApplyForThisOrder'] = $arResponse['calculationResult']['bonuses']['maxToApply'];
            }
        }
		return $arResponse['calculationResult']['bonuses'];
	}

    public static function getMaxToApplyByBasket($basket, $isArrayBasket = false, $arUserSend = []) {
        $parameters = OperationManager::getPurchaseParams($basket, $isArrayBasket, $arUserSend);
        $operationName = Operation::OPERATION_CALCULATE_PURCHASE;
        $operation = new Operation($parameters, $operationName);
        $operation->send();
        $response = $operation->getResponse();
        $arResponse = json_decode($response, true);
        return $arResponse['calculationResult']['bonuses']['maxToApply'];
    }

	public static function OnSaleOrderFinalStatus($orderId, $statusID){
		if ($statusID == static::ORDER_FINAL_STATUS_ID) {
			$parameters = ['orderId' => (string)$orderId];
			$operation = new Operation($parameters, Operation::OPERATION_CONFIRM_ORDER);
            Operation::log('OnSaleOrderFinalStatus ' . date('Y-m-d H:i:s'));
			$operation->send();
		}
	}

	public static function onSaleCancelOrder($orderId, $value) {
		if ($value != 'Y') {
			return false;
		}

		$parameters = ['orderId' => (string)$orderId];
		$operation = new Operation($parameters, Operation::OPERATION_CANCEL_ORDER);
        Operation::log('onSaleCancelOrder ' . date('Y-m-d H:i:s'));
		$operation->send();
	}

}