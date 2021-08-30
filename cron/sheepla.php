<?
$locFileName = '/home/bitrix/ext_www/paoloconte.ru/cron/lock_sheepla.txt';

// Скрипт в определенный момент времени может выполняться только один
if (file_exists($locFileName)) {
	if (time() - filectime($locFileName) > 18000) {
		unlink($locFileName);
	} else {
		die();
	}
}
file_put_contents('/home/bitrix/ext_www/paoloconte.ru/cron/lock_sheepla.txt', 'LOCKED');


define('STOP_STATISTICS', true);

$dir = __DIR__;
if (strpos($dir, '/cron')) {
	$dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');

if(!Loader::IncludeModule("sale") || !Loader::IncludeModule("catalog"))
{
	ShowError("SALE_MODULE_NOT_INSTALLED");
	// Сбрасываем блокировку
	unlink('lock_sheepla.txt');

	return;
}


const ADMIN_KEY = '145da9c9aaaf42aea1d90313d4f27906';
const API_URL = 'https://api.sheepla.com/';

// Количество заказов в пачке
const ITER_COUNT = 20;

// Статусы шиплы, при которых нужно выгрузить заказ в 1С
$sheepla_complete_statuses = array(
	"Доставлено",
	"Доставлен в пункт самовывоза",
	"Полный отказ"
);

$arPropCodes = array(
	'status' => "STATUS_SHEEPLA",
	'substatus' => "SUBSTATUS_SHEEPLA",
	'ctn' => "CTN_SHEEPLA"
);


class SheeplaClientMy
{
	public function sendRequest($method, $request)
	{
		$result = false;

		$context = stream_context_create(array('http' => array(
			'method' => "POST",
			'header' => "Content-type: text/xml; charset=utf-8",
			'content' => $request,
			'content‐length' => strlen($request)
		)));

		if (strlen($request) > 0)
		{
			$result = file_get_contents(API_URL.$method, false, $context);
		}
		else
		{
			$result = file_get_contents(API_URL.$method);
		}
		return $result;
	}
}


function get_count_orders() {
	$arFilter = Array("!CANCELED" => "Y", "STATUS_ID" => "D", '>=ID' => 22000);
	$db_sales = CSaleOrder::GetList(
		array("DATE_INSERT" => "ASC"),
		$arFilter,
		array("CANCELED"),
		false,
		array('ID')
	);
	if ($arSale = $db_sales->Fetch()) {
		return $arSale["CNT"];
	}

	return 0;
}


// Получаем заказы со статусом "Доставляется"
function getOrders($page_number) {
	$arFilter = Array("!CANCELED" => "Y", "STATUS_ID" => "D", '>=ID' => 22000);
	$db_sales = CSaleOrder::GetList(
		array("DATE_INSERT" => "ASC"),
		$arFilter,
		false,
		array(
			"nPageSize" => ITER_COUNT,
			"iNumPage" => $page_number
		),
		array('ID', 'STATUS_ID', 'DELIVERY_ID', 'DATE_INSERT')
	);
	$arSales = array();
	while ($arSale = $db_sales->Fetch())
	{
		if (strpos($arSale['DELIVERY_ID'], 'sheepla') !== false) {
			$arSales[$arSale['ID']] = $arSale;
		}
	}

	return $arSales;
}

// Делаем запрос в шиплу
function createRequest($arSales) {
	// Строим XML
	$strOrderFilter = '<externalOrderIds>';
	foreach($arSales as $ORDER_ID => $arSale){
		$strOrderFilter .= '<externalOrderId>'.$arSale['ID'].'</externalOrderId>';
	}

	$strOrderFilter .= '</externalOrderIds>';

	$request = '
		<getShipmentDetailsRequest xmlns="http://www.sheepla.pl/webapi/1_0">
			<authentication>
				<apiKey>'.ADMIN_KEY.'</apiKey>
			</authentication>
		  <cultureId>1049</cultureId>
		  '.$strOrderFilter.'
		</getShipmentDetailsRequest>';

	// Запрос
	$client = new SheeplaClientMy;
	$response = $client->sendRequest('getShipmentDetails', $request);

	// Строим нормальный массив для вывода
	$xml = new SimpleXMLElement($response);
	$arStatuses = array();
	foreach ($xml->shipments->shipment as $shipment){
		$arTemp = array();
		$arTemp['status'] = (string)$shipment->currentStatusName;
		$arTemp['substatus'] = (string)$shipment->currentSubStatusName;
		$arTemp['ctn'] = (string)$shipment->ctn;
		$arTemp['mod_date'] = strtotime((string)$shipment->lastModificationDate);
		$arTemp['mod_date_format'] = (string)$shipment->lastModificationDate;
		$arStatuses[(int)$shipment->externalOrderId][] = $arTemp;
	}

	return $arStatuses;
}

// Сохраняем инфу из шиплы
function save_info($arSales, $arStatuses, $arPropCodes, $sheepla_complete_statuses) {
	foreach($arSales as $ORDER_ID => $arSale){
		$arLastStatus = array();
		if ($ORDER_ID == 31046) {
			print_r($arStatuses);
		}
		if (!empty($arStatuses[$ORDER_ID])) {
			$arLastStatus = $arStatuses[$ORDER_ID][0];

			foreach ($arStatuses[$ORDER_ID] as $arStatus) {
				// Ищем статус, который обновлялся последним
				if ($arLastStatus['mod_date'] <= $arStatus['mod_date']) {
					$arLastStatus = $arStatus;
				}
			}


			foreach ($arPropCodes as $key => $propCode) {

				$str_to_prop = trim($arLastStatus[$key]);

				//Получаем свойство заказа
				if ($arProp = CSaleOrderProps::GetList(array(), array('CODE' => $propCode))->Fetch()) {
					// Если нашли значение - обновляем его
					// Если не нашли - создаем новое
					if ($arPropValue = CSaleOrderPropsValue::GetList(array("SORT" => "ASC"),
						array(
							"ORDER_ID" => $ORDER_ID,
							"ORDER_PROPS_ID" => $arProp["ID"]
						))->Fetch()
					) {
						CSaleOrderPropsValue::Update($arPropValue['ID'], array("VALUE" => $str_to_prop));
					} else {
						$arFields = array(
							"ORDER_ID" => $ORDER_ID,
							"ORDER_PROPS_ID" => $arProp['ID'],
							"NAME" => $arProp['NAME'],
							"CODE" => $arProp['CODE'],
							"VALUE" => $str_to_prop
						);
						CSaleOrderPropsValue::Add($arFields);
					}

					// Обновляем поле UPDATED_1C у заказа, чтобы он выгрузился впоследствии в 1С
					// при определенных статусах
					if ($key == "status" && array_search($str_to_prop, $sheepla_complete_statuses) !== false) {
						CSaleOrder::Update($ORDER_ID, array("UPDATED_1C" => "N"));
						// file_put_contents($_SERVER['DOCUMENT_ROOT'].'/log.txt', print_r("Update 1C: ".$ORDER_ID, true)."\n", FILE_APPEND | LOCK_EX);
						// file_put_contents($_SERVER['DOCUMENT_ROOT'].'/log.txt', print_r($str_to_prop, true)."\n", FILE_APPEND | LOCK_EX);
					}
				}
			}
		}
	}
}


$orders_count = (int)get_count_orders();
$max_page = ceil($orders_count / ITER_COUNT);
// Скармливаем заказы пачками, потомучто при большом количестве заказов
// запрос в шиплу падает
$page_number = 1;
for ($page_number = 1; $page_number <= $max_page; $page_number++) {
	// Заказчик временно отключил $arSales = getOrders($page_number);
	// file_put_contents($_SERVER['DOCUMENT_ROOT'].'/log.txt', print_r($page_number, true)."\n", FILE_APPEND | LOCK_EX);
	// file_put_contents($_SERVER['DOCUMENT_ROOT'].'/log.txt', print_r(array_keys($arSales), true)."\n", FILE_APPEND | LOCK_EX);
	// Заказчик временно отключил $arStatuses = createRequest($arSales);
	// Заказчик временно отключил save_info($arSales, $arStatuses, $arPropCodes, $sheepla_complete_statuses);
	$page_number++;

	// if ($page_number == 2) {
	// 	break;
	// }
}

// Сбрасываем блокировку
unlink($locFileName);