<?php

namespace Citfact\Order;


class OrderLogger
{

    private static function log($message = '', $title = '', $pathFolder = '')
    {
        try {
            if ($pathFolder) {
                $path = $_SERVER["DOCUMENT_ROOT"] . $pathFolder;
            } else {
                $path = $_SERVER["DOCUMENT_ROOT"] . "/local/var/log/OrderLogger";
            }
            mkdir($path, 0775, true);
            file_put_contents($path . "/log-" . date('Y-m-d') . ".log",
                'Время лога: ' . date('H:i:s') . PHP_EOL, FILE_APPEND);
            if ($title) {
                $title = ' - ' . $title;
                file_put_contents($path . "/log-" . date('Y-m-d') . ".log",
                    $title . PHP_EOL, FILE_APPEND);
            }
            file_put_contents($path . "/log-" . date('Y-m-d') . ".log", print_r($message, true) . PHP_EOL, FILE_APPEND);
        } catch (\Exception $e) {
        }
    }

    public function logForOnBeforeOrderUpdate($orderId, $arFields)
    {
        if (!$this->isActiveLogForOnBeforeOrderUpdate()) {
            return;
        }
        global $USER;
        static::log('===============');
        static::log('$orderId: ' . $orderId);
        static::log($arFields['DATE_INSERT'], 'DATE_INSERT');
        static::log($arFields['DATE_UPDATE'], 'DATE_UPDATE');;

        if (isset($USER) && is_object($USER)) {
            $userId = $USER->GetID();
            static::log('Заказ изменил пользователь: ' . $USER->GetID() . ', логин:' . $USER->GetLogin());
        } else {
            static::log('Заказ изменил аноним');
        }

        static::log(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));
    }

    private function isActiveLogForOnBeforeOrderUpdate()
    {
        if (\Bitrix\Main\Config\Option::get('sale', 'isActiveLogForOnBeforeOrderUpdate') == 'Y') {
            return true;
        }
        return false;
    }
}