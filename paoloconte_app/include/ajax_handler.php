<?
/*
 * принимаем все ajax посылки в этом файле
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && !empty($_POST)){

    if(CModule::IncludeModule("iblock")) {
        /******************************/

        $RESULT["status"] = false; // статус запроса, по дефолту false и при успешном действии ставим true а при ошибке так и оставляем false
        //$RESULT["success"] тут писать сообщения о успешном действии
        //$RESULT["error"] тут писать сообщения о ошибке
        setlocale( LC_NUMERIC, '' );
        $element = new CIBlockElement; //элемент инфоблока
        $PROPS = array(); //сюда собирать свойства элемента
        $userId=intVal($USER->GetID()); // текущий пользователь
        $SITE_ID = 's1';

        /*
         * если необходимо написать еще обработчик
         * надо просто создать еще один case и делать в нем все что нужно
         */
        switch($_POST["TYPE"]){
            //добавление в избранное
            case "ADD_FAVORITE":
                if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")){
                    if(!empty($_POST["product_id"])) {
                        $result = Add2BasketByProductID(
							$_POST["product_id"],
							1,
							array(
								//array("NAME" => "Размер", "CODE" => "RAZMER", "VALUE" => "37")
							)
						);
                        if ($result != false) {
                            if (CSaleBasket::Update($result, array("DELAY" => "Y")))
                                $RESULT["status"] = true;
                        }
                        if ($RESULT["status"])
                            $RESULT["success"] = "Товар успешно добавлен в избранное";
                        else
                            $RESULT["error"] = "При добавлении в избранное произошла ошибка! Или id товара не верный или у товара нет цены.";
                    }
                    else
                        $RESULT["error"] = "Ошибка! Не указан id товара.";
                }
                break;
            // Удаление из избранного
            case "DEL_FAVORITE":
                if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")){
                    if(!empty($_POST["product_id"])) {
                        if (CSaleBasket::Delete($_POST["product_id"]))
                            $RESULT["status"] = true;
                        if ($RESULT["status"])
                            $RESULT["success"] = "Товар успешно удален из избранного";
                        else
                            $RESULT["error"] = "При удалении из избранного произошла ошибка! Вероятно не верный id";
                    }
                    else
                        $RESULT["error"] = "Ошибка! Не указан id";
                }
                break;
            //проверка на добавление в избранное
            case "CHECK_FAVORITE":
                if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")){
                    if(!empty($_POST["products_id"]) && is_array($_POST["products_id"])) {
                        $prods = $_POST["products_id"];
                        foreach ($prods as $key => $arProduct) {
                            $prods[$key]['FAVORITE'] = 'N';
                        }
                        $arBasketItems = array();
                        $arFilter = array("FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL", "DELAY" => "Y");
                        $arSelect = array("ID", "PRODUCT_ID", "NAME", "DELAY");
                        $dbBasketItems = CSaleBasket::GetList(array("NAME" => "ASC", "PRODUCT_ID" => "ASC"), $arFilter, false, false, $arSelect);
                        while ($arItems = $dbBasketItems->Fetch()) {
                            foreach ($prods as $key => $arProduct) {
                                if($arItems['PRODUCT_ID'] == $arProduct['ID']) {
                                    $prods[$key]['FAVORITE'] = 'Y';
                                    $prods[$key]['BASKET_ID'] = $arItems['ID'];
                                }
                            }
                        }
                        $RESULT["products"] = $prods;
                        $RESULT["status"] = true;
                    }
                }
                break;

			// Удаление подписки на цену
			case "DEL_SUBSCRIBE_PRICE":
				if (CModule::IncludeModule("iblock")){
					if(!empty($_POST["element_id"])) {
						$el = new \CIBlockElement;
						$arLoadProductArray = Array(
							"ACTIVE"         => "N",
						);
						if ($el->Update((int)$_POST["element_id"], $arLoadProductArray))
							$RESULT["status"] = true;
						if ($RESULT["status"])
							$RESULT["success"] = "Подписка успешно удалена";
						else
							$RESULT["error"] = "При удалении подписки что-то пошло не так =/";
					}
					else
						$RESULT["error"] = "Ошибка! Не указан id";
				}
				break;

			// Изменение адреса доставки
			case "EDIT_ADDRESS":
				if (CModule::IncludeModule("iblock")){
					global $USER;
					if(!empty($_POST["element_id"])) {
						$el = new \CIBlockElement;
						$arProps = array(
							'USER' => $USER->GetID(),
							'LOCATION' => htmlspecialcharsbx($_POST["location_id"]),
							'ADDRESS' => htmlspecialcharsbx($_POST["address"]),
							'SELECTED' => htmlspecialcharsbx($_POST["selected"]),
						);
						$arLoadProductArray = Array(
							"MODIFIED_BY"    => $USER->GetID(),
							'PROPERTY_VALUES' => $arProps
						);
						if ($el->Update((int)$_POST["element_id"], $arLoadProductArray))
							$RESULT["status"] = true;
						if ($RESULT["status"])
							$RESULT["success"] = "Адрес сохранен";
						else
							$RESULT["error"] = "При сохранении что-то пошло не так :(";
					}
					else
						$RESULT["error"] = "Ошибка! Не указан id";
				}
				break;

			// Выбор адреса доставки
			case "SELECT_ADDRESS":
				if (CModule::IncludeModule("iblock")){
					if(!empty($_POST["element_id"])) {
						global $USER;
						$el = new \CIBlockElement;
						$user_id = $USER->GetID();
						$arFilter = array('IBLOCK_ID' => 33, 'ACTIVE' => 'Y', 'PROPERTY_USER' => $user_id);
						$res = $el->GetList(array('ID' => 'DESC'), $arFilter, false, false,
							array('IBLOCK_ID', 'ID')
						);
						$arIds = array();
						while ($arRes = $res->GetNext()) {
							$el->SetPropertyValuesEx($arRes['ID'], 33, array('SELECTED' => 0));
						}

						$el->SetPropertyValuesEx((int)$_POST["element_id"], 33, array('SELECTED' => 1));

						//if ($el->Update((int)$_POST["element_id"], $arLoadProductArray))
							$RESULT["status"] = true;
						if ($RESULT["status"])
							$RESULT["success"] = "Адрес сохранен";
						else
							$RESULT["error"] = "При сохранении что-то пошло не так :(";
					}
					else
						$RESULT["error"] = "Ошибка! Не указан id";
				}
				break;

			// Добавление адреса доставки
			case "ADD_ADDRESS":
				if (CModule::IncludeModule("iblock")){
					global $USER;
					$el = new \CIBlockElement;
					$arProps = array(
						'USER' => $USER->GetID(),
						'LOCATION' => htmlspecialcharsbx($_POST["location_id"]),
						'ADDRESS' => htmlspecialcharsbx($_POST["address"]),
						'SELECTED' => 1,
					);
					$arLoadProductArray = Array(
						"MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
						"IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
						"IBLOCK_ID"      => 33,
						"NAME" => 'Адрес',
						'PROPERTY_VALUES' => $arProps
					);
					if ($PRODUCT_ID = $el->Add($arLoadProductArray))
						$RESULT["status"] = true;
					if ($RESULT["status"])
						$RESULT["success"] = "Адрес сохранен";
					else
						$RESULT["error"] = "При добавлении что-то пошло не так :(";
				}
				break;

			// Удаление адреса доставки
			case "DELETE_ADDRESS":
				if (CModule::IncludeModule("iblock")){
					global $USER;
					if(!empty($_POST["element_id"])) {
						$el = new \CIBlockElement;

						if ($el->Delete((int)$_POST["element_id"]))
							$RESULT["status"] = true;
						if ($RESULT["status"])
							$RESULT["success"] = "Адрес удален";
						else
							$RESULT["error"] = "При удалении что-то пошло не так :(";
					}
					else
						$RESULT["error"] = "Ошибка! Не указан id";
				}
				break;

			// Смена аватара пользователя
			case "CHANGE_AVA":
				$path = $_SERVER["DOCUMENT_ROOT"].$_POST['files_path'].basename(trim($_POST['filename']));
				$arFile = \CFile::MakeFileArray($path);

				$user = new \CUser;
				$user_id = $user->GetID();

				$rsUser = $user->GetByID($user_id);
				$arUser = $rsUser->Fetch();

				$arFile['del'] = "Y";
				$arFile['old_file'] = $arUser['PERSONAL_PHOTO'];

				$fields = Array(
					"PERSONAL_PHOTO" => $arFile,
				);

				if ($user->Update($user_id, $fields)){
					$RESULT["status"] = true;

					$rsUser = $user->GetByID($user_id);
					$arUser = $rsUser->Fetch();
					$file = CFile::ResizeImageGet($arUser['PERSONAL_PHOTO'], array('width'=>103, 'height'=>103), BX_RESIZE_IMAGE_EXACT, true);
					$RESULT["file_src"] = $file['src'];
				}
				else {
					$RESULT["error"] .= $user->LAST_ERROR;
				}

				if ($RESULT["status"])
					$RESULT["success"] = "Аватар успешно загружен";

				/*if (CModule::IncludeModule("iblock")){
					if(!empty($_POST["element_id"])) {
						$el = new \CIBlockElement;
						$arLoadProductArray = Array(
							"ACTIVE"         => "N",
						);
						if ($el->Update((int)$_POST["element_id"], $arLoadProductArray))
							$RESULT["status"] = true;
						if ($RESULT["status"])
							$RESULT["success"] = "Подписка успешно удалена";
						else
							$RESULT["error"] = "При удалении подписки что-то пошло не так =/";
					}
					else
						$RESULT["error"] = "Ошибка! Не указан id";
				}*/
				break;

			// Проверка на существование email из формы подписки
			case "CHECK_EMAIL_SUBSCRIBE":
				if (CModule::IncludeModule("iblock")){
					if(!empty($_POST["email"])) {
						$el = new \CIBlockElement;
						$arFilter = array('IBLOCK_ID' => IBLOCK_SUBSCRIBE_EMAIL, 'ACTIVE' => 'Y', 'PROPERTY_EMAIL' => $_POST["email"]);
						$res = $el->GetList( array('ID' => 'DESC'), $arFilter, false, false, array('IBLOCK_ID', 'ID') );
						if ($arRes = $res->GetNext()) {
							$RESULT["status"] = 'found';
						}
						else{
							$RESULT["status"] = 'not_found';
						}
					}
					else
						$RESULT["error"] = "Ошибка! Не указан email";
				}
				break;

            default: die("Error type, sorry man!"); //тип формы не был указан, шлем нах...
        }

        $RESULT["sendPostData"] = $_POST; // заодно прикрепляем к массиву то что было прислано GET

        print json_encode($RESULT); // возвращаем JSON результат

        /******************************/
    }
}
else die("Error send method, sorry man!"); //не аякс запрос или пустой GET, шлем нах...
?>