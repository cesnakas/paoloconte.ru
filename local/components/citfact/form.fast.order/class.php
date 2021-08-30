<?php
use \Bitrix\Main\Localization\Loc as Loc;
use \Bitrix\Main\SystemException as SystemException;
use \Bitrix\Main\Loader as Loader;
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class FormFastOrder extends CBitrixComponent
{
    private $post = array();
    private $notValidField = array();
    private $basket = array();
    private $userInfo = array();
    private $firstError = '';
    private $redirectPage = '';
    private $userID = 0;
    private $orderID = 0;
    private $order = NULL;
    private $payment = NULL;
    private $shipment = NULL;
    private $orderPropsData = NULL;
    private $count = array(
        'LOGIN' => 0,
        'MAX_LOGIN' => 50,
        'EMAIL' => 0,
        'MAX_EMAIL' => 50,
    );

    /**
     * @param $params
     * @override
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $params['ID_COMPONENT'] = (!empty($params['ID_COMPONENT'])) ? $params['ID_COMPONENT'] : 'personal_fast_order';
        $params['AUTH'] = ($params['AUTH'] == 'Y') ? true : false ;

        $this->arParams = $params;
        return $params;
    }

    /**
     * @override
     * @throws Exception
     */
    protected function checkModules()
    {
        if(!Loader::includeModule("sale"))
            throw new SystemException(Loc::getMessage("CVP_SALE_MODULE_NOT_INSTALLED"));
    }

    /**
     * @override
     * @throws Exception
     */
    protected function checkParams()
    {
        if (intval($this->arParams['PAY_ID']) <= 0)
            throw new SystemException(Loc::getMessage("CVP_SALE_PAY_ID_NOT"));

        if (intval($this->arParams['DELIVERY_ID']) <= 0)
            throw new SystemException(Loc::getMessage("CVP_SALE_DELIVERY_ID_NOT"));

        if (intval($this->arParams['PERSON_TYPE_ID']) <= 0)
            throw new SystemException(Loc::getMessage("CVP_SALE_PERSON_TYPE_ID_NOT"));
    }

    /**
     * @param $post
     */
    private function setPost($post) {
        $this->post = json_decode($post['data'], true);
        $this->onPrepareComponentParams(json_decode($post['params'], true));
    }

    private function setFisrtError ($error) {
        if (empty($this->firstError)) {
            $this->firstError = $error;
        }
    }

    /**
     * @return bool
     */
    private function checkFields () {
        if (empty($this->post[$this->arParams['ID_COMPONENT']])) {
            return false;
        }

        foreach ($this->post as $name => $value) {
            switch ($name) {
                case "yarobot":
                    if (!empty($value)) {
                        return false;
                    }
                    break;
                case "PHONE":
                case "NAME":
                    if (empty($value)) {
                        $this->notValidField[] = $name;
                    }
            }
        }
        if (!empty($this->notValidField)) {
            $this->setFisrtError(Loc::getMessage('CVP_NOT_VALID'));
            return false;
        } else {
            return true;
        }
    }

    public function getNotValidFields () {
        return $this->notValidField;
    }

    public function getFirstError () {
        return $this->firstError;
    }

    private function checkBasket () {
        if (
            empty($this->basket)
            || $this->basket->count() <= 0
        ) {
            $this->setFisrtError(Loc::getMessage('CVP_EMPTY_BASKET'));
            return false;
        } else {
            return true;
        }
    }

    private function setBasket () {
        $this->basket = \Bitrix\Sale\Basket::loadItemsForFUser(\CSaleBasket::GetBasketUserID(), $this->getSiteId());
    }

    private function doCreateOrder () {
        $this->order = \Bitrix\Sale\Order::create($this->getSiteId(), $this->userID);
        $this->order->setPersonTypeId($this->arParams['PERSON_TYPE_ID']);
        $this->order->setBasket($this->basket);
    }


    private function setShipment () {
        if (
            !$this->order
            || !empty($this->firstError)
        )
            return;

        $shipmentCollection = $this->order->getShipmentCollection();
        $this->shipment = $shipmentCollection->createItem(
            $resewweg = \Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->arParams['DELIVERY_ID'])
        );

        $shipmentItemCollection = $this->shipment->getShipmentItemCollection();
        foreach ($this->basket as $basketItem) {
            $item = $shipmentItemCollection->createItem($basketItem);
            $item->setQuantity($basketItem->getQuantity());
        }
    }

    private function setOrderProps () {
        $this->getOrderPropsData();
        $propertyCollection  = $this->order->getPropertyCollection();
        foreach ($this->orderPropsData as $name => $idProp) {
            $somePropValue = $propertyCollection->getItemByOrderPropertyId($idProp);
            switch ($name) {
                case 'NAME':
                    $somePropValue->setValue($this->post['NAME']);
                    break;
                 case 'PHONE':
                     $somePropValue->setValue($this->post['PHONE']);
                     break;
                 case 'EMAIL':
                     $somePropValue->setValue($this->post['EMAIL']);
                     break;
            }
        }
    }

    private function getOrderPropsData () {
        $res = \Bitrix\Sale\Internals\OrderPropsTable::getList(array(
            'filter' => array(
                'PERSON_TYPE_ID' => $this->arParams['PERSON_TYPE_ID'],
                array(
                    'LOGIC' => 'OR',
                    array('IS_EMAIL' => 'Y'),
                    array('IS_PAYER' => 'Y'),
                    array('IS_PHONE' => 'Y'),
                )
            ),
            'select' => array(
                'ID',
                'IS_EMAIL',
                'IS_PAYER',
                'IS_PHONE',
            )
        ));
        while ($arRes = $res->Fetch()) {
            if ($arRes['IS_EMAIL'] == 'Y') {
                $this->orderPropsData['EMAIL'] = $arRes['ID'];
            } else if($arRes['IS_PAYER'] == 'Y') {
                $this->orderPropsData['NAME'] = $arRes['ID'];
            }else if($arRes['IS_PHONE'] == 'Y') {
                $this->orderPropsData['PHONE'] = $arRes['ID'];
            }
        }
    }


    private function setPayment () {
        if (
            !$this->order
            || !empty($this->firstError)
        )
            return;

        $paymentCollection = $this->order->getPaymentCollection();
        $this->payment = $paymentCollection->createItem(
            \Bitrix\Sale\PaySystem\Manager::getObjectById($this->arParams['PAY_ID'])
        );
        $this->payment->setField("SUM", $this->order->getPrice());
        $this->payment->setField("CURRENCY", $this->order->getCurrency());
    }


    public function saveOrder ($post) {
        $this->setPost($post);
        $this->checkParams();
        if (!$this->checkFields()) {
            return;
        }

        if (!empty($this->firstError))
            return;

        $this->setBasket();
        if (!$this->checkBasket()) {
            return;
        }

        $this->setUser();
        if ($this->userID <= 0) {
            $this->setFisrtError(Loc::getMessage('CVP_NOT_USER'));
            return;
        }

        $this->doCreateOrder();
        $this->setShipment();
        $this->setPayment();
        $this->setOrderProps();

        $result = $this->order->save();
        if (!$result->isSuccess()) {
            $this->setFisrtError($result->getErrors());
        } else {
            $this->orderID = $result->getID();
            $this->authNewUser();
            $this->setRedirect();
        }
    }

    private function authNewUser () {
        global $USER;
        if (
            !$USER->IsAuthorized()
            && $this->arParams['AUTH'] == 'Y'
        ) {
            $user = new \CUser();
            $user->Authorize($this->userID);
        }
    }

    public function getOrderID () {
        return $this->orderID;
    }

    public function setRedirect () {
        if (!empty($this->arParams['PAGE_CONFIRM'])) {
            if (strpos($this->arParams['PAGE_CONFIRM'], '#ORDER_ID#') !== false) {
                $this->redirectPage = str_replace('#ORDER_ID#', $this->orderID, $this->arParams['PAGE_CONFIRM']);
            } else {
                $this->redirectPage = $this->arParams['PAGE_CONFIRM'];
            }
        }
    }

    public function getRedirect () {
        return $this->redirectPage;
    }

    private function setUserInfo () {
        global $USER;
        if (!$USER->IsAuthorized()) {
            $this->setPhone();
            $this->generateUserLogin();
            $this->generateUserEmail();
            $this->generatePassword();
        }
    }

    private function setPhone () {
        $this->userInfo['PHONE'] = preg_replace("/[^0-9]/", '', strval($this->post['PHONE']));
    }

    private function generateUserLogin () {
        $this->userInfo['LOGIN'] = "OC_" . $this->userInfo['PHONE'] . "_" . GetRandomCode(3);
        if ($this->checkUserLogin()) {
            $this->generateUserLogin();
        }
    }

    private function checkUserLogin () {
        $this->count['LOGIN']++;
        if ($this->count['LOGIN'] >= $this->count['MAX_LOGIN']) {
            return false;
        }
        $res = \Bitrix\Main\UserTable::getlist(array(
            'filter' => array(
                'LOGIN' => $this->userInfo['LOGIN']
            ),
            'select' => array(
                'ID'
            ),
            'limit' => '1'
        ));
        if ($arRes = $res->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    private function generateUserEmail () {
        $this->userInfo['EMAIL'] = $this->userInfo['LOGIN']. GetRandomCode(3) ."@".SITE_SERVER_NAME;
        if ($this->checkUserEmail()) {
            $this->generateUserEmail();
        }
    }

    private function checkUserEmail () {
        $this->count['EMAIL']++;
        if ($this->count['EMAIL'] >= $this->count['MAX_EMAIL']) {
            return false;
        }
        $res = \Bitrix\Main\UserTable::getlist(array(
            'filter' => array(
                'EMAIL' => $this->userInfo['EMAIL']
            ),
            'select' => array(
                'ID'
            ),
            'limit' => '1'
        ));
        if ($arRes = $res->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    private function generatePassword () {
        $password_chars = array(
            "abcdefghijklnmopqrstuvwxyz",
            "ABCDEFGHIJKLNMOPQRSTUVWX­YZ",
            "0123456789",
            "!@#\$%^&*()",
        );
        $this->userInfo['PASSWORD'] = randString(8, $password_chars);
    }

    /**
     * @return int
     */
    private function getNewUser () {
        $this->setUserInfo();
        $fields = Array(
            "LOGIN" => $this->userInfo['LOGIN'],
            "NAME" => $this->post['NAME'],
            "PERSONAL_PHONE" => $this->userInfo['PHONE'],
            "PASSWORD" => $this->userInfo['PASSWORD'],
            "CONFIRM_PASSWORD" => $this->userInfo['PASSWORD'],
            "EMAIL" => $this->userInfo['EMAIL'],
            "ACTIVE" => "Y",
            "LID" => SITE_ID,
        );
        $user = new CUser;
        $ID = $user->Add($fields);
        if (intval($ID) <= 0)
        $this->setFisrtError($user->LAST_ERROR);

        return intval($ID);
    }

    /**
     * @return int
     */
    private function setUser () {
        global $USER;
        if ($USER->IsAuthorized()) {
            $this->userID = $USER->GetID();
        } else {
            $this->userID = $this->getNewUser();
        }
    }

    private function prepareData () {
        $this->setBasket();
        if (!$this->checkBasket()) {
            $this->arResult['BASKET_EMPTY'] = true;
        }

        $this->arResult['ERROR_TEXT'] = $this->firstError;
    }

    /**
     * Start Component
     */
    public function executeComponent()
    {
        try
        {
            $this->checkModules();
            $this->checkParams();
            $this->prepareData();
            $this->includeComponentTemplate();
        }
        catch (SystemException $e)
        {
            ShowError($e->getMessage());
        }
    }
}