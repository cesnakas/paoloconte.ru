<?

namespace Citfact\InfoBip;

use Bitrix\Main;
use Bitrix\Main\Config\Option;
use Citfact\InfoBip\Proceed;
use Bitrix\Sale;


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

        // 1. Получаем свойства заказа: SMS_MESSAGE_ID / SMS_STATUS
        $order = Sale\Order::load($orderId);
        $arPropertyCollection = $order->getPropertyCollection()->getArray();
        $smsId = $smsMessage = '';
        foreach ($arPropertyCollection['properties'] as $arProperty){
            if ($arProperty['CODE'] == 'SMS_MESSAGE_ID'){
                $smsId = implode('', $arProperty['VALUE']);
            } else if ($arProperty['CODE'] == 'SMS_MESSAGE_TEXT'){
                $smsMessage = implode('', $arProperty['VALUE']);
            }
        }

        if (!empty($smsId)){
            $tabContent = 'Отправлено СМС сообщение: <br>ID: <b>' . $smsId . '</b>';
            if (!empty($smsMessage)){
                $tabContent .= '<br> Текст сообщения: <b>' . $smsMessage . '</b>';
            }
            $form->tabs[] = array(
                'DIV' => 'tab_sms',
                'TAB' => 'СМС отправлено',
                'TITLE' => 'СМС отправлено',
                'CONTENT' => $tabContent
            );
            return;
        }

        // 2. Проверяем наличие реквеста $_REQUEST['smsMessage']
        if (!empty($_REQUEST['smsMessage'])){
            //--Отправляем СМС
            Events::sendSms($orderId, $_REQUEST['smsMessage']);
            echo '<script>location.reload();</script>';
            return;

        }

        // 3. Выводим форму для отправки СМС
        $strSmsMessage1 = Option::get('sale', 'ORDER_SMS_MESSAGE_1');
        $strSmsMessage2 = Option::get('sale', 'ORDER_SMS_MESSAGE_2');
        $strSmsMessage3 = Option::get('sale', 'ORDER_SMS_MESSAGE_3');
        $strSmsMessage4 = Option::get('sale', 'ORDER_SMS_MESSAGE_4');
        $strSmsMessage5 = Option::get('sale', 'ORDER_SMS_MESSAGE_5');

        $formContent = '
        <style>
            input, label{
                cursor: pointer;
                vertical-align: middle;
                margin-top: 0;
            }        
        </style>
        <form method="post" action="#">';

        //--Получить телефон покупаателя
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
        $formContent .= '<p>Выберите текст СМС сообщения на номер <b>'.$userPhone.'</b>:</p>';

        if (!empty($strSmsMessage1)){
            $formContent .= '
            <input type="radio" id="message1" name="smsMessage" value="'.htmlentities($strSmsMessage1).'">
            <label for="message1">'.$strSmsMessage1.'</label><br><br>
            ';
        }
        if (!empty($strSmsMessage2)){
            $formContent .= '
            <input type="radio" id="message2" name="smsMessage" value="'.htmlentities($strSmsMessage2).'">
            <label for="message2">'.$strSmsMessage2.'</label><br><br>
            ';
        }
        if (!empty($strSmsMessage3)){
            $formContent .= '
            <input type="radio" id="message3" name="smsMessage" value="'.htmlentities($strSmsMessage3).'">
            <label for="message3">'.$strSmsMessage3.'</label><br><br>
            ';
        }
        if (!empty($strSmsMessage4)){
            $formContent .= '
            <input type="radio" id="message4" name="smsMessage" value="'.htmlentities($strSmsMessage4).'">
            <label for="message4">'.$strSmsMessage4.'</label><br><br>
            ';
        }
        if (!empty($strSmsMessage5)){
            $formContent .= '
            <input type="radio" id="message5" name="smsMessage" value="'.htmlentities($strSmsMessage5).'">
            <label for="message5">'.$strSmsMessage5.'</label><br><br>
            ';
        }
        $formContent .= '<input type="submit" value="Отправить">';
        $formContent = str_replace('#ORDER_ID#', $orderId, $formContent);
        $formContent  .= '</form>';

        $form->tabs[] = array(
            'DIV' => 'tab_sms',
            'TAB' => 'Отправить СМС',
            'TITLE' => 'Отправить СМС',
            'CONTENT' => $formContent
        );
    }


    function sendSms($orderId, $strSmsMessage, $isCheckBuyer = false){
        $order = Sale\Order::load($orderId);
        $customerId = $order->getField('USER_ID');

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
            return false;
        }

        //--Получаем номер телефона покупателя
        $arUser = \CUser::GetByID($buyerId)->Fetch();
        if (empty($arUser['PERSONAL_PHONE'])) {
            return;
        }
        $userPhone = $arUser['PERSONAL_PHONE'];
        $userPhone = str_replace('-', '', $userPhone);
        $userPhone = str_replace(' ', '', $userPhone);
        $userPhone = str_replace('(', '', $userPhone);
        $userPhone = str_replace(')', '', $userPhone);

        //--Защита от многократной отправки СМС
        foreach ($order->getPropertyCollection() as $property) {
            if ($property->getField('CODE') == 'SMS_MESSAGE_ID') {
                $smsMessageValue = $property->getValue();
                if (!empty($smsMessageValue)){
                    return;
                }
            }
        }

        //--Отправляем СМС
        $parameters = array(
            'messages' => array(
                'from' => 'PaoloConte',
                'text' => $strSmsMessage,
                'destinations' => array(
                    'to' => $userPhone,
                    'messageId' => 'smsPaolo-Order-' . $order->getId() . '-Buyer-' . $buyerId,
                ),
                'callbackData' => $order->getId(),
                'language' => array(
                    'languageCode' => 'RU'
                ),
                'notifyUrl' => Proceed::OPERATION_NOTIFY,
                'notifyContentType' => 'application/json',

                'flash' => false,
                'intermediateReport' => true,
                'validityPeriod' => 720
            ),
        );

        $operationName = Proceed::OPERATION_POST_SEND_SMS;

        $operation = new Proceed($parameters, $operationName);
        Proceed::log('sendSms ' . date('Y-m-d H:i:s'));
        $operation->send();
        $response = $operation->getResponse();
        $arResponse = json_decode($response, true);

        //{"messages":[{"to":"+71212131313","status":{"groupId":1,"groupName":"PENDING","id":26,"name":"PENDING_ACCEPTED","description":"Message sent to next instance"},"messageId":"smsPaolo-Order-112551-Buyer-310866"}]}

        //--Прописываем статус СМС в заказ
        $smsStatus = 'READY';
        if (!empty($arResponse['messages'][0]['status']['groupName'])) {
            switch ($arResponse['messages'][0]['status']['groupName']) {
                case 'ACCEPTED':
                case 'PENDING':
                    $smsStatus = 'PENDING';
                    break;
                case 'DELIVERED':
                    $smsStatus = 'DELIVERED';
                    break;
                case 'UNDELIVERABLE':
                case 'EXPIRED':
                case 'REJECTED':
                    $smsStatus = 'UNDELIVERABLE';
                    break;
            }
        }

        $smsMessage = 'отсутствует';
        if (!empty($arResponse['messages'][0]['messageId'])) {
            $smsMessage = $arResponse['messages'][0]['messageId'];
        }

        foreach ($order->getPropertyCollection() as $property) {
            if ($property->getField('CODE') == 'SMS_MESSAGE_ID') {
                $property->setValue($smsMessage);
                continue;
            }
            if ($property->getField('CODE') == 'SMS_STATUS') {
                $property->setValue($smsStatus);
                continue;
            }
            if ($property->getField('CODE') == 'SMS_MESSAGE_TEXT') {
                $property->setValue($strSmsMessage);
                continue;
            }
        }
        $saveResult = $order->getPropertyCollection()->save();
        $order->save();
        return $saveResult;
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
        Events::sendSms($order->getId(), $strSmsMessage, true);

        $order->setField("STATUS_ID", "P");
        $order->save();
    }

}