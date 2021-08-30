<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");


use Bitrix\Main\Loader;
use Bitrix\Sale\Internals;

Loader::includeModule('iblock');
Loader::includeModule('sale');
Loader::includeModule('catalog');

global $USER;
$arReturn = array('errors'=>array(), 'result'=>array());

$arFormdata = array();
parse_str($_POST['formdata'], $arFormdata);

$translit = array(

	'а' => 'a',   'б' => 'b',   'в' => 'v',

	'г' => 'g',   'д' => 'd',   'е' => 'e',

	'ё' => 'yo',   'ж' => 'zh',  'з' => 'z',

	'и' => 'i',   'й' => 'j',   'к' => 'k',

	'л' => 'l',   'м' => 'm',   'н' => 'n',

	'о' => 'o',   'п' => 'p',   'р' => 'r',

	'с' => 's',   'т' => 't',   'у' => 'u',

	'ф' => 'f',   'х' => 'x',   'ц' => 'c',

	'ч' => 'ch',  'ш' => 'sh',  'щ' => 'shh',

	'ь' => '\'',  'ы' => 'y',   'ъ' => '\'\'',

	'э' => 'e\'',   'ю' => 'yu',  'я' => 'ya',


	'А' => 'A',   'Б' => 'B',   'В' => 'V',

	'Г' => 'G',   'Д' => 'D',   'Е' => 'E',

	'Ё' => 'YO',   'Ж' => 'Zh',  'З' => 'Z',

	'И' => 'I',   'Й' => 'J',   'К' => 'K',

	'Л' => 'L',   'М' => 'M',   'Н' => 'N',

	'О' => 'O',   'П' => 'P',   'Р' => 'R',

	'С' => 'S',   'Т' => 'T',   'У' => 'U',

	'Ф' => 'F',   'Х' => 'X',   'Ц' => 'C',

	'Ч' => 'CH',  'Ш' => 'SH',  'Щ' => 'SHH',

	'Ь' => '\'',  'Ы' => 'Y\'',   'Ъ' => '\'\'',

	'Э' => 'E\'',   'Ю' => 'YU',  'Я' => 'YA',

);

$arFormdata['CITY_ID'] = strtr(ucfirst($arFormdata['CITY_ID']), array_flip($translit));
// $id_user = $arFormdata['USER_ID'];
// $rsUser = CUser::GetByID($id_user);
// $arUser = $rsUser->Fetch();
// $arFormdata['USER_FULL_NAME'] = $arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME'];
if ($arFormdata['yarobot'] == '' && check_bitrix_sessid()) {

	$arParams = $_POST['params'];


	// Массив свойств из инфоблока
	$arPropsIblock = array();
	$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$arParams['IBLOCK_ID']));
	while ($prop_fields = $properties->GetNext()){
		$arPropsIblock[$prop_fields['CODE']] = $prop_fields;
	}
	
	$hasFiles = false;
	$arPropVals = array();
	foreach ($arParams['SHOW_PROPERTIES'] as $key => $arProp) {
		// Для свойства типа «Файл» формируем массив
		if ($arPropsIblock[$key]['PROPERTY_TYPE'] == 'F') {
			$path = $_SERVER["DOCUMENT_ROOT"].$arParams['AJAX_FILES_PATH'].basename(trim($arFormdata[$key]));
			if ($path != ''){
				$arPropVals[$key] = \CFile::MakeFileArray($path);
				$hasFiles = true;
			}
		}
		else {
                    $arPropVals[$key] = htmlspecialcharsbx(trim($arFormdata[$key]));
		}
	}

    if ($cityId = $_SESSION["CITY_ID"]){

        $res = CIBlockElement::GetByID($cityId);
        if ($arFields = $res->GetNext())
        {
            $arPropVals['CITY'] =  $arFields['NAME'];
        }
    }

	$IBLOCK_ID = (int)$arParams['IBLOCK_ID'];
	$el = new \CIBlockElement;

	// Ищем совпадения по свойствам
	$equal_element_id = 0;
	if (!empty($arParams['CHECK_EQUAL_PROPS'])){
		$arOrder = array();
		$arFilter = array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y');
		foreach ($arParams['CHECK_EQUAL_PROPS'] as $propcode) {
			$arFilter['PROPERTY_'.$propcode] = $arPropVals[$propcode];
		}
		$arSelectFields = array("ID");
		$rsElements = $el->GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
		if($arElement = $rsElements->GetNext())
		{
			$equal_element_id = $arElement['ID'];
		}
	}

	// Если был найден совпадающий элемент, то обновляем его
	// Иначе, добавляем новый
	if ($equal_element_id != 0) {
		$el->SetPropertyValuesEx($equal_element_id, false, $arPropVals);
		$arReturn['result'][] = $arParams['SUCCESS_MESSAGE'];
	} else {
		$arLoadProductArray = Array(
			"MODIFIED_BY" => $USER->GetID(),
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID" => $IBLOCK_ID,
			"NAME" => ($arFormdata["TOVAR_NAME"] ? $arFormdata["TOVAR_NAME"] : 'Запрос') . ($_GET['SIZE'] ?  ' (' . htmlspecialchars($_GET['SIZE']) . ')' : ''),
			"ACTIVE" => ($arParams['ELEMENT_ACTIVE'] == 'Y' ? 'Y' : 'N'),
			'PROPERTY_VALUES' => $arPropVals,
		);

		// Генерация купона для формы на главной
                $arDiscount = CSaleDiscount::GetByID(DISCOUNT_ID_SUBSCRIBE);
		$COUPON_NUMBER = '';
                $discontActive = (!empty($arDiscount["ID"]) && $arDiscount["ACTIVE"] === "Y");
		if ($arParams['GENERATE_COUPON'] == 'Y' && $discontActive) {
			global $USER;
			if ($USER->IsAuthorized()) {
				$USER_ID = $USER->GetID();
			}
			else{
				$USER_ID = 0;
			}

			$COUPON_NUMBER = CatalogGenerateCoupon();
			$arCouponFields = array(
				"DISCOUNT_ID" => $arDiscount["ID"],
				"ACTIVE" => "Y",
				"TYPE" => 2,//1 - на одну позицию заказа, 2 - на один заказ, 4 - многоразовый
				"COUPON" => $COUPON_NUMBER,
				'DESCRIPTION' => 'USER_ID='.$USER_ID
			);

			$result = Internals\DiscountCouponTable::add($arCouponFields);
			/*$CID = CCatalogDiscountCoupon::Add($arCouponFields);
			$CID = IntVal($CID);*/
		}

		// Добавляем в инфоблок
        $eventAvailable = (($arParams["EVENT_NAME"] !== "INDEX_PROMO_FORM") || $discontActive);
        if (($PRODUCT_ID = $el->Add($arLoadProductArray))) {
            // Если есть файлы, прикрепляем их в письмо
            $arFilesIds = [];
            if ($hasFiles === true && $arParams['ATTACH_FILES'] == 'Y') {
                $arFilter = ['IBLOCK_ID' => $IBLOCK_ID, 'ID' => $PRODUCT_ID];
                $arSelectFields = ["ID", "ACTIVE", "NAME", 'PROPERTY_' . $arParams['FILE_PROPERTY_CODE']];
                $rsElements = CIBlockElement::GetList([], $arFilter, FALSE, FALSE, $arSelectFields);
                while ($arElement = $rsElements->GetNext()) {
                    $arFilesIds[] = $arElement['PROPERTY_' . $arParams['FILE_PROPERTY_CODE'] . '_VALUE'];
                }
            }

            // Отсылаем письмо
            $arFields = $arPropVals;
            $arFields['COUPON_NUMBER'] = $COUPON_NUMBER;
            $arFields['SIZE'] = htmlspecialchars($_GET['SIZE']);
            $arFields['COLOR'] = htmlspecialchars($_GET['COLOR']);
            $arFields['URL'] = htmlspecialchars($_GET['URL']);
            $arFields['NAME_TOVAR'] = htmlspecialchars($_GET['NAME_TOVAR']);
            if ($eventAvailable) {
                if (!empty($arParams["EVENT_MESSAGE_ID"])) {
                    foreach ($arParams["EVENT_MESSAGE_ID"] as $messId) {
                        settype($messId, "int");
                        if (!empty($messId)) {
                            CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields, "N", $messId);
                        }
                    }
                } else {
                    CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields, 'Y', '', $arFilesIds);
                    $arReturn['EVENT_NAME'] = array($arParams["EVENT_NAME"], SITE_ID, $arFields, 'Y', '', $arFilesIds);
                }
            }
        } else {
            $arReturn['errors'][] = "Ошибка: " . $el->LAST_ERROR;
        }
        if (empty($arReturn['errors'])) {
            $arReturn['result'][] = $arParams['SUCCESS_MESSAGE'];
        }
    }
}

echo json_encode($arReturn);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
