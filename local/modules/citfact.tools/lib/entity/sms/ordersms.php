<?php

namespace Citfact\Entity\Sms;

use Bitrix\Main\Type\DateTime;
use Citfact\SiteCore\Core;

class OrderSmsRepository
{
    const TABLE_NAME = 'site_core_order_sms';
    const STATUSES = [
        -3 => 'Сообщение не найдено',
        -2 => 'Остановлено',
        -1 => 'Ожидает отправки',
        0 => 'Передано оператору',
        1 => 'Доставлено',
        2 => 'Прочитано',
        3 => 'Просрочено',
        4 => 'Нажата ссылка',
        20 => 'Невозможно доставить',
        22 => 'Неверный номер',
        23 => 'Запрещено',
        24 => 'Недостаточно средств',
        25 =>'Недоступный номер',
        9999 => 'Ошибка запроса',
    ];

    public static function getMessagesByOrderId ($id , $last = false)
    {
        $select = ['ID', 'MESSAGE', 'STATUS', 'DATE_UPDATE', 'ORDER_ID', 'MESSAGE_ID', 'USER_ID'];
        $filter = [
            "=ORDER_ID" => $id
        ];
        $order = ['DATE_UPDATE' => 'desc'];
        $parameters = [
            'select' => $select,
            'filter' => $filter,
            'order' => $order,
        ];
        $resDbElements = OrderSmsTable::getList($parameters);
        $ids = [];
        while ($element = $resDbElements->fetch()) {
            $ids[] = $element;
        }
        return $ids;
    }

    public static function saveMessage ($orderId, $userId, $message, $status, $messageId)
    {
        $select = ['ID'];
        $filter = [
            "=ORDER_ID" => $orderId,
            "=MESSAGE" => $message,
            "=MESSAGE_ID" => $messageId
        ];
        $parameters = [
            'select' => $select,
            'filter' => $filter,
        ];
        $resDbElements = OrderSmsTable::getList($parameters);
        $element = $resDbElements->fetch();
        if ($element) {
            OrderSmsTable::update($element['ID'], [
                'ORDER_ID' => $orderId,
                'USER_ID' => $userId,
                'MESSAGE' => $message,
                'MESSAGE_ID' => $messageId,
                'STATUS' => $status,
            ]);
        } else {
            OrderSmsTable::add(
                [
                    'ORDER_ID' => $orderId,
                    'USER_ID' => $userId,
                    'MESSAGE' => $message,
                    'MESSAGE_ID' => $messageId,
                    'STATUS' => $status,
                ]
            );
        }
    }

    public static function getLastSmsByOrderId ($orderId)
    {
        $result = self::getMessagesByOrderId($orderId, true);
        if (isset($result[0])) {
            return $result[0];
        } else {
            return false;
        }
    }


}