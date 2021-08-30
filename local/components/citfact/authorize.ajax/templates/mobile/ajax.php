<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

global $USER;
$arReturn = array('errors'=>array(), 'result'=>array());

//echo "<pre style=\"display:block;\">"; print_r($_POST); echo "</pre>";

$email = htmlspecialcharsbx(trim($_POST['EMAIL']));
$pass = htmlspecialcharsbx($_POST['PASSWORD']);

// Если пользователь уже существует, пытаемся его авторизовать
$rsUser = $USER->GetByLogin($email);
if($arUser = $rsUser->Fetch()){
	$arAuthResult = $USER->Login($email, $pass, "Y");
	if ($arAuthResult !== true) {
		$arReturn['errors'][] = $arAuthResult['MESSAGE'];
	}
}
else{
	$arReturn['errors'][] = Loc::getMessage('USER_NOT_FOUND');
}

$strReturn = json_encode($arReturn);
echo $strReturn;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>