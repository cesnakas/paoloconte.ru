<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;
$arReturn = array('errors'=>array(), 'result'=>array());

//echo "<pre style=\"display:block;\">"; print_r($_POST); echo "</pre>";

$name = htmlspecialcharsbx(trim($_POST['NAME']));
$secondname = htmlspecialcharsbx(trim($_POST['SECOND_NAME']));
$lastname = htmlspecialcharsbx(trim($_POST['LAST_NAME']));
$phone = htmlspecialcharsbx(trim($_POST['PHONE']));
$email = htmlspecialcharsbx(trim($_POST['EMAIL']));
$pass = htmlspecialcharsbx(trim($_POST['PASSWORD']));
$confirm_pass = htmlspecialcharsbx(trim($_POST['CONFIRM_PASSWORD']));

$friend_email = htmlspecialcharsbx(trim($_POST['UF_FRIEND_EMAIL']));
$cardnumber = htmlspecialcharsbx(trim($_POST['UF_CARDNUMBER']));

// Если пользователь уже существует, пытаемся его авторизовать
$rsUser = $USER->GetByLogin($email);
if($arUser = $rsUser->Fetch()){
	$arReturn['errors'][] = 'Пользователь с таким email уже зарегистрирован.';
	/*$arAuthResult = $USER->Login($email, $pass, "Y");
	if ($arAuthResult !== true) {
		$arReturn['errors'][] = $arAuthResult['MESSAGE'];
	}*/
}
// Если пользователь не существует, создаем пользователя
else {
	// Создаем пользователя
	$arFields = Array(
		"LOGIN" => $email,
		"EMAIL" => $email,
		"NAME" => $name,
		"SECOND_NAME" => $secondname,
		"LAST_NAME" => $lastname,
		"PERSONAL_PHONE" => $phone,
		"ACTIVE" => "Y",
		"PASSWORD" => $pass,
		"CONFIRM_PASSWORD" => $confirm_pass,
		"UF_FRIEND_EMAIL" => $friend_email,
//		"UF_SUBSCRIBE" => $_POST['subscribe'],
		"UF_SUBSCRIBE" => 4,
		"UF_CARDNUMBER" => $cardnumber
	);

	$ID = $USER->Add($arFields);
	if (intval($ID) > 0) {
		$arReturn['result'][] = "Вы успешно зарегистрированы.";
		//$arReturn['result'][] = iconv('windows-1251', 'UTF-8', "Пароль: " . $pass);

		// Пытаемся авторизовать пользователя
		$arAuthResult = $USER->Login($email, $pass, "Y");
		if ($arAuthResult !== true) {
			$arReturn['errors'][] = $arAuthResult['MESSAGE'];
		}

		/*$arEventFields = array(
			"USERNAME" => $_REQUEST['username'],
			"PHONE" => $_REQUEST['phone'],
			"LOGIN" => $email,
			"MAILTO" => $email,
			//"PASSWORD" => $pass,
		);
		CEvent::SendImmediate("BLOG_NEW_USER", 's1', $arEventFields);*/

		// Если указывался email друга, то ищем пользователя с таким email и выдаем ему купон на 100500 рублей
		if ($friend_email != ''){
			$filter = Array("EMAIL" => $friend_email);
			$rsUsers = CUser::GetList(($by="ID"), ($order="asc"), $filter); // выбираем пользователей
			if($arUser = $rsUsers->Fetch()){

				if (CModule::IncludeModule("catalog"))
				{
					$COUPON = CatalogGenerateCoupon();

					$arCouponFields = array(
						"DISCOUNT_ID" => "4",
						"ACTIVE" => "Y",
						"ONE_TIME" => "Y",
						"COUPON" => $COUPON,
						"DATE_APPLY" => false,
						'DESCRIPTION' => 'USER_ID='.$arUser['ID']
					);

					$CID = CCatalogDiscountCoupon::Add($arCouponFields);
					$CID = IntVal($CID);
					if ($CID <= 0)
					{
						$ex = $APPLICATION->GetException();
						$errorMessage = $ex->GetString();
						$arReturn['errors'][] = $errorMessage;
					}
					else{
						// Отсылаем письмо с купоном другу
						$arEventFields = array(
							"USERNAME" => $arUser['NAME']/*.' '.$arUser['LAST_NAME']*/,
							"COUPON" => $COUPON,
							"EMAIL" => $arUser['EMAIL'],
						);
						CEvent::Send("COUPON_FRIEND", 's1', $arEventFields);
						$arReturn['EVENT_ARR'] = $arEventFields;
					}
				}

			}
		}

	} else {
		$arReturn['errors'][] = $USER->LAST_ERROR;
	}
}

$strReturn = json_encode($arReturn);
echo $strReturn;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>