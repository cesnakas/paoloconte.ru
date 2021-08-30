<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();




$idIblockCity =  \Citfact\Tools::getIdIblock(CODE_IBLOCK_CITIES);
$idIblockShops =  \Citfact\Tools::getIdIblock(CODE_IBLOCK_SHOPS);


$arResult['ADDRESSES'] = array();
$locationName = "";

//Поиск названия города при выборе другого адреса в местоположении
if (!empty($_POST["ORDER_PROP_6"]) && ($_POST["ADDRESS_MY"] == "new" || !isset($_POST["ADDRESS_MY"]))){
    $db_vars = \CSaleLocation::GetList(
        array(
            "SORT" => "ASC",
            "COUNTRY_NAME_LANG" => "ASC",
            "CITY_NAME_LANG" => "ASC"
        ),
        array("ID" => (int)$_POST["ORDER_PROP_6"], "LID" => LANGUAGE_ID, /*'COUNTRY_LID' => 'ru', 'REGION_LID' => 'ru', 'CITY_LID' => 'ru'*/),
        false,
        false,
        array()
    );
    while ($vars = $db_vars->Fetch()) {
        if (!empty($vars["CITY_NAME"]))
        {
            $locationName =  $vars["CITY_NAME"];
        }
      
    }
    
}



if ($USER->IsAuthorized()) {
    $arResult['ADDRESSES'] = \Citfact\Paolo::GetUserAddresses($USER->GetID());
    $zipSelected = '';

    foreach ($arResult['ADDRESSES'] as $key => $arAddress) {
        
        if ($arAddress['SELECTED'] == 1) {
            $zipSelected = $arAddress['ZIP'];
            
        }
        //ищем выбранный пользователем адрес
        
        if ($_POST["ADDRESS_MY"] != "new" && ($_POST['ADDRESS_MY'] == $key || ($_POST['ADDRESS_MY'] == '' && $arAddress['SELECTED'] == 1))) {
            $locationName = $arAddress["CITY_NAME"];
        }
    }

    foreach ($arResult["ORDER_PROP"]["USER_PROPS_N"] as &$arProp) {
        if ($arProp['CODE'] == 'ZIP' && $arProp['VALUE'] == '') {
            $arProp['VALUE'] = $zipSelected;
        }
    }
}

$cityId = 0;

//поиск ID города 
if (!empty($locationName)){
    $locationName = strtoupper($locationName);
    $cities = CIBlockElement::GetList(false, array("IBLOCK_ID"=>$idIblockCity,"NAME"=>"%".$locationName."%","ACTIVE"=>"Y"),false,false,array("ID","NAME"));
    while($city = $cities->Fetch()){
            $cityId = $city["ID"];
    }
    
}



$storeList = array(); //список cкладов (в который будем собирать магазины)

$storeIds = array();
/* Выборка всех магазинов (для доставки Самовывоз из магазинов)  */
if (!empty($cityId)){
    $shops = CIBlockElement::GetList(false, array("PROPERTY_CITY"=>$cityId,"IBLOCK_ID"=>$idIblockShops,"ACTIVE"=>"Y"),false,false,array("ID","IBLOCK_ID","PROPERTY_STORE_ID","NAME"));
    while($shop = $shops->GetNextElement()){
        $shopElement = $shop->GetFields();
        $shopElement["PROP"] = $shop->GetProperties();
        $shopList[$shopElement["ID"]] = $shopElement;
        if (!empty($shopElement["PROP"]) && !empty($shopElement["PROP"]["STORE_ID"]) && !empty($shopElement["PROP"]["STORE_ID"]["VALUE"])){
            //собираем в склады магазины
            $storeList[(int)$shopElement["PROP"]["STORE_ID"]["VALUE"]]["SHOPS"][] = $shopElement;
            $storeIds[] = (int)$shopElement["PROP"]["STORE_ID"]["VALUE"];
        }
    }
    $activeStorage = array();
    //поиск только активных складов
    $resStore = CCatalogStore::GetList(array(),array("ACTIVE" => "Y", "ID" => $storeIds),false,false,array("ID"));
    while($sklad = $resStore->Fetch()){
        $activeStorage[] = $sklad["ID"];
    }
    
}

//обход массива товаров (Доставка из магазина)
$storeProductIds = [];
if (!empty($_SESSION["GOODS_DELIVERY_STORE"])){
    foreach ($_SESSION["GOODS_DELIVERY_STORE"] as $good_id) {
        //выборка складов с товарами в наличии (AMOUNT)
        $rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' => $good_id,"STORE_ID"=>$activeStorage,">AMOUNT"=>0), false, false, array());
        while ($arStore = $rsStore->Fetch()) {
            $storeProductIds[] = $arStore["STORE_ID"];
        }
    }
}


$arResult["DELIVERY_STORE_LIST"] = [];
//если один из товаров есть в наличии в складах города
if (!empty($storeProductIds)){
    //поиск одинаковых складов на которых доступны все товары и объединение их в один массив
    //$commonStoreList = array_intersect(...$storeProductIds);
        //удаляем из массива лишние склады
    foreach($storeList as $storeIdItem => $storeItem){
        if (!in_array($storeIdItem,$storeProductIds)) {
            unset($storeList[$storeIdItem]);
        }
    }
    $arResult["DELIVERY_STORE_LIST"] = $storeList;
}


if (empty($arResult["DELIVERY_STORE_LIST"])){
    //удалим из списка доставку Самовывоз из магазина, если не найдено магазинов
    foreach($arResult["DELIVERY"] as $key_del => $delivery){
        if ($delivery["ID"] == ID_DELIVERY_STORE){
            unset($arResult["DELIVERY"][$key_del]);
            break;
        }
    }
}


/*перемещение ошибок*/
if (!empty($arResult['ERROR'])) {
    foreach ($arResult['ERROR'] as $keyError => $arError):
        $pos1 = stripos($arError, 'Выберите пункт самовывоз');
        if ($pos1 !== false) {
            $arResult['ERROR_KEY']['PICKUP'] = $keyError;
        }
        foreach ($arResult['ORDER_PROP']['PRINT'] as $keyProp => $arProperty) {
            $pos1 = stripos($arError, $arProperty['NAME']);
            if ($pos1 !== false) {
                $arResult['ERROR_KEY']['PRINT'][$keyProp] = $keyError;
            }
        }
        foreach ($arResult['ORDER_PROP']['USER_PROPS_N'] as $keyProp => $arProperty) {
            $pos1 = stripos($arError, $arProperty['NAME']);
            if ($pos1 !== false) {
                $arResult['ERROR_KEY']['USER_PROPS_N'][$keyProp] = $keyError;
            }
        }
        foreach ($arResult['ORDER_PROP']['USER_PROPS_Y'] as $keyProp => $arProperty) {
            $pos1 = stripos($arError, $arProperty['NAME']);
            if ($pos1 !== false) {
                $arResult['ERROR_KEY']['USER_PROPS_Y'][$keyProp] = $keyError;
            }
        }
        foreach ($arResult['ORDER_PROP']['RELATED'] as $keyProp => $arProperty) {
            $pos1 = stripos($arError, $arProperty['NAME']);
            if ($pos1 !== false) {
                $arResult['ERROR_KEY']['RELATED'][$arProperty['ID']] = $keyError;
            }
        }
    endforeach;
}

usort($arResult["ORDER_PROP"]["USER_PROPS_N"], function ($a, $b) {
    return ($a['SORT'] - $b['SORT']);
});

$arResult["ORDER_PROP"]['HOME_PROPS'] = [];
foreach ($arResult["ORDER_PROP"]["USER_PROPS_N"] as &$prop) {
    $prop['PLACEHOLDER'] = getPlaceholder($prop['CODE']);
    if (in_array($prop['CODE'], ['STREET', 'HOUSE', 'FLAT'])) {
        $arResult["ORDER_PROP"]['HOME_PROPS'][] = $prop;
    }
}

foreach ($arResult["ORDER_PROP"]["USER_PROPS_Y"] as &$prop) {
    $prop['PLACEHOLDER'] = getPlaceholder($prop['CODE']);
}


function getPlaceholder($code)
{
    switch ($code) {
        case 'FIO':
        case 'EMAIL':
        case 'PHONE':
        case 'LOYALTY_CARD':
        case 'ADDRESS':
        case 'STREET':
        case 'HOUSE':
        case 'FLAT':
        case 'NAME':
        case 'SECOND_NAME':
        case 'SURNAME':
            return GetMessage('SOA_PROP_PLACEHOLDER_' . $code);
        default:
            return $code;
    }
}

$arResult['isSelfDelivery'] = false;
foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery) {
    if ($arDelivery["ID"] == ID_DELIVERY_STORE) {
        foreach ($arDelivery["STORE"] as $store) {
            if (array_key_exists($store, $arResult["DELIVERY_STORE_LIST"])) {
                $arResult['isSelfDelivery'] = true;
            }
        }
    }
}
if (!$arResult['isSelfDelivery']) {
    foreach ($arResult["DELIVERY"] as $delivery_id => $arDelivery) {
        if ($arDelivery["ID"] == ID_DELIVERY_STORE) {
            unset($arResult["DELIVERY"][$delivery_id]);
        }
    }
}