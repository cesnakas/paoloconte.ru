<?
global $MESS;

$MESS["SPCP_DTITLE"] = "Промсвязьбанк";
$MESS["SPCP_DDESCR"] = "</br><ul>
								<li>Сайт банка: <a href=\"http://www.psbank.ru\" target=\"_blank\">Промсвязьбанк</a> <sup><b>(откроется в новом окне)</b></sup></li>
								<li><a href=\"http://https://www.psbank.ru/Business/Everyday/Acquiring/Internet/Apply\" target=\"_blank\">Заявка на подключение к интернет-эквайрингу</a> <sup><b>(откроется в новом окне)</b></sup></li>
								<li>Техническая поддержка по вопросам работы платёжного модуля: <a href=\"mailto:support@it-infinity.ru\" target=\"_blank\">support@it-infinity.ru</a> <sup><b>(откроется в новом окне)</b></sup></li>
								<li>Сайт разработчика модуля: <a href=\"http://www.it-infinity.ru\" target=\"_blank\">www.it-infinity.ru</a> <sup><b>(откроется в новом окне)</b></sup></li>
							</ul>";

$MESS["MERCH_NAME"] = "Название вашего магазина";
$MESS["MERCH_NAME_DESCR"] = "";
$MESS["MERCH_URL"] = "Адрес сайта вашего магазина";
$MESS["MERCH_URL_DESCR"] = "";
$MESS["MERCHANT"] = "Идентификатор магазина";
$MESS["MERCHANT_DESCR"] = "Идентификатор магазина, который получен от банка (MERCHANT)";
$MESS["TERMINAL"] = "Идентификатор V-POS терминала";
$MESS["TERMINAL_DESCR"] = "Идентификатор V-POS терминала, который получен от банка (TERMINAL)";
$MESS["MAC"] = "KEY";
$MESS["MAC_DESCR"] = "MAC-ключ, который получен от банка. Для тестов используйте C50E41160302E0F5D6D59F1AA3925C45";
$MESS["ORDER_DESC"] = "Описание заказа";
$MESS["ORDER_DESC_DESCR"] = "";
$MESS["ORDER_DESC_VAL"] = "Оплата заказа №#ID#";
$MESS["EMAIL"] = "E-mail магазина";
$MESS["EMAIL_DESCR"] = "E-mail для получения уведомлений по результату транзакций";
$MESS["SHOP_RESULT"] = "Адрес страницы на которую будет возвращён клиент после оплаты";
$MESS["SHOP_RESULT_DESCR"] = "Адрес страницы на вашем сайте, куда будет возвращён клиент после работы со службой эквайринга";

$MESS["ORDER_ID"] = "Номер заказа";
$MESS["ORDER_ID_DESCR"] = "";
$MESS["SHOULD_PAY"] = "Сумма заказа";
$MESS["SHOULD_PAY_DESCR"] = "Сумма к оплате";
$MESS["CURRENCY"] = "Валюта";
$MESS["CURRENCY_DESCR"] = "Валюта заказа";

$MESS["IS_TEST"] = "Тестовый режим";
$MESS["IS_TEST_DESCR"] = "Если пустое значение - магазин будет работать в обычном режиме";
$MESS["PAY_BUTTON"] = "Оплатить";

$MESS["EXTCODE"] = "Расширенный ответ";
$MESS["EXTCODE_AS_FAIL"] = "Карточка не прошла аутентификацию у своего банка";
$MESS["EXTCODE_UNAVAIL"] = "Банк клиента не может провести аутентификацию";
$MESS["EXTCODE_AS_ERROR"] = "Технический сбой при 3D аутентификации";
$MESS["ERROR_CHECKSUM"] = "Контрольная сумма не совпадает";
$MESS["ERROR_SUM"] = "Сумма заказа не верна";
$MESS["ERROR_FROM_SERVER"] = "Произвести оплату заказа №#ID# не удалось. Проверьте вводимые данные и попробуйте оплатить заказ заново.";
$MESS["PAYMENT_OK"] = "Оплата заказа №#ID# произведена успешно. Ваш заказ оплачен и в ближайшее время будет отгружен.";

$MESS["ALLOW_DELIVERY"] = "Разрешать доставку";
$MESS["ALLOW_DELIVERY_DESCR"] = "Если Y, то при получении оплаты заказа разрешиться его доставка";
$MESS["PAY_OK"] = "Сообщение при успешной оплате";
$MESS["PAY_OK_DESCR"] = "Укажите сообщение, которое будет видеть пользователь при возврате на сайт после успешной оплаты";
$MESS["PAY_ERROR"] = "Сообщение при ошибке оплаты";
$MESS["PAY_ERROR_DESCR"] = "Укажите сообщение, которое будет видеть пользователь при возврате на сайт при ошибке оплаты";


$MESS["PAY_DEBUG"] = "Включить режим отладки";
$MESS["PAY_DEBUG_DESCR"] = "Если установлено Y, действия платёжной системы записываются в лог файл</br>" . 
"<small>Используйте для отправки в службу технической поддержки. Файлы логов лежат в <b>/bitrix/modules/itinfinity.promsvyazbank/logs/</b></small>";


?>
