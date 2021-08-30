<?php


/**
 * @TODO Скрипт автоматического приема отчетов о доставке из системы infobip.com. 08.06.2020 отчеты из системы не поступают
 */

file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/local/var/logs/infobip.log', 'NOTIFY RUN:' . json_encode($_POST) . "\n", FILE_APPEND | LOCK_EX);

?>