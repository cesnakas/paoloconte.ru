<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

require_once 'include/Ulogin.class.php';

$arResult = $arParams;

global $USER;
global $APPLICATION;

if (!empty($_POST['token']) && !$USER->isAuthorized()) {
    $s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
    $profile = json_decode($s, true);

    if (count($profile) && !isset($profile['error'])){

        list($d, $m, $y) = explode('.', $profile['bdate']);



        //$arResult['USER']['LOGIN'] = Ulogin::genNickname($profile);
        $arResult['USER']['LOGIN'] = $profile['email'];
        $arResult['USER']['NAME'] = $APPLICATION->ConvertCharset($profile['first_name'], "UTF-8", SITE_CHARSET);
        $arResult['USER']['LAST_NAME'] = $APPLICATION->ConvertCharset($profile['last_name'], "UTF-8", SITE_CHARSET);
        $arResult['USER']['EMAIL'] = $profile['email'];
        $arResult['USER']['PERSONAL_GENDER'] = ($profile['sex'] == 2 ? 'M' : 'F');
        $arResult['USER']['PERSONAL_CITY'] = $profile['city'];
        $arResult['USER']['PERSONAL_BIRTHDAY'] = (!empty($profile['bdate']))?$d . '.' . $m . '.' . $y:'';
        $arResult['USER']['EXTERNAL_AUTH_ID'] = $profile['identity'];
        $arResult['USER']['PHOTO'] = $profile['photo'];
        $arResult['USER']['PHOTO_BIG'] = $profile['photo_big'];
        $arResult['USER']['NETWORK'] = $profile['network'];

        // проверяем есть ли пользователь в БД.	Если есть - то авторизуем, нет  - регистрируем и авторизуем
        $rsUsers = CUser::GetList(
            ($by = "email"),
            ($order = "desc"),
            array(
                "EXTERNAL_AUTH_ID" => $arResult['USER']["EXTERNAL_AUTH_ID"]
            )
        );
        $arUser = $rsUsers->GetNext();

        $emailExist = false;
        // проверка уникальности email
        if ($arParams['UNIQUE_EMAIL'] == 'Y'){
          $emailUsers = CUser::GetList(
                                ($by = "id"),
                                ($order = "desc"),
                                array(
                                  "EMAIL" => $arResult['USER']["EMAIL"],
                                  "ACTIVE" => "Y"
                                )
                        );

          if (intval($emailUsers->SelectedRowsCount()) > 0){
            $emailExist = true;
			  if ($arUser = $emailUsers->Fetch())
			  {
				  $arResult['FOUNDED_USER_ID'] = $arUser['ID'];
			  }
          }
        }
		
        if ($arUser["EXTERNAL_AUTH_ID"] == $arResult['USER']["EXTERNAL_AUTH_ID"]) {

            // такой пользователь есть, авторизуем пользователя

            $ID_INFO = explode('=',$arUser['ADMIN_NOTES']);

            if ($arResult['USER']['NETWORK'] == $ID_INFO[0] && $arUser['ACTIVE'] == 'Y'){//старый формат хранения аккаунтов, конвертируем

                $USER->Update($arUser['ID'], array('EXTERNAL_AUTH_ID'=>''));
                Ulogin::createUloginAccount($arResult['USER'], $arUser['ID']);
                $ID_INFO[1] = $arUser['ID'];
            }

            //Если имя и фамилия изменились, то обновляем

            $rsUsers = CUser::GetList(
                ($by = "email"),
                ($order = "desc"),
                array(
                    "ID" => $ID_INFO[1]
                )
            );

            $arUser = $rsUsers->GetNext();

            $toUpdate = array();

            if ($arUser["NAME"] != $arResult["USER"]["NAME"]) {

                $toUpdate["NAME"] = $arResult["USER"]["NAME"];

            }

            if ($arUser["LAST_NAME"] != $arResult['USER']["LAST_NAME"]) {

                $toUpdate["LAST_NAME"] = $arResult["USER"]["LAST_NAME"];

            }

            if (count($toUpdate) > 0){

                $USER->Update($arUser['ID'], $toUpdate);

            }

            $USER->Authorize($ID_INFO[1]);

            if ($arParams["REDIRECT_PAGE"] != "")
                LocalRedirect($arParams["REDIRECT_PAGE"]);
            else
                LocalRedirect($APPLICATION->GetCurPageParam("", array("logout")));

        }else if (!$emailExist){
            // регистрируем пользователя, и добавляем его в группы, указанные в параметрах
            $user = new CUser;
            $GroupID = USERGROUP_TO_SOCIAL;
            $passw = rand(1000000,10000000);

            if (is_array($arParams["GROUP_ID"]))
                $GroupID = $arParams["GROUP_ID"];
        if (!is_array($GroupID)) $GroupID=array($GroupID);

            if (!$arResult['USER']["EMAIL"])
                $arResult['USER']["EMAIL"] = "yourmail@domain.com";

            # проверяем есть ли такой логин
            $rsUsers = CUser::GetList(
                ($by = "email"),
                ($order = "desc"),
                array(
                    "LOGIN" => $arResult['USER']["LOGIN"],
                    "ACTIVE" => "Y"
                )
            );

            while ($arUser = $rsUsers->GetNext())
                $count_user_id[] = $arUser["ID"];

            if (count($count_user_id) > 0) {
                $arResult['USER']["LOGIN"] = $arResult['USER']["LOGIN"] . "_" . count($count_user_id);
            }

            $imageContent = file_get_contents($profile['photo']);
                $ext = strtolower(substr($profile['photo'],-3));
                if (!in_array($ext,array('jpg','jpeg','png','gif','bmp'))) $ext = 'jpg';

                $tmpName = $tmpName = rand(100000,10000000).'.'.$ext;
                $tmpName = $_SERVER["DOCUMENT_ROOT"]."/images/".$tmpName;

			if (file_put_contents($tmpName,$imageContent)){
				$arIMAGE = CFile::MakeFileArray($tmpName);
				$arIMAGE["MODULE_ID"] = "main";
			}
			else 
				$arIMAGE = '';
			
			
            $arFields = Array(
                "NAME" => $arResult['USER']['NAME'],
                "LAST_NAME" => $arResult['USER']['LAST_NAME'],
                "EMAIL" => $arResult['USER']["EMAIL"],
                "LOGIN" => $arResult['USER']["LOGIN"],
                "PERSONAL_GENDER" => $arResult['USER']["PERSONAL_GENDER"],
                "ADMIN_NOTES" => $arResult['USER']["NETWORK"],
                "PERSONAL_BIRTHDAY" => $arResult['USER']['PERSONAL_BIRTHDAY'],
                "ACTIVE" => "Y",
                "GROUP_ID" => $GroupID,
                "PASSWORD" => $passw,
                "CONFIRM_PASSWORD" => $passw,
                "PERSONAL_PHOTO"    => $arIMAGE,
            );

            $UserID = $user->Add($arFields);
			
        if ($UserID && $arParams['SEND_EMAIL'] == 'Y'){
          $arEventFields = array(
            'USER_ID' => $UserID,
            'LOGIN' => $arFields['LOGIN'],
            'EMAIL' => $arFields['EMAIL'],
            'NAME' => $arFields['NAME'],
            'LAST_NAME' => $arFields['LAST_NAME'],
            'USER_IP'  => '',
            'USER_HOST'  => ''
          );
          $event = new CEvent;
          $msg = $event->SendImmediate("NEW_USER", SITE_ID, $arEventFields);
          ShowMessage($msg);

        }
		else {
			/*define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log_ulogin.txt");
			AddMessage2Log(print_r($user->LAST_ERROR, TRUE));*/
		}

            unlink($tmpName);

            if (intval($UserID) > 0) {
                $arFields['EXTERNAL_AUTH_ID'] = $arResult['USER']["EXTERNAL_AUTH_ID"];
               // Ulogin::createUloginAccount($arResult['USER'], $UserID); // после регистрации еще одного опльзователя создаем? а зачем?
                $USER->Authorize($UserID);

                if ($arParams["REDIRECT_PAGE"] != "")
                    LocalRedirect($arParams["REDIRECT_PAGE"]);
                else
                    LocalRedirect($APPLICATION->GetCurPageParam("", array("logout")));

            }

        }
		else{
			// Если есть пользователь с таким email
			//$arResult['ERRORS'][] = 'Пользователь с таким email уже зарегистрирован.';
			if ((int)$arResult['FOUNDED_USER_ID'] > 0) {
                $arGroupsFoundedUser = CUser::GetUserGroup($arResult['FOUNDED_USER_ID']);
                if (in_array('1', $arGroupsFoundedUser) == false) { // админов не авторизуем
                    $USER->Authorize($arResult['FOUNDED_USER_ID']);
                    if ($arParams["REDIRECT_PAGE"] != "")
                        LocalRedirect($arParams["REDIRECT_PAGE"]);
                    else
                        LocalRedirect($APPLICATION->GetCurPageParam("", array("logout")));
                }
			}
		}
    }else{
        if (isset($profile['error']))
            ShowMessage(array("TYPE" => "ERROR", "MESSAGE" => $profile['error']));
    }
}


if (!isset($GLOBALS['ULOGIN_OK'])) {
    $GLOBALS['ULOGIN_OK'] = 1;
}
else
{
    $GLOBALS['ULOGIN_OK']++;
}

$strErrors = '';
foreach ($arResult['ERRORS'] as $error){
	$strErrors .= '<p style="font-size: 14px;">'.$error.'</p>';
}

$url = 'https://' .  $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_URL'];

$code = '<div id="uLogin' . $GLOBALS['ULOGIN_OK'] . '" x-ulogin-params="display=' . $arParams['TYPE'] . '&fields=first_name,last_name,city,photo,photo_big,email,network' .
    '&providers=' . $arParams['PROVIDERS'] . '&hidden=' . $arParams['HIDDEN'] . '&redirect_uri=' . urlencode( $url ) . '">
	<a href="javascript:void(0)" class="vk" x-ulogin-button = "vkontakte"><i class="fa fa-vk"></i></a>
	<a href="javascript:void(0)" class="facebook" x-ulogin-button = "facebook"><i class="fa fa-facebook"></i></a>
	<a href="javascript:void(0)" class="twitter" x-ulogin-button = "twitter"><i class="fa fa-twitter"></i></a>
	<a href="javascript:void(0)" class="instagram" x-ulogin-button = "instagram"><i class="fa fa-instagram"></i></a>
	<a href="javascript:void(0)" style="background: black;" class="googleplus" x-ulogin-button = "googleplus"><i class="fa fa-google-plus"></i></a>
    </div>'.$strErrors;

if ($GLOBALS['ULOGIN_OK'] == 1) {
    $code = '<script src="https://ulogin.ru/js/ulogin.js"></script>' . $code;
}


$arResult['ULOGIN_CODE'] = $code;


$this->IncludeComponentTemplate();
?>