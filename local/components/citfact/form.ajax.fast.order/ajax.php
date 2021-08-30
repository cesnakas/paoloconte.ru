<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Sale\Internals;
Loader::includeModule('iblock');
Loader::includeModule('sale');

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
		if ($arPropsIblock[$key]['PROPERTY_TYPE'] == 'F'){
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

    if ($cityId = $_SESSION["CITY_ID"]) {
        $res = CIBlockElement::GetByID($cityId);
        if ($arFields = $res->GetNext()) {
            $arPropVals['CITY'] = $arFields['NAME'];
        }
    }

    $saleBasket = new CSaleBasket();
	$IBLOCK_ID = (int)$arParams['IBLOCK_ID'];
	$el = new \CIBlockElement;

    if (!empty($arParams['PROPERTY_ELEMENTS'])) {
        // массив товаров корзины
        $arBasketItems = array();
        $resBasketItems = $saleBasket->GetList(
            array(),
            array(
                "FUSER_ID" => $saleBasket->GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL",
                "DELAY" => "N",
                "CAN_BUY" => "Y"
            ),
            false,
            false,
            array("ID", "PRODUCT_ID")
        );
        while ($arResBasket = $resBasketItems->Fetch()) {
            $arBasketItems[] = $arResBasket['PRODUCT_ID'];
        }

        $arPropVals[$arParams['PROPERTY_ELEMENTS']] = $arBasketItems;
    }


	// Если был найден совпадающий элемент, то добавляем новый

    $arLoadProductArray = Array(
        "MODIFIED_BY" => $USER->GetID(),
        "IBLOCK_SECTION_ID" => false,
        "IBLOCK_ID" => $IBLOCK_ID,
        "NAME" => 'Заказ в 1 клик',
        "ACTIVE" => ($arParams['ELEMENT_ACTIVE'] == 'Y' ? 'Y' : 'N'),
        'PROPERTY_VALUES' => $arPropVals,
    );


    // Добавляем в инфоблок
    if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
        //echo $PRODUCT_ID;
        $arReturn['result'][] = $arParams['SUCCESS_MESSAGE'];

        // Если есть файлы, прикрепляем их в письмо
        $arFilesIds = array();
        if ($hasFiles === true && $arParams['ATTACH_FILES'] == 'Y'){
            $arOrder = array();
            $arFilter = array('IBLOCK_ID' => $IBLOCK_ID, 'ID' => $PRODUCT_ID);
            $arSelectFields = array("ID", "ACTIVE", "NAME", 'PROPERTY_'.$arParams['FILE_PROPERTY_CODE']);
            $rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
            while($arElement = $rsElements->GetNext())
            {
                $arFilesIds[] = $arElement['PROPERTY_'.$arParams['FILE_PROPERTY_CODE'].'_VALUE'];
            }
        }

        // чистим корзину
        if (!empty($arParams['PROPERTY_ELEMENTS'])) {
            $saleBasket->DeleteAll($saleBasket->GetBasketUserID());
        }

        // redirect
        if (!empty($arParams['REDIRECT_PAGE'])) {
            $arReturn['REDIRECT'] = $arParams['REDIRECT_PAGE'];
        }


        // Отсылаем письмо
        $arFields = $arPropVals;

        $arFields['ELEMENT_LINK'] = '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID='.$IBLOCK_ID.'&type='.$arParams['IBLOCK_TYPE'].'&ID='.$PRODUCT_ID.'&lang=ru&find_section_section=-1&WF=Y';

        if (!empty($arParams["EVENT_MESSAGE_ID"])) {
            foreach ($arParams["EVENT_MESSAGE_ID"] as $v)
                if (IntVal($v) > 0) {
                    CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields, "N", IntVal($v));
                }
        } else {
            CEvent::Send($arParams["EVENT_NAME"], SITE_ID, $arFields, 'Y', '', $arFilesIds);
            $arReturn['EVENT_NAME'] = array($arParams["EVENT_NAME"], SITE_ID, $arFields, 'Y', '', $arFilesIds);
        }
    } else {
        $arReturn['errors'][] = "Ошибка: " . $el->LAST_ERROR;
    }

}

$strReturn = json_encode($arReturn);
echo $strReturn;


require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
