<?

namespace Citfact\Smsc;

use Bitrix\Main;
use Bitrix\Main\Config\Option;
use Bitrix\Sale;
use Citfact\Entity\Sms\OrderSmsRepository;
use Bitrix\Main\Type\DateTime;


class Events
{

    function OnAdminOrderPage(&$form){

        global $APPLICATION;

        $curPage = $APPLICATION->GetCurPage();
        if ($curPage != '/bitrix/admin/sale_order_view.php'){
            return;
        }
        $enableSmsActivity = Option::get('sale', 'ORDER_ON_SMS_ACTIVITY');

        if ($enableSmsActivity != 'Y'){
            return;
        }

        $orderId = intval($_REQUEST['ID']);
        if (!$orderId){
            return;
        }

        $userPhone = Events::getCustomerPhone($orderId);

        if (!empty($_REQUEST['smsMessage'])){
            //--Отправляем СМС
            $resultSending = Events::sendSms($orderId, $_REQUEST['smsMessage'], $userPhone);
            $backToSMSTab = true;
        }
        $resultUpdating = [];
        $result = OrderSmsRepository::getMessagesByOrderId($orderId);
        if (!empty($_REQUEST['updateSms'])) {
            $resultUpdating = Events::updateSms($orderId, $result);
            $backToSMSTab = true;
        }
        $tabContent = Events::render('list', [
            'arResult' => $result,
            'orderId' => $orderId,
            'userPhone' => $userPhone,
            'resultSending' => isset($resultSending) ? $resultSending : false,
            'resultUpdating' => isset($resultUpdating) ? $resultUpdating : false,
            'backToSMSTab' => isset($backToSMSTab) ? $backToSMSTab : false,
        ]);
        $form->tabs[] = array(
            'DIV' => 'tab_sms',
            'TAB' => 'СМС отправлено',
            'TITLE' => 'СМС отправлено',
            'CONTENT' => $tabContent
        );
    }

     function render ($template, $vars){
        ob_start();
        extract($vars);
        require(__DIR__ . '/views/' . $template . '.php');
        $output = ob_get_clean();
        return $output;
    }

    function updateSms($orderId, $messages) {
        $final = [];
        $userPhone = Events::getCustomerPhone($orderId);
        $userPhone = str_replace('-', '', $userPhone);
        $userPhone = str_replace(' ', '', $userPhone);
        $userPhone = str_replace('(', '', $userPhone);
        $userPhone = str_replace(')', '', $userPhone);
        foreach ($messages as $sms) {
            $result = Events::customUpdateStatus($sms, $userPhone);
            if ($sms['STATUS'] !== $result[0]) {
                $status = $result[0];
                if (sizeof($result) < 3) {
                    $status = 9999;
                }
                OrderSmsRepository::saveMessage(
                    $orderId, $sms['USER_ID'], $sms['MESSAGE'], $status, $sms['MESSAGE_ID']);
            }
            $final[] = $result;
        }
        return $final;
    }

    function getCustomerPhone ($orderId) {
        //--Получить телефон покупаателя
        $order = Sale\Order::load($orderId);
        $customerId = $order->getField('USER_ID');
        $arFilter = array("USER_ID" => $customerId);
        $buyersFilter = [];
        $buyersFilter['filter'] = $arFilter;
        $buyersFilter['select'] = array('USER_ID');
        $buyersFilter['order'] = array("USER_ID" => "ASC");
        $buyersData = \Bitrix\Sale\BuyerStatistic::getList($buyersFilter);
        $buyerId = 0;
        while ($buyer = $buyersData->fetch()) {
            $buyerId = $buyer['USER_ID'];
        }

        //--Получаем номер телефона покупателя
        $arUser = \CUser::GetByID($buyerId)->Fetch();
        $userPhone = '';
        if (!empty($arUser['PERSONAL_PHONE'])) {
            $userPhone = $arUser['PERSONAL_PHONE'];
        }
        return $userPhone;
    }

    function sendSms($orderId, $strSmsMessage,  $userPhone, $isCheckBuyer = false){

        global $USER;
        $order = Sale\Order::load($orderId);
        $customerId = $order->getField('USER_ID');

        $result = array();

        $arFilter = array(
            "USER_ID" => $customerId
        );

        $buyersFilter = [];
        $buyersFilter['filter'] = $arFilter;
        $buyersFilter['select'] = array(
            'USER_ID',
            'COUNT_FULL_PAID_ORDER',
            'SUM_PAID',
        );

        $buyersFilter['order'] = array(
            "USER_ID" => "ASC"
        );

        $buyersData = \Bitrix\Sale\BuyerStatistic::getList($buyersFilter);
        $isBuyerTrusted = false;
        $buyerId = 0;

        while ($buyer = $buyersData->fetch()) {
            $buyerId = $buyer['USER_ID'];
            if (!empty($buyer['COUNT_FULL_PAID_ORDER'])) {
                $isBuyerTrusted = true;
                break;
            }
        }

        if ($isCheckBuyer && !$isBuyerTrusted){
            $result['error'] = 'Контрагент не проверен';
            return $result;
        }

        if (empty($userPhone)) {
           $result['error'] = 'Телефон контрагента не найден';
            return $result;
        }
        $userPhone = str_replace('-', '', $userPhone);
        $userPhone = str_replace(' ', '', $userPhone);
        $userPhone = str_replace('(', '', $userPhone);
        $userPhone = str_replace(')', '', $userPhone);

        //--Защита от частой отправки СМС
        $timePeriod = Option::get('sale', 'TIMEOUT_SMS');
        if (!empty($timePeriod)) {
            $lastSMS = OrderSmsRepository::getLastSmsByOrderId($orderId);
            if (!empty($lastSMS)) {
                $lastTime = $lastSMS['DATE_UPDATE'];
                $now = new DateTime();
                $timeDiff = $now->getTimestamp() - $lastTime->getTimestamp();
                if ($timePeriod > $timeDiff) {
                    $result['error'] = 'Слишком частая отправка сообщений';
                    return $result;
                }
            }
        }
        $result['sms'] = Events::customSendSMS($userPhone, $strSmsMessage);
        if ($result['sms'] === false) {
            $result['error'] = 'Поле sms не заполнено';
            return $result;
        } elseif ($result['sms'][1] <= 0) {
            $result['error'] = 'Произошла ошибка при отправке SMS';
        }

        OrderSmsRepository::saveMessage(
            $orderId, $userId=$USER->GetID(), $strSmsMessage, $result['sms'][1], $result['sms'][0]);
        return $result;

    }

    function customUpdateStatus ($message, $phone) {
        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/smsc.sms/classes/general/smsc_api.php");
        if(strlen(trim($message['MESSAGE']))<=0) return false;

        $user = \COption::GetOptionString('smsc.sms', 'login');
        $pass = \COption::GetOptionString('smsc.sms', 'password');

        define("SMSC_LOGIN", $user);
        define("SMSC_PASSWORD", $pass);

        $result = \get_status($message['MESSAGE_ID'], $phone);
        return $result;
    }

    function customSendSMS ($phone, $message, $translit = 0, $time = 0, $format = 0, $sender = false, $encoding = LANG_CHARSET)
    {
        require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/smsc.sms/classes/general/smsc_api.php");

        if(strlen(trim($message))<=0) return false;

        $sender = trim($sender) ?: \COption::GetOptionString('smsc.sms', 'default_from');


        $user = \COption::GetOptionString('smsc.sms', 'login');
        $pass = \COption::GetOptionString('smsc.sms', 'password');

        define("SMSC_LOGIN", $user);
        define("SMSC_PASSWORD", $pass);
        define("CHARSET", $encoding);

        $result = \send_sms($phone, $message, $translit, $time, 0, $format, $sender, 'charset='.CHARSET);

        return $result;
    }

    /**
     * отправляем СМС заказчику при смене статуса заказа на "Согласуется"
     * @param Main\Event $event
     */
    function OnSaleStatusOrderSendSms(\Bitrix\Main\Event $event)
    {
        $statusCode = $event->getParameter('VALUE');

        if ($statusCode != 'C') {
            return true;
        }
        $enableSmsActivityAuto = Option::get('sale', 'ORDER_ON_SMS_ACTIVITY_AUTO');
        $enableSmsActivity = Option::get('sale', 'ORDER_ON_SMS_ACTIVITY');

        if ($enableSmsActivity != 'Y' || $enableSmsActivityAuto != 'Y') {
            return;
        }

        $parameters = $event->getParameters();
        $order = $parameters['ENTITY'];

        $strSmsMessage = Option::get('sale', 'ORDER_SMS_MESSAGE');
        $strSmsMessage = str_replace('#ORDER_ID#', $order->getId(), $strSmsMessage);

        //В заказе указан любой способ доставки, кроме самовывоза
        $deliveryIds = $order->getDeliverySystemId();
        $arDeliverySamovyvoz = array(
            2,  //Самовывоз
            42, //СДЕК -  Пункт самовывоза. Оплата на месте
            39, //СДЕК - Пункт самовывоза. Оплата онлайн на сайте
        );
        foreach ($deliveryIds as $chosenDelivery) {
            if (in_array($chosenDelivery, $arDeliverySamovyvoz)) {
                return;
            }
        }

        //В заказе все товары есть в наличии (нет в комментарии слов "отсутствует", либо "долг за контрагентом")
        $managerComment = mb_strtolower($order->getField('COMMENTS'));     //Комментарий менеджер           /   COMMENTS
        $responsibleComment = '';                                           //Комментарий ответственного    /	RESPONSIBLE_COMMENT

        $propertyCollection = $order->getPropertyCollection();
        $arProperties = $propertyCollection->getArray();

        foreach ($arProperties['properties'] as $property) {
            if ($property['CODE'] == 'RESPONSIBLE_COMMENT') {
                foreach ($property['VALUE'] as $comment) {
                    $responsibleComment .= mb_strtolower($comment);
                }
            }
        }

        $disableMarker = 'отсутствует';
        $disableMarker2 = 'долг за контрагентом';
        if (
            strpos($managerComment, $disableMarker) !== false ||
            strpos($managerComment, $disableMarker2) !== false ||
            strpos($responsibleComment, $disableMarker) !== false ||
            strpos($responsibleComment, $disableMarker2) !== false
        ) {
            return;
        }
        $userPhone = Events::getCustomerPhone($order->getId());
        Events::sendSms($order->getId(), $strSmsMessage, $userPhone, true);

        $order->setField("STATUS_ID", "P");
        $order->save();
    }

}