<?
use Bitrix\Sale\Internals;
use Citfact\CloudLoyalty\Events;
use Citfact\CloudLoyalty\OperationManager;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;
$arReturn = array('errors'=>array(), 'result'=>array());

function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

$name = htmlspecialcharsbx(trim($_POST['NAME']));
$secondname = htmlspecialcharsbx(trim($_POST['SECOND_NAME']));
$lastname = htmlspecialcharsbx(trim($_POST['LAST_NAME']));
$bdate = htmlspecialcharsbx(trim($_POST['BIRTH_DATE']));

if (validateDate($bdate, 'Y-m-d')) {
    $objDateTime = new \Bitrix\Main\Type\DateTime($bdate, "Y-m-d");
} elseif (validateDate($bdate, 'd.m.Y')) {
    $objDateTime = new \Bitrix\Main\Type\DateTime($bdate, "d.m.Y");
} elseif (validateDate($bdate, 'd-m-Y')) {
    $objDateTime = new \Bitrix\Main\Type\DateTime($bdate, "d-m-Y");
} elseif (validateDate($bdate, 'd/m/Y')) {
    $objDateTime = new \Bitrix\Main\Type\DateTime($bdate, "d/m/Y");
} else {
    $arReturn['errors'][] = 'Дата рождения введена некорректно';
}

$phone = htmlspecialcharsbx(trim($_POST['PHONE']));
$email = htmlspecialcharsbx(trim($_POST['EMAIL']));
$pass = htmlspecialcharsbx(trim($_POST['PASSWORD']));
$confirm_pass = htmlspecialcharsbx(trim($_POST['CONFIRM_PASSWORD']));
$friend_email = htmlspecialcharsbx(trim($_POST['UF_FRIEND_EMAIL']));
$cardNumber = htmlspecialcharsbx(trim($_POST['UF_CARDNUMBER']));
$cardNumber = str_replace('-','', $cardNumber);
//$captcha_code = htmlspecialcharsbx(trim($_POST['captcha_code']));
//$captcha_word = htmlspecialcharsbx(trim($_POST['captcha_word']));

//if(!$APPLICATION->CaptchaCheckCode($captcha_word, $captcha_code)){
//$arReturn['errors'][] = 'Введите цифры с картинки';
//$arReturn['result']['captcha'] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());

//
if(empty($cardNumber)){
    $cardNumber = Events::getCardIdForPhone($phone);
}

if(!empty($cardNumber) && !Events::checkUserInCloudloyaltyCardAndPhone($cardNumber, $phone)){
    $arReturn['errors'][] = 'Карта '. $cardNumber . ' прикреплена к другому телефону' .$phone.'<br/>';
}

if(!empty($cardNumber) && Events::checkCardUserInBitrix($cardNumber)){
    $arReturn['errors'][] = 'Пользователь с такой картой уже зарегистрирован на сайте<br/>';
}

// Google Recaptcha
require_once("../../googlecaptcha.php");

$errorMessages = GoogleReCaptcha::checkClientResponse();
if(!empty($errorMessages))
{
    $arReturn["errors"][] = $errorMessages;
}


if(empty($arReturn['errors'])) {
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
        if(empty($cardNumber)){

            $cardNumber = OperationManager::generateCardNumber();
        }
        // Создаем пользователя
        $arFields = Array(
            "LOGIN" => $email,
            "EMAIL" => $email,
            "NAME" => $name,
            "SECOND_NAME" => $secondname,
            "LAST_NAME" => $lastname,
            "PERSONAL_BIRTHDAY" => $objDateTime,
            "PERSONAL_PHONE" => $phone,
            "ACTIVE" => "Y",
            "PASSWORD" => $pass,
            "CONFIRM_PASSWORD" => $confirm_pass,
            "UF_FRIEND_EMAIL" => $friend_email,
            //"UF_SUBSCRIBE" => $_POST['subscribe'],
            "UF_SUBSCRIBE" => 4,
            "UF_LOYALTY_CARD" => $cardNumber,
        );
        $ID = $USER->Add($arFields);
        if (intval($ID) > 0) {            
            //$res = $USER->Update($ID, array("PERSONAL_BIRTHDAY" => $objDateTime));                        
            $arReturn['result'][] = "Вы успешно зарегистрированы.";
            //$arReturn['result'][] = iconv('windows-1251', 'UTF-8', "Пароль: " . $pass);

            // Пытаемся авторизовать пользователя
            $arAuthResult = $USER->Login($email, $pass, "Y");
            if ($arAuthResult !== true) {
                $arReturn['errors'][] = $arAuthResult['MESSAGE'];
            }
            else {
                $arEventFields = array(
                    "USER_ID" => $ID,
                    "NAME" => $name,
                    "LAST_NAME" => $lastname,
                    "PHONE" => $phone,
                    "LOGIN" => $email,
                    "EMAIL" => $email,
                    "MESSAGE" => '',
                    //"PASSWORD" => $pass,
                );
                CEvent::SendImmediate("USER_INFO", 's1', $arEventFields);
            }

            
            // Если указывался email друга, то ищем пользователя с таким email и выдаем ему купон на 100500 рублей
            if ($friend_email != ''){
                $filter = Array("EMAIL" => $friend_email);
                $rsUsers = CUser::GetList(($by="ID"), ($order="asc"), $filter); // выбираем пользователей
                if($arUser = $rsUsers->Fetch()){

                    if (CModule::IncludeModule("catalog"))
                    {
                        $COUPON = CatalogGenerateCoupon();

                        $arCouponFields = array(
                            "DISCOUNT_ID" => "25",
                            "ACTIVE" => "Y",
                            "TYPE" => 2,//1 - на одну позицию заказа, 2 - на один заказ, 4 - многоразовый
                            "COUPON" => $COUPON,
                            'DESCRIPTION' => 'USER_ID='.$arUser['ID']
                        );

                        $result = Internals\DiscountCouponTable::add($arCouponFields);
                        if (!$result->isSuccess())
                        {
                            $errorMessage = $result->getErrorMessages();
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
}
$strReturn = json_encode($arReturn);
echo $strReturn;


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>