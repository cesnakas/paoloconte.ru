<?header('Content-type: text/html; charset="windows-1251"',true);
//setlocale(LC_ALL, 'ru_RU');
// перекуячиваем кодировку

//if ($_SERVER['REMOTE_ADDR']!='127.0.0.1') die(); // Защита от постороннего запуска
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {echo "die"; die();}
//echo '<pre>', print_r($_REQUEST), '</pre>';

use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');

$res = array('error'=>'', 'result'=>'');
if ( Loader::includeModule('iblock') && $_REQUEST['action'] != '' )
{
	if ($_REQUEST['action']=='region-show-cities-list' && $_REQUEST['request'] != ''){
		$region_name = $_REQUEST['request'];
		$res['result']['region'] = Citfact\Paolo::GetRegionSettings($region_name);
		$res['result']['cities'] = Citfact\Paolo::GetCities($region_name);
		$res['action'] = $_REQUEST['action'];
	}
}
else{
	$res['error'] = 'Ошибка подключения модулей.';
}

$res = json_encode($res);
echo $res;

require($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/epilog_after.php');
?>