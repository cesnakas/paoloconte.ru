<? // Class Loader PSR-0. Classes contained in /local/src/.

define('LOGFILE', '/local/var/log/main.log');
define('DEBUG_LOG_EVENT_ORDER', 'Y');

ini_set('memory_limit', '4096M');

if (
    $_SERVER['HTTP_HOST'] != 'm.new.paoloconte.ru'
    && $_SERVER['HTTP_HOST'] != 'm.paoloconte.ru:443'
    && $_SERVER['HTTP_HOST'] != 'www.m.paoloconte.ru:443'
    && $_SERVER['HTTP_HOST'] != 'm.paoloconte.ru:80'
    && $_SERVER['HTTP_HOST'] != 'www.m.paoloconte.ru:80'
    && $_SERVER['HTTP_HOST'] != 'm.paoloconte.ru'
    && $_SERVER['HTTP_HOST'] != 'www.m.paoloconte.ru'
    && strpos($_SERVER['HTTP_HOST'], 'mpaoloconte.testfact4.ru') === false
) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
}

use Citfact\CloudLoyalty\DataLoyalty;
use Citfact\CloudLoyalty\Events;
use Citfact\CloudLoyalty\LoyaltyLogger;
use Citfact\ProductAvailability;
use Fact;
use Bitrix\Sale\Internals;
use Bitrix\Sale\Internals\DiscountCouponTable;
use Bitrix\Sale\DiscountCouponsManager;
use Bitrix\Main;
use Bitrix\Main\Type\DateTime;
use Bitrix\Sale\Order;
use Bitrix\Main\Localization\Loc;
use Citfact\CloudLoyalty\Operation;
use Citfact\CloudLoyalty\OperationManager;

define(CODE_IBLOCK_CITIES,'city'); //Код инфоблока Справочник магазинов
define(CODE_IBLOCK_SHOPS,'shops'); //Код инфоблока Справочник магазинов
define(CODE_PIKUP_ADDRESS,'PICKUP_ADDRESS'); //Код свойства заказа "Адрес магазина для самовывоза"
define(CODE_EXTERNAL_CODE_STORE,'EXTERNAL_CODE_STORE'); // Код свойства заказа "Внешний код склада"
define(CODE_NAME_STORE,'NAME_STORE'); // Код свойства заказа "Наменование склада"
define(CODE_PICKUP_DELIVERY,'bx_self_export_5ec50df592bcb'); //Код доставки самовывоза
define('CLOYALITY_EXCLUDED_SECTIONS', ['183', '181', '260', '243', '241']); // родительские разделы исключения из программы лояльности
define('DISCOUNT_ID_SUBSCRIBE','12'); // ID скидки за подписку


define(SECT_WOMAN_SHOES, 'zhenskaya-obuv-2');
define(SECT_MAN_SHOES, 'muzhskaya-obuv-2');
define(IMAGE_PLACEHOLDER, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=');
Loc::loadLanguageFile(__FILE__);

$eventManager = \Bitrix\Main\EventManager::getInstance();

Main\Loader::includeModule('sale');
CModule::IncludeModule('citfact.tools');
CModule::IncludeModule('citfact.aqsi');

if (!(defined('EXCHANGE_1C') && EXCHANGE_1C)) {
    \Citfact\Seo\UtmManager::getInstance()->registerEvents();
}

define('ID_DELIVERY_STORE',\Citfact\Tools::getIdDelivery(CODE_PICKUP_DELIVERY)); // ID доставки "Самовывоз из магазина"

AddEventHandler("main", 'OnProlog', 'setCurrentSectionCodeBySectionCodePath');
function setCurrentSectionCodeBySectionCodePath()
{
    $_REQUEST['SECTION_PATH'] = preg_replace('/\%\d+\F/', '/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (!(strpos($_SERVER['HTTP_HOST'], 'testfact') === false)) {
        $_REQUEST['SECTION_PATH'] = str_replace('index.php', "", $_REQUEST['SECTION_PATH']);
    }
    unset($_GET['SECTION_PATH']);
    $path = explode('/', $_REQUEST['SECTION_PATH']);
    $_REQUEST['CATALOG_CODE'] = getCatalogCode($path);

    if ($_REQUEST['CATALOG_CODE'] === 'catalog' || $_REQUEST['CATALOG_CODE'] === 'search') {
        $_REQUEST['CATALOG_CODE'] = '';
    }
}

function getCatalogCode($path) {
    $code = '';
    if (!empty($path)) {
        $code = array_pop($path);
        if (empty($code)) {
            $code = getCatalogCode($path);
        }
    }
    return $code;
}

//массив с текущем путем
$curPath = explode("/", $APPLICATION->GetCurDir());
$curPath = array_filter($curPath);
if($APPLICATION->GetCurPage() == '/bitrix/admin/sale_order_props_edit.php' || $APPLICATION->GetCurPage() == '/bitrix/admin/sale_order_new.php'){
    $APPLICATION->AddHeadScript('/local/templates/paoloconte/javascript/custom_admin.js');
}

//#58042#, #50660#
AddEventHandler("main", "OnEpilog", "activeLinkOnComment");
function activeLinkOnComment()
{
    global $USER, $APPLICATION;
    $curPage = $APPLICATION->GetCurPage();

    if($USER->IsAdmin() && $curPage == '/bitrix/admin/iblock_list_admin.php'){
        CJSCore::Init(array("jquery")); ?>
        <script>
            $(document).ready(function () {
                var sort = $('.adm-list-table-cell-sort');
                var colTotal = 0;
                for (var i = 0; i < sort.length; i++) {
                    colTotal++;
                }

                for (var i = 0; i < sort.length; i++) {
                    if ( sort[i].title === 'Сортировка: Ссылка на товар') {
                        var table = $('tr.adm-list-table-row');

                        for (var i = 0; i < table.length; i++) {
                           var tr = $(table[i]).find('td');
                           var marker = '/catalog/';
                            for(numbrCol=0; numbrCol<tr.length; numbrCol++){

                                var unfilteredHtml = $(tr[numbrCol]).html();
                                var filteredHtml = unfilteredHtml.replace(/%2F/g, '/');
                                $(tr[numbrCol]).html(filteredHtml);

                                var line = $(tr[numbrCol]).html();
                                if (line.indexOf(marker) === 0){
                                    $(tr[numbrCol]).html('<a href="'+$(tr[numbrCol]).html()+'" target="_blank">'+$(tr[numbrCol]).html()+'</a>');
                                }
                            }
                        }
                    }
                }
            });
        </script>
        <?
    }
}


// Для композита вот это:
class CacheProvider extends Bitrix\Main\Data\StaticCacheProvider
{
    public static function createKey()
    {
        global $USER;
        $page_name = "page";
        if (!empty($_SESSION["CITY_ID"]))
            $page_name .= "_region_" . $_SESSION["CITY_ID"];

        return $page_name;
    }

    public function setUserPrivateKey()
    {
    }

    public function isCacheable()
    {
        return true;
    }

    public function getCachePrivateKey()
    {
        return self::createKey();
    }

    public function onBeforeEndBufferContent()
    {
    }
}

AddEventHandler("main", "OnGetStaticCacheProvider", "OnGetStaticCacheProviderHandler");
function OnGetStaticCacheProviderHandler()
{
    return new CacheProvider;
}


// Заполняем свойство «Популярность» при добавлении товара в корзину
// событие вызывается перед добавлением товара к корзину. на вход передаются параметры товара. в случае если возвращает false, товар в корзину добавлен не будет.
// http://dev.1c-bitrix.ru/api_help/sale/sale_events.php
AddEventHandler("sale", "OnBeforeBasketAdd", Array("MyBasket", "BeforeBasketAdd"));

class MyBasket
{
    function BeforeBasketAdd($arFields)
    {
        // Подключаем модуль
        if (CModule::IncludeModule("iblock")) {
            // Получаем элемент
            $arOrder = array("SORT" => "ASC");
            $arFilter = array('ID' => $arFields["PRODUCT_ID"], 'IBLOCK_ID' => IBLOCK_SKU);
            $arSelectFields = array("ID", "ACTIVE", "NAME");
            $rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE/*, $arSelectFields*/);
            // Если нашли торговое предложение, то узнаем id товара-родителя и увеличиваем счетчик у него
            if ($arElement = $rsElements->GetNext()) {
                $arFilter = array('IBLOCK_ID' => IBLOCK_SKU, 'ID' => $arFields['PRODUCT_ID']);
                $arSelectFields = array("ID", "NAME", 'PROPERTY_CML2_LINK', 'PROPERTY_CML2_LINK.PROPERTY_POPULAR');
                $rsElements2 = CIBlockElement::GetList(array(), $arFilter, FALSE, FALSE, $arSelectFields);
                if ($arElement2 = $rsElements2->GetNext()) {
                    // Получаем значение свойства-счетчика
                    $COUNT_TO_BASKET = $arElement2["PROPERTY_CML2_LINK_PROPERTY_POPULAR_VALUE"];
                    // Увеличиваем счетчик на единицу и сохраняем значение в свойстве
                    CIBlockElement::SetPropertyValueCode($arElement2["PROPERTY_CML2_LINK_VALUE"], "POPULAR", intval($COUNT_TO_BASKET) + 1);
                }
            } else {
                $arFilter = array('IBLOCK_ID' => IBLOCK_CATALOG, 'ID' => $arFields['PRODUCT_ID']);
                $arSelectFields = array("ID", "NAME", 'PROPERTY_POPULAR');
                $rsElements2 = CIBlockElement::GetList(array(), $arFilter, FALSE, FALSE, $arSelectFields);
                if ($arElement2 = $rsElements2->GetNext()) {
                    $COUNT_TO_BASKET = $arElement2["PROPERTY_POPULAR_VALUE"];
                    CIBlockElement::SetPropertyValueCode($arElement2['ID'], "POPULAR", intval($COUNT_TO_BASKET) + 1);
                }
            }
        }
    }
}


// Email в качестве логина при оформлении заказа
AddEventHandler("main", "OnBeforeUserAdd", Array("Handlers", "OnBeforeUserAddHandler"));

class Handlers
{
    function OnBeforeUserAddHandler(&$arFields)
    {
        // Если пользователь уже существует, добавляем к email единицу
        global $USER;
        $rsUser = $USER->GetByLogin($arFields['EMAIL']);
        if ($arUser = $rsUser->Fetch()) {
            $arEmail = explode('@', $arFields['EMAIL']);
            $arEmail[0] = $arEmail[0] . '1';
            $arFields['LOGIN'] = implode('@', $arEmail);
        } else {
            $arFields['LOGIN'] = $arFields['EMAIL'];
        }
    }
}




/*
Добавляем в заказ ДР пользователя, чтобы далее передать его в 1С
На текущий момнет битрикс не умеет передавать ДР пользователя (как параметр пользователя) в 1С
отключили, т.к. ломалась обмен заказами с Б24
задача 47087
 */
//AddEventHandler("sale", "OnOrderSave", "OnOrderSave_birthday");
function OnOrderSave_birthday($ID, $arFields)
{
    $codeProp = 'birthday';
    $arUserGroupNotSimple = array(
        1, // Администраторы
        5, // Редакторы сайта
        10, // Администраторы интернет-магазина
        11, // Редакторы новостей и форума
        13, // Операторы чата
        18, // Обмен с 1С
        20, // Управление вакансиями
        21, // Управление заказами
    );
    if ($arFields['USER_ID'] > 0) {
        $db_vals = CSaleOrderPropsValue::GetList(array("SORT" => "ASC"),array("ORDER_ID" => $ID, "CODE" => $codeProp)); // проверяем заполнено свойство или нет
        if ($arVals = $db_vals->Fetch()){
            // если заполнено, не трогаем
        }else{ // если пустое добавляем
            $arGroups = CUser::GetUserGroup($arFields['USER_ID']); // группы пользователя
            $arGroupDiff = array_diff($arGroups, $arUserGroupNotSimple); // выводит из 1-го массива все то, чего нет во втором массиве
            if ($arGroupDiff == $arGroups) { // соответственно, если группы пользователей схожи с массивом расхождений, значит нет ни одного совпадения с группой менеджеров
                $dbUsers = CUser::GetList($sort_by = "ID", $sort_ord = "ASC", array('ID'=>$arFields['USER_ID']), array('FIELDS' => array('ID', 'PERSONAL_BIRTHDAY'))); // если ДР заполнено у пользователя
                if ($arUser = $dbUsers->Fetch())
                {
                    if (!empty($arUser['PERSONAL_BIRTHDAY'])) {
                        $db_props = CSaleOrderProps::GetList(array("SORT" => "ASC"),array("CODE" => $codeProp,), false, false, array('NAME', 'CODE', 'ID')); // если на сайте вообще есть данное свойство, достаем его ID и NAME
                        if ($props = $db_props->Fetch())
                        {
                            $arFieldsProp = array(
                                "ORDER_ID" => $ID,
                                "ORDER_PROPS_ID" => $props['ID'],
                                "NAME" => $props['NAME'],
                                "CODE" => $codeProp,
                                "VALUE" => $arUser['PERSONAL_BIRTHDAY'],
                            );
                            CSaleOrderPropsValue::Add($arFieldsProp);
                        }
                    }
                }
            }
        }
    }
}

AddEventHandler("sale", "OnOrderSave", "OnBeforeOrderAddHandler");
function OnBeforeOrderAddHandler($orderID, $arFields, $orderFields, $isNew)
{
    $propsID = [];
    $addressPropValue = $address = $locationPropValue = '';
    $db_props = CSaleOrderProps::GetList(
        ['SORT' => 'ASC'],
        [
            'PERSON_TYPE_ID' => 1,
            'CODE' => ['FULL_ADDRESS', 'LOCATION', 'ADDRESS']
        ],
        false,
        false,
        []
    );

    while ($props = $db_props->Fetch()) {
        $propsID[$props['CODE']] = $props['ID'];
    }
    if ($propsID) {
        $order = Bitrix\Sale\Order::load($orderFields['ID']);
        $propertyCollection = $order->getPropertyCollection();
        $addressProp = $propertyCollection->getItemByOrderPropertyId($propsID['ADDRESS']);
        $locationProp = $propertyCollection->getItemByOrderPropertyId($propsID['LOCATION']);
        $fullAddressProp = $propertyCollection->getItemByOrderPropertyId($propsID['FULL_ADDRESS']);

        if (empty($fullAddressProp->getValue())) { //если поле Полный адрес доставки пустое
            $locationPropValue = $locationProp->getViewHtml();
            if ($addressProp) {
                $address = (!empty($addressProp->getValue())) ? ', ' . $addressProp->getValue() : '';
            }
            $addressPropValue = $locationPropValue . $address;

            $fullAddressProp->setValue($addressPropValue);
            $order->save();
        }
    }
}

// После добавления заказа
AddEventHandler("sale", "OnOrderAdd", "OnOrderAdd_coupon");
function OnOrderAdd_coupon($ID, $arFields)
{
    // При добавлении заказа, если в сессию записан код купона, то выструмляем дату применения этому купону
    if (!empty($_SESSION['APPLIED_COUPONS'])) {
        $arFilterCoup = array('COUPON' => $_SESSION['APPLIED_COUPONS']);
        $arSelectCoup = array('ID', 'ONE_TIME');
        $recCoup = CCatalogDiscountCoupon::GetList(
            array(),
            $arFilterCoup,
            false,
            false,
            $arSelectCoup
        );
        while ($coupon = $recCoup->GetNext()) {
            global $DB;
            $arFields = array(
                'DATE_APPLY' => date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), time())
            );
            // Если купон не многоразовый, снимаем активность
            if ($coupon['ONE_TIME'] != 'N') {
                $arFields['ACTIVE'] = 'N';
            }
            CCatalogDiscountCoupon::Update($coupon['ID'], $arFields);
            unset($_SESSION['APPLIED_COUPONS']);
        }
    }

}


// Здесь НЕ OnOrderAdd, так как свойства заказа заполняются после него
AddEventHandler("sale", "OnSaleComponentOrderOneStepFinal", "OnOrderAddHandler");
function OnOrderAddHandler($ID, $arFields)
{
    CModule::IncludeModule("sale");
    CModule::IncludeModule("iblock");

    $location = 0;
    $address = '';

    $db_props = CSaleOrderPropsValue::GetOrderProps($ID);
    while ($arProp = $db_props->Fetch()) {
        if ($arProp['CODE'] == 'LOCATION') {
            $location = $arProp['VALUE'];
        }
        if ($arProp['CODE'] == 'ADDRESS') {
            $address = $arProp['VALUE'];
        }
    }

    if ($location != 0 && $address != '' && strpos($address, 'Пункт') === false) {
        $IBLOCK_ID = 33;
        $user_id = $arFields['USER_ID'];

        // Ищем адрес с такими же параметрами
        $arOrder = array();
        $arFilter = array('IBLOCK_ID' => $IBLOCK_ID, 'ACTIVE' => 'Y',
            'PROPERTY_USER' => $user_id, 'PROPERTY_LOCATION' => $location, 'PROPERTY_ADDRESS' => $address,
        );
        $arSelectFields = array("ID", "ACTIVE", "NAME");
        $rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
        $found = false;
        if ($arElement = $rsElements->GetNext()) {
            $found = true;
        }

        // Если не нашли - добавляем новый адрес
        if ($found === false) {
            $el = new \CIBlockElement;
            $arPropVals['USER'] = $user_id;
            $arPropVals['LOCATION'] = htmlspecialcharsbx(trim($location));
            $arPropVals['ADDRESS'] = htmlspecialcharsbx(trim($address));
            $arLoadProductArray = Array(
                "MODIFIED_BY" => $user_id,
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID" => $IBLOCK_ID,
                "NAME" => 'Адрес',
                "ACTIVE" => 'Y',
                'PROPERTY_VALUES' => $arPropVals,
            );

            // Добавляем в инфоблок
            if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {

            } else {

            }
        }
    }


    // В свойство заказа пишем ID региональных цен
    $arGeoPrices = \Citfact\Paolo::GetRegionPriceTypes($_SESSION['CITY_ID']);
    $price_id = $arGeoPrices['PRICE_ID'];
    $price_id_action = $arGeoPrices['PRICE_ID_ACTION'];
    $str_to_prop = $price_id . ',' . $price_id_action;

    $arFilter = Array("ID" => $ID);
    $db_sales = CSaleOrder::GetList(array(), $arFilter, false, array('*'));
    if ($arOrder = $db_sales->Fetch()) {
        $arPropCodes = array("PRICES_ID");

        foreach ($arPropCodes as $propCode) {
            //Получаем свойство заказа
            if ($arProp = CSaleOrderProps::GetList(array(), array('CODE' => $propCode))->Fetch()) {
                // Если нашли значение - обновляем его
                // Если не нашли - создаем новое
                if ($arPropValue = CSaleOrderPropsValue::GetList(array("SORT" => "ASC"),
                    array(
                        "ORDER_ID" => $ID,
                        "ORDER_PROPS_ID" => $arProp["ID"]
                    ))->Fetch()
                ) {
                    CSaleOrderPropsValue::Update($arPropValue['ID'], array("VALUE" => $str_to_prop));
                } else {
                    $arFields = array(
                        "ORDER_ID" => $ID,
                        "ORDER_PROPS_ID" => $arProp['ID'],
                        "NAME" => $arProp['NAME'],
                        "CODE" => $arProp['CODE'],
                        "VALUE" => $str_to_prop
                    );
                    CSaleOrderPropsValue::Add($arFields);
                }
            }
        }
    }
}


// Перед отправкой письма о новом заказе
AddEventHandler("sale", "OnOrderNewSendEmail", "OnOrderNewSendEmailHandler");
function OnOrderNewSendEmailHandler($ID, &$eventName, &$arFields)
{
    require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/citfact.tools/constants.php");
    $originalDiscount = DataLoyalty::getInstance()->getOriginalDiscount();
    $originalPrices = DataLoyalty::getInstance()->getOriginalPrices();

    if ($ID > 0 && CModule::IncludeModule('iblock')) {
        $arFields['ORDER_LIST'] = '<table cellpadding="5" cellspacing="5">';
        $rsBasket = CSaleBasket::GetList(array(), array('ORDER_ID' => $ID));
        while ($arBasket = $rsBasket->GetNext()) {
            //мы берем картинку только если это товар из инфоблока
            if ($arBasket['MODULE'] == 'catalog') {
                if ($arProduct = CIBlockElement::GetByID($arBasket['PRODUCT_ID'])->Fetch()) {
                    $db_props = CIBlockElement::GetProperty($arProduct['IBLOCK_ID'], $arProduct['ID'], array(), Array("CODE" => "CML2_ARTICLE"));
                    if ($ar_props = $db_props->Fetch())
                        $article = $ar_props["VALUE"];
                    else
                        $article = false;

                    $imageConfig = array(
                        'TYPE' => 'ONE',
                        'SIZE' => array(
                            'SMALL' => array('W' => 80, 'H' => 80)
                        )
                    );
                    $arPhotos = Citfact\Paolo::getProductImage($article, $imageConfig);
                    $photo_path = $arPhotos['PHOTO'][0]['SMALL'];
                }
            }
            $price = $arBasket['PRICE'];

            if ($_SESSION['ORIGINAL_DISCOUNT_PROMOCODE_APPLY']
                && intval($_SESSION['ORIGINAL_DISCOUNT_PROMOCODE'][$arBasket['PRODUCT_ID']]['PRICE']) > 0) {
                $price = intval($_SESSION["ORIGINAL_DISCOUNT_PROMOCODE"][$arBasket['PRODUCT_ID']]['PRICE']);
            }

            if (DataLoyalty::getInstance()->getUseCloudScore() == "Y"
                && intval($originalPrices[$arBasket['PRODUCT_ID']]) > 0
                && intval($originalDiscount[$arBasket['PRODUCT_ID']]) > 0
            ) {
                $price = $price - intval($originalDiscount[$arBasket['PRODUCT_ID']]);
            }

            $arFields['ORDER_LIST'] .= '<tr valign="top">'
                . '<td style="vertical-align: middle;">' . ($arPhotos ? '<img src="http://' . $GLOBALS['SERVER_NAME'] . (str_replace(array('+', ' '), '%20', $photo_path)) . '" width="" height="" alt="Фото товара">' : '') . '</td>'
                . '<td style="vertical-align: middle;">' . $arBasket['NAME'] . '</td>'
                . '<td style="white-space: nowrap; vertical-align: middle;">' . (int)$arBasket['QUANTITY'] . ' шт.</td>'
                . '<td style="white-space: nowrap; vertical-align: middle;">' . SaleFormatCurrency($price, $arBasket['CURRENCY']) . '</td>'
                . '</tr>';
        }
        $arFields['ORDER_LIST'] .= '</table>';

        $order = \Bitrix\Sale\Order::load($ID);
        $basket = $order->getBasket();
        $bonusData = DataLoyalty::getInstance()->getBonusData();
        $arFields['PRICE'] =
            $basket->getPrice()
            - (int)$bonusData['applied']
            + (int)$arFields['DELIVERY_PRICE']
            - (int)$_SESSION['ORIGINAL_DISCOUNT_PROMOCODE_APPLY'];
        $arFields['PRICE'] = number_format($arFields['PRICE'], 0, ',', ' ') . " руб.";
        $_SESSION['ORIGINAL_DISCOUNT_PROMOCODE_APPLY'] = 0;
        $_SESSION['ORIGINAL_DISCOUNT_PROMOCODE'] = [];
    }
}

AddEventHandler("sale", "OnSaleComponentOrderDeliveriesCalculated", "OnSaleComponentOrderDeliveriesCalculatedHandler");
function OnSaleComponentOrderDeliveriesCalculatedHandler($order, &$arUserResult, $request, &$arParams, &$arResult, &$arDeliveryServiceAll, &$arPaySystemServiceAll)
{
    $bonusData = DataLoyalty::getInstance()->getBonusData();
    //Если используется оплата бонусами CL и сумма к оплате менее 3 тысяч рублей, отменяем нулевую стоимость доставки
    if (DataLoyalty::getInstance()->getUseCloudScore() == "Y"){
        //Проверка на сумму корзины к оплате
        $basket = $order->getBasket();
        $basketPrice = $basket->getPrice();
        $clApplied = $bonusData['applied'];

        if ($basketPrice - $clApplied < \COption::GetOptionInt('citfact.tools', 'min_price_delivery', 3000)){

            foreach ($arResult["DELIVERY"] as $key => $item){
                if ($item["CHECKED"] == "Y")
                {

                    $arResult["DELIVERY"][$key]["DELIVERY_DISCOUNT_PRICE"] = $arResult["DELIVERY"][$key]["PRICE"];
                    $arResult["DELIVERY"][$key]["DELIVERY_DISCOUNT_PRICE_FORMATED"] = $arResult["DELIVERY"][$key]["PRICE_FORMATED"];

                    $arResult["JS_DATA"]["DELIVERY"][$key]["DELIVERY_DISCOUNT_PRICE"] = $arResult["DELIVERY"][$key]["PRICE"];
                    $arResult["JS_DATA"]["DELIVERY"][$key]["DELIVERY_DISCOUNT_PRICE_FORMATED"] = $arResult["DELIVERY"][$key]["PRICE_FORMATED"];

                    $arResult["DELIVERY_PRICE"] = $arResult["DELIVERY"][$key]["PRICE"];
                    $arResult["DELIVERY_PRICE_FORMATED"] = $arResult["DELIVERY"][$key]["PRICE_FORMATED"];

                    $roundPriceDelivery = round($arResult["DELIVERY"][$key]["PRICE"]);
                }
            }

            $roundPriceBasketAll = 0;
            $basket = $order->getBasket();
            $basketItems = $basket->getBasketItems();
            foreach ($basketItems as $key => $basketItem) {
                $roundPriceBasketItem = round($basketItem->getPrice());
                $roundPriceBasketAll = $roundPriceBasketAll + $roundPriceBasketItem;
            }
            $allSum = $roundPriceDelivery + $roundPriceBasketAll;

            foreach ($order->getShipmentCollection() as $shipment)
            {
                if (!$shipment->isSystem()) {
                    $shipment->setField('CUSTOM_PRICE_DELIVERY', 'Y');
                    $shipment->setFieldNoDemand('PRICE_DELIVERY', $roundPriceDelivery);
                    break;
                }
            }

            $order->setFieldNoDemand('PRICE_DELIVERY', $roundPriceDelivery);
            $order->setFieldNoDemand('PRICE', $allSum);
        }
    }
}
AddEventHandler("sale", "OnBasketAdd", "OnBasketAddHandler");
function OnBasketAddHandler($ID, $arFields)
{
    DataLoyalty::getInstance()->deleteOriginalDiscount();
}
AddEventHandler("sale", "OnBeforeBasketDelete", "OnBasketDeleteHandler");
function OnBasketDeleteHandler($ID)
{
    DataLoyalty::getInstance()->deleteOriginalDiscount();
}
// Обработчик события "OnGetOptimalPrice"
AddEventHandler("catalog", "OnGetOptimalPrice", "OnGetOptimalPriceHandler");
function OnGetOptimalPriceHandler($intProductID, $quantity, $arUserGroups, $renewal, $arPrices, $siteID, $arDiscountCoupons = false)
{
    LoyaltyLogger::log('OnGetOptimalPrice. intProductID: ' . $intProductID);
    $originalDiscount = DataLoyalty::getInstance()->getOriginalDiscount();
    $originalPrices = DataLoyalty::getInstance()->getOriginalPrices();

    if (DataLoyalty::getInstance()->getUseCloudScore() == "Y") {
        LoyaltyLogger::log(intval($originalDiscount), 'intval($originalDiscount)');

        if (intval($originalDiscount[$intProductID])){
            return true;
        }

        $fuser = \Bitrix\Sale\Fuser::getId();
        $basket = \Bitrix\Sale\Basket::loadItemsForFUser($fuser, SITE_ID);
        $bonusData = \Citfact\CloudLoyalty\Events::calculatePurchase();
        $log[$intProductID]['BONUS_DATA'] = $bonusData;
        $basketData = array();

        $productsData = array();
        foreach ($basket as $basitem)
        {
            if ($basitem->getField("DELAY") == "Y" || $basitem->getField("CAN_BUY") == "N"){
                continue;
            }

            $arItem = array();

            $prodId = $basitem->getField("PRODUCT_ID");
            $offerInfo = \CCatalogSku::GetProductInfo($basitem->getField("PRODUCT_ID"));
            if ($offerInfo){
                $prodId = $offerInfo["ID"];
            }

            $res = CIBlockElement::GetByID($prodId);
            while($arRes = $res->GetNext())
            {
                if (in_array($arRes["IBLOCK_SECTION_ID"], $_SESSION["cloyalty_excluded_sections"]))
                {
                    $arItem["PRODUCT_EXCLUDED"] = true;
                    $basketData["CLOTHES_PRICE"] += $basitem->getField("PRICE");
                }
            }

            $arItem["PRODUCT_ID"] = $basitem->getField("PRODUCT_ID");
            $arItem["BASKET_ITEM_ID"] = $basitem->getField("ID");
            $arItem["QUANTITY"] = $basitem->getField("QUANTITY");
            $arItem["PRICE"] = $basitem->getField("PRICE");
            $basketData["PRODUCTS"][] = $arItem;
        }
        $totalPrice = 0;
        foreach ($basketData["PRODUCTS"] as $basitem)
        {
            $totalPrice += intval($basitem["PRICE"]);
        }
        $basketData["PRICE_TOTAL"] = $totalPrice - $basketData["CLOTHES_PRICE"];
        $basketData["FUSER"] = $fuser;
        $basketData["CLOUD_SCORE"] = intval($bonusData['maxToApplyForThisOrder']);

        $discountRatio = $basketData["CLOUD_SCORE"] / $basketData["PRICE_TOTAL"] ;
        $discountRound = 0;
        $firstClKey = -1;
        foreach($basketData["PRODUCTS"] as $key=>$product)
        {
            if ($product["PRODUCT_EXCLUDED"]){
                continue;
            }
            if ($firstClKey == -1){
                $firstClKey = $key;
            }
            $basketData["PRODUCTS"][$key]["CLOUD_DISCOUNT"] = floor($product["PRICE"] * $discountRatio / 10) * 10;
            $discountRound += $basketData["PRODUCTS"][$key]["CLOUD_DISCOUNT"];
        }

        $discountDelta = $basketData["CLOUD_SCORE"] - $discountRound;
        $basketData["PRODUCTS"][$firstClKey]["CLOUD_DISCOUNT"] += $discountDelta;
    }
//    LoyaltyLogger::log($basketData["PRODUCTS"], '$basketData["PRODUCTS"]');
    if (intval($_SESSION["PRODUCT_PRICED"][$intProductID]) && DataLoyalty::getInstance()->getUseCloudScore() != "Y"){
        return true;
    }

    $arPrices = GetCatalogProductPriceList($intProductID);
    $arTemp = array();
    foreach ($arPrices as $key => $arPrice) {
        $arTemp[trim($arPrice['CATALOG_GROUP_ID'])] = $arPrice;
    }

    $arPrices = $arTemp;

    $arGeoPrices = \Citfact\Paolo::GetRegionPriceTypes($_SESSION['CITY_ID']);

    $price_id = $arGeoPrices['PRICE_ID'];
    $price_id_action = $arGeoPrices['PRICE_ID_ACTION'];

    // Если мы в админке
    if (SITE_ID == 'ru' /*&& empty($arDiscountCoupons)*/) {
        // Если вызываем обработчик из редактирования заказа
        if (strpos($_SERVER['HTTP_REFERER'], 'sale_order_new.php') !== false) {
            $arUrl = parse_url($_SERVER['HTTP_REFERER']);
            $arRequestParams = array();
            parse_str($arUrl['query'], $arRequestParams);

            $ORDER_ID = (int)$arRequestParams['ID'];
            // Получаем id региональных цен из заказа
            // Если нашли, подставляем id типов цен, по которым был сделан заказ
            $arFilter = Array("ID" => $ORDER_ID);
            $db_sales = CSaleOrder::GetList(array(), $arFilter, false, array('*'));
            if ($arOrder = $db_sales->Fetch()) {
                $arPropCodes = array("PRICES_ID");
                foreach ($arPropCodes as $propCode) {
                    //Получаем свойство заказа
                    if ($arProp = CSaleOrderProps::GetList(array(), array('CODE' => $propCode))->Fetch()) {
                        // Если нашли значение
                        if ($arPropValue = CSaleOrderPropsValue::GetList(array("SORT" => "ASC"),
                            array(
                                "ORDER_ID" => $ORDER_ID,
                                "ORDER_PROPS_ID" => $arProp["ID"]
                            ))->Fetch()
                        ) {

                            $arPriceIds = explode(',', $arPropValue['VALUE']);
                            $price_id = $arPriceIds[0];
                            $price_id_action = $arPriceIds[1];
                        }
                    } else {
                        return false;
                    }
                }
            }
        }

        //return false;
    }

    $arRegionPrice = array();

    if ($arPrices[$price_id]['PRICE'] > 0 && $arPrices[$price_id_action]['PRICE'] != '') {
        $arRegionPrice = $arPrices[$price_id_action];
        $arRegionPriceID = $price_id_action;
    } else {
        $arRegionPrice = $arPrices[$price_id];
        $arRegionPriceID = $price_id;
    }

    $arRegionPrice["ELEMENT_IBLOCK_ID"] = IBLOCK_CATALOG;
    $discountResult = array();

    $arDiscounts = CCatalogDiscount::GetDiscountByProduct($intProductID, CSaleBasket::GetBasketUserID(), "N", $arRegionPriceID, SITE_ID);

    $discount_price = $arRegionPrice["PRICE"];
    foreach ($arDiscounts as $arDiscountsF) {
        if ($arDiscountsF["VALUE_TYPE"] == "F") {
            $discount_price = ($arRegionPrice["PRICE"] - $arDiscountsF["VALUE"]);
        }
        if ($arDiscountsF["VALUE_TYPE"] == "P") {
            $discount_price = ($arRegionPrice["PRICE"] - ($arRegionPrice["PRICE"] / 100 * $arDiscountsF["VALUE"]));
        }
        $discountResult = $arDiscountsF;
    }

    //если задан купон скидки
    if (!empty($arDiscountCoupons)) {
        $arFilterCoup = array(
            'COUPON' => $arDiscountCoupons[0],
            'ACTIVE' => 'Y',
        );
        $arSelectCoup = array();
        $recCoup = CCatalogDiscountCoupon::GetList(
            array(),
            $arFilterCoup,
            false,
            false,
            $arSelectCoup
        );
        $coupon = $recCoup->GetNext();

        // Если купон НЕ на одну позицию ИЛИ на одну позицию и с пустой датой применения, то применяем купон
        if (
            $coupon['ONE_TIME'] != 'Y'
            || ($coupon['ONE_TIME'] == 'Y' && $coupon['DATE_APPLY'] == '') /*&& !in_array($coupon['COUPON'], $_SESSION['COUPONS'])*/
        ) {
            $discountResult = CCatalogDiscount::GetByID($coupon['DISCOUNT_ID']);
            $discountResult['COUPON'] = $coupon['COUPON'];
        } else {
            $discountResult = array();
        }

        // Если купон на одну позицию заказа, то записываем его в сессию
        if ($coupon['ONE_TIME'] == 'Y') {
            $_SESSION['COUPONS'][] = $coupon['COUPON'];
        }

        // Примененный купон также записываем в сессию
        $_SESSION['APPLIED_COUPONS'][] = $coupon['COUPON'];
    }

    $arRegionPrice['DISCOUNT_PRICE'] = $discount_price;

    $arReturn = array(
        "PRICE" => $arRegionPrice,
        "DISCOUNT_PRICE" => $discount_price,
        "DISCOUNT" => $discountResult,
        "DISCOUNT_LIST" => (!empty($discountResult)) ? array($discountResult) : array(),
    );


//    LoyaltyLogger::log($arReturn, '$arReturn');
    LoyaltyLogger::log($originalPrices, '$originalPrices first state');
    LoyaltyLogger::log($originalDiscount, '$originalDiscount first state');

    if (DataLoyalty::getInstance()->getUseCloudScore() == "Y"){
        global $APPLICATION;
        $page = $APPLICATION->GetCurPage();
        if ($page == '/cabinet/basket/index.php')
        {
            foreach ($arReturn as $priceItem)
            {
                if (is_array($priceItem) && !empty($priceItem["PRODUCT_ID"]))
                {
                    $originalPrices[$priceItem["PRODUCT_ID"]] = $priceItem["DISCOUNT_PRICE"];
                    DataLoyalty::getInstance()->setOriginalPrices($originalPrices);

                    if (!$priceItem["PRODUCT_EXCLUDED"])
                    {
                        $originalDiscount[$priceItem["PRODUCT_ID"]] = intval($priceItem["CLOUD_DISCOUNT"]);
                        DataLoyalty::getInstance()->setOriginalDiscount($originalDiscount);
                    }
                }
            }

            foreach ($basketData["PRODUCTS"] as $basketItem)
            {
                if ($basketItem["PRODUCT_ID"] == $arReturn["PRICE"]["PRODUCT_ID"])
                {
                    if (!$basketItem["PRODUCT_EXCLUDED"])
                    {
                        $originalDiscount[$basketItem["PRODUCT_ID"]] = $basketItem["CLOUD_DISCOUNT"];
                        DataLoyalty::getInstance()->setOriginalDiscount($originalDiscount);
                    }
                }
            }
        }
    }
    $_SESSION["PRODUCT_PRICED"][$intProductID] = $arReturn;
    return $arReturn;
}


// При оплате заказа - записываем поля платежной системы в свойство заказа
//AddEventHandler("sale", "OnSalePayOrder", "OnSalePayOrderHandler");
//AddEventHandler("sale", "OnSaleBeforePayOrder", "OnSalePayOrderHandler");
function OnSalePayOrderHandler($ORDER_ID, $val)
{
    // Если флаг оплаты равен Y
    if ($val == 'Y') {
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");

        $arFilter = Array("ID" => $ORDER_ID);
        $db_sales = CSaleOrder::GetList(array(), $arFilter, false, array('*'));
        if ($arOrder = $db_sales->Fetch()) {
            //define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log_pay.txt");
            //AddMessage2Log($val.': '.print_r($arOrder, TRUE));

            $arPropCodes = array("PS_STATUS", "PS_STATUS_CODE", "PS_STATUS_DESCRIPTION",
                "PS_STATUS_MESSAGE", "PS_SUM", "PS_CURRENCY", "PS_RESPONSE_DATE");

            foreach ($arPropCodes as $propCode) {
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
                        CSaleOrderPropsValue::Update($arPropValue['ID'], array("VALUE" => $arOrder[$propCode]));
                    } else {
                        $arFields = array(
                            "ORDER_ID" => $ORDER_ID,
                            "ORDER_PROPS_ID" => $arProp['ID'],
                            "NAME" => $arProp['NAME'],
                            "CODE" => $arProp['CODE'],
                            "VALUE" => $arOrder[$propCode]
                        );
                        CSaleOrderPropsValue::Add($arFields);
                    }
                }
            }
        }
    }
}



// Перед изменением заказа сравниваем новый состав с составом из сессии
// В случае изменения шлем письмо
AddEventHandler("sale", "OnBeforeOrderUpdate", "OnBeforeOrderUpdateHandler");
function OnBeforeOrderUpdateHandler($ORDER_ID, &$arFields)
{
    // Если статус платежной системы равен Y
    if ($arFields['PS_STATUS'] == 'Y') {
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");

        $arFilter = Array("ID" => $ORDER_ID);
        $db_sales = CSaleOrder::GetList(array(), $arFilter, false, array('*'));
        if ($arOrder = $db_sales->Fetch()) {

            $arPropCodes = array("PS_STATUS", "PS_STATUS_CODE", "PS_STATUS_DESCRIPTION",
                "PS_STATUS_MESSAGE", "PS_SUM", "PS_CURRENCY", "PS_RESPONSE_DATE");

            foreach ($arPropCodes as $propCode) {
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
                        CSaleOrderPropsValue::Update($arPropValue['ID'], array("VALUE" => $arFields[$propCode]));
                    } else {
                        $arFieldsAdd = array(
                            "ORDER_ID" => $ORDER_ID,
                            "ORDER_PROPS_ID" => $arProp['ID'],
                            "NAME" => $arProp['NAME'],
                            "CODE" => $arProp['CODE'],
                            "VALUE" => $arFields[$propCode]
                        );
                        CSaleOrderPropsValue::Add($arFieldsAdd);
                    }
                }
            }
        }
    }
}




/**
 *  Для сдека уменьшаем ширину в 10 раз, т.к. вес в сдеке считается по формуле Ш*В*Д/5000
 * Что значительно больше, чем реальный вес
 *
 *
 * условия задаются модификацией массива arOrderGods, который выглядит следующим образом:
array(
'ключ' => array(
'DIMENSIONS' => array( // указываются в миллиметрах
'WIDTH'  => <ширина>,
'HEIGHT' => <высота>,
'LENGTH' => <длина>,
),
'WEIGHT' => <вес> // указывается в граммах
)
)
остальные поля модифицировать крайне не рекомендуется

!Не забудьте, что $arOrderGoods - указатель на массив
 */
AddEventHandler('ipol.sdek', 'onBeforeDimensionsCount', 'ipolSdekHandleGoods');
function ipolSdekHandleGoods(&$arOrderGoods){
    if(!cmodule::includeModule('iblock'))
        return;

    foreach($arOrderGoods as $key => $arGood){
        if ($arGood['DIMENSIONS']['WIDTH']) {
            $arOrderGoods[$key]['DIMENSIONS']['WIDTH'] = $arGood['DIMENSIONS']['WIDTH']/10;
        }
    }
}




/*
 * Проверяем, заполнен ли самовывоз для СДЕКа
 * task 26559 point 12
 * */

Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleComponentOrderResultPrepared', 'MyOnSaleComponentOrderResultPrepared');
function MyOnSaleComponentOrderResultPrepared($order, &$arUserResult, $request, &$arParams, &$arResult)
{
    if (
        $arUserResult['FINAL_STEP'] == 'Y' // финальный шаг
        && empty($arUserResult['ORDER_PROP'][SDEK_HIDDEN_PROP_PVZ]) // пустое свойство (заполняется на javascript в шаблоне сдека)
        && in_array($arUserResult['DELIVERY_ID'], array(SDEK_PROFILE_PICKUP, SDEK_PROFILE_PICKUP_ONLINE)) // выбрана доставка самовывоз
    ) {

        $arResult['ERROR'][] = Loc::getMessage("ERROR_SALE_ORDER_AJAX_SDEK_PVZ_EMPTY");
    }

}


// для заказа удаляем кастомные цены (округление цены), чтобы можно было купоны применять
Main\EventManager::getInstance()->addEventHandler('sale', 'OnBeforeSaleOrderFinalAction', array('\Citfact\EventListener\OrderRoundPrice', 'OnBeforeSaleOrderFinalAction'));

// для заказа округляем его суммы. Чтобы не было проблем с платежками
// также сохраняем купоны в свойство
Main\EventManager::getInstance()->addEventHandler('sale', 'OnAfterSaleOrderFinalAction', array('\Citfact\EventListener\OrderRoundPrice', 'RoundBasket'));
Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleOrderSaved', array('\Citfact\EventListener\OrderRoundPrice', 'RoundOrder'));


// округление суммы доставки
\Bitrix\Main\EventManager::getInstance()->addEventHandler('sale','OnBeforeSaleShipmentSetField','roundPriceInit');
function roundPriceInit(\Bitrix\Main\Event $event)
{
    $name = $event->getParameter('NAME');
    $value = $event->getParameter('VALUE');
    if ($name == 'PRICE_DELIVERY' || $name == 'BASE_PRICE_DELIVERY')
    {
        $value = round($value);
        $event->addResult(
            new Main\EventResult(
                Main\EventResult::SUCCESS, array('VALUE' => $value)
            )
        );
    }
}





// После изменения заказа сохраняем состав заказа в сессию
AddEventHandler("sale", "OnOrderUpdate", "OnOrderUpdateHandler");
function OnOrderUpdateHandler($ORDER_ID, $arFields)
{
    CModule::IncludeModule("catalog");
    CModule::IncludeModule("sale");

    $orderLogger = new \Citfact\Order\OrderLogger();
    $orderLogger->logForOnBeforeOrderUpdate($ORDER_ID, $arFields);

    $arBasketItemsIds = array();
    $dbBasketItems = CSaleBasket::GetList(
        array(
            "ID" => "ASC"
        ),
        array(
            "ORDER_ID" => $ORDER_ID
        ),
        false,
        false,
        array('ID')
    );
    while ($arItem = $dbBasketItems->Fetch()) {
        $arBasketItemsIds[] = $arItem['ID'];
    }

    if (isset($_SESSION['ORDER_ITEMS_' . $ORDER_ID]) && !empty($_SESSION['ORDER_ITEMS_' . $ORDER_ID])) {
        if ($arBasketItemsIds == $_SESSION['ORDER_ITEMS_' . $ORDER_ID]) {

        } else {
            // Получаем инфу о заказе
            /*$arFilter = Array("ID" => $ORDER_ID);
            $db_sales = CSaleOrder::GetList(array(), $arFilter);
            if ($arOrder = $db_sales->Fetch())
            {
            }*/
            $arProps = array();
            $db_props = CSaleOrderPropsValue::GetOrderProps($ORDER_ID);
            while ($arProp = $db_props->Fetch()) {
                $arProps[$arProp['CODE']] = $arProp['VALUE'];
            }

            // Если массивы не равны, шлем уведомление об изменении состава
            // Отсылаем письмо с купоном другу
            $arEventFields = array(
                "ORDER_ID" => $ORDER_ID,
                "EMAIL" => $arProps['EMAIL'],
            );
            CEvent::Send("SALE_ORDER_ITEMS_CHANGED", 's1', $arEventFields);
        }
    }

    $_SESSION['ORDER_ITEMS_' . $ORDER_ID] = $arBasketItemsIds;
}


// При отсылке письма об отмене заказа: формирование купона и вставка в поля письма
AddEventHandler("sale", "OnOrderCancelSendEmail", "OnOrderCancelSendEmailHandler");
function OnOrderCancelSendEmailHandler($ORDER_ID, $eventName, &$arFields)
{
    if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog")) {
        require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/citfact.tools/constants.php");
        $arFilter = Array("ID" => $ORDER_ID);
        $db_sales = CSaleOrder::GetList(array(), $arFilter);
        if ($arOrder = $db_sales->Fetch()) {
            $USER_ID = $arOrder['USER_ID'];

            //Для купона генерируются сроки активности с сегодня до +30 дней
			$dateToday = date("d.m.Y");
            $dateAfter = mktime(0, 0, 0, date("m"), date("d")+30, date("Y"));
			$dateAfter = date("d.m.Y", $dateAfter);
            $dateStart = new \Bitrix\Main\Type\Date($dateToday);
            $dateExpire = new \Bitrix\Main\Type\Date($dateAfter);

            $COUPON = CatalogGenerateCoupon();
            $arCouponFields = array(
                "DISCOUNT_ID" => DISCOUNT_FOR_CANCEL_ID,
                "ACTIVE" => "Y",
                "TYPE" => 2,//1 - на одну позицию заказа, 2 - на один заказ, 4 - многоразовый
                "COUPON" => $COUPON,
                "DESCRIPTION" => "USER_ID=" . $USER_ID,
                "ACTIVE_FROM" => $dateStart,
                "ACTIVE_TO" => $dateExpire,
				"USER_ID" => $USER_ID
            );

            $result = DiscountCouponTable::add($arCouponFields);

            if ($result->isSuccess())
                $arFields['COUPON_NUMBER'] = $COUPON;
        }
    }
}

AddEventHandler("catalog", "OnGetDiscountByProduct", "DiscountByProduct");
function DiscountByProduct($productID = 0, $arUserGroups = array(), $renewal = "N", $arCatalogGroups = array(), $siteID = false, $arDiscountCoupons = false)
{
    $dbItems = CSaleBasket::GetList(
        array("ID" => "ASC"),
        array(
            "FUSER_ID" => CSaleBasket::GetBasketUserID(),
            "LID" => SITE_ID,
            "ORDER_ID" => "NULL",
            "PRODUCT_ID" => $productID
        ),
        false,
        false,
        array(
            "ID", "NAME", "PRODUCT_ID", "DELAY"
        )
    );
    while ($arItem = $dbItems->GetNext()) {
        if ($arItem['DELAY'] == "N") {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

AddEventHandler("catalog", "OnGenerateCoupon", "OnGenerateCouponHandler");
function OnGenerateCouponHandler()
{
    $coupon = randString(7, array("ABCDEFGHIJKLNMOPQRSTUVWXYZ"));
    return $coupon;
}

AddEventHandler('catalog', 'OnBeforeCatalogImport1C', 'OnBeforeCatalogImport1CHandler');

function OnBeforeCatalogImport1CHandler($arParams, $arFields)
{

	if (file_exists($arFields)) {


		$fileInfo = pathinfo($arFields);
		$newFileName = $_SERVER['DOCUMENT_ROOT'] . '/local/var/tmp/1с/' . $fileInfo['filename'] . '_' . time() . '.' . $fileInfo['extension'];
		if (file_exists($newFileName)) {
			unlink($newFileName);
		}

		copy($arFields, $newFileName);
	}

	Fact\UpdateStorage::save(array('NAME' => 'execute_file', 'VAL' => $arFields));
    Fact\State::getInstance()->purge();
    Fact\UpdateStorage::save(array('NAME' => 'start', 'VAL' => time()));
}

AddEventHandler('catalog', 'OnSuccessCatalogImport1C', 'OnSuccessCatalogImport1CHandler');


function OnSuccessCatalogImport1CHandler($arParams, $arFields)
{
    Fact\UpdateStorage::save(array('NAME' => 'end', 'VAL' => time()));
}


AddEventHandler("iblock", "OnBeforeIBlockSectionUpdate", Array("ExchangeHandlers", "OnBeforeIBlockSectionUpdateHandler"));
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("ExchangeHandlers", "OnBeforeIBlockElementUpdateHandler"));

class ExchangeHandlers
{
    function OnBeforeIBlockSectionUpdateHandler(&$arFields)
    {
        if (defined('EXCHANGE_1C') && EXCHANGE_1C && \Bitrix\Main\Config\Option::get('sale', '1C_LOCK_SECTIONS_ACTIVITY') == 'Y') {
            unset($arFields['ACTIVE']);
        }

        if (defined('EXCHANGE_1C') && EXCHANGE_1C && \Bitrix\Main\Config\Option::get('sale', '1C_LOCK_SECTIONS_SECTIONS') == 'Y') {
            unset($arFields['IBLOCK_SECTION_ID']);
        }
    }

    function OnBeforeIBlockElementUpdateHandler(&$arFields)
    {
        if (defined('EXCHANGE_1C') && EXCHANGE_1C && \Bitrix\Main\Config\Option::get('sale', '1C_LOCK_ELEMENT_ACTIVITY') == 'Y') {
            unset($arFields['ACTIVE']);
        }

        if (defined('EXCHANGE_1C') && EXCHANGE_1C && \Bitrix\Main\Config\Option::get('sale', '1C_LOCK_ELEMENT_SECTIONS') == 'Y') {
            unset($arFields['IBLOCK_SECTION']);
            unset($arFields['IBLOCK_SECTION_ID']);
        }
    }
}


// купон на скидку для людей оставивших отзыв
// создается (неаткивный) при создании отзыва
// при активации отзыва - отправляем купон
AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("PaoloconteReviewsProduct", "OnAfterReviewsProductAdd"));
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", Array("PaoloconteReviewsProduct", "OnBeforeReviewsProductUpdate"));
class PaoloconteReviewsProduct
{
	private static $couponCodeProp = 'COUPON';
	private static $userCodeID = 'USER_ID';
	private static $mailCodeID = 'USER_EMAIL';
	private static $typeSend = 'SEND_REVIEWS_COUPON';
	private static $idSyte = 's1';
	private static $discountID = COUPON_FOR_REVIEWS;

	private static function getPropId($code){
		$res = CIBlockProperty::GetByID($code, IBLOCK_PRODUCT_REVIEW);
		if($ar_res = $res->GetNext())
			return $ar_res['ID'];
		else
			return false;
	}

	private static function getValueProp($prop){
		$result = array();
		foreach ($prop as $val) {
			if (!empty($val['VALUE'])) {
				$result[] = $val['VALUE'];
			}
		}
		return $result;
	}

	private static function getCouponPropId(){
		return self::getPropId(self::$couponCodeProp);
	}

	private static function getMailCodeID(){
		return self::getPropId(self::$mailCodeID);
	}

	private static function getUserPropId(){
		return self::getPropId(self::$userCodeID);
	}

	private static function generateCoupon(){
		do
		{
			$coupon = randString(7, array("ABCDEFGHIJKLNMOPQRSTUVWXYZ"));
			$resManage = new DiscountCouponsManager();
			$existCoupon = $resManage->isExist($coupon);
			$resultCorrect = empty($existCoupon);
		} while (!$resultCorrect);
		return $coupon;
	}

	public static function OnAfterReviewsProductAdd(&$arFields)
    {
		if(
            $arFields['IBLOCK_ID']
            != IBLOCK_PRODUCT_REVIEW
            || !isset($arFields["ID"])
            || $arFields["ID"] == 0
            || \Bitrix\Main\Loader::includeModule('sale') == false
        )
			return;

		$thisCouponCodeProp = self::$couponCodeProp;
		$thisUserCodeID = self::$userCodeID;

		$resRev = CIBlockElement::GetList(Array("SORT"=>"ASC", "ID"=>"DESC"), Array("IBLOCK_ID" => IBLOCK_PRODUCT_REVIEW, "ID" => $arFields["ID"]), false, Array("nTopCount"=>1), Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_".$thisUserCodeID, "PROPERTY_".$thisCouponCodeProp,));
		if($obRev = $resRev->GetNextElement())
		{
			$arFieldsRev = $obRev->GetFields();
			if (empty($arFieldsRev["PROPERTY_".$thisCouponCodeProp."_VALUE"])) {
				$usId = $arFieldsRev["PROPERTY_".$thisUserCodeID."_VALUE"];
				$COUPON = self::generateCoupon();
				if (!empty($COUPON)) {
					$fieldsCoupon = array(
						"DISCOUNT_ID" => self::$discountID,
						"ACTIVE"  => "N",
						"COUPON"  => $COUPON,
						"USER_ID"  => $usId, // Владелец купона
						"TYPE"   => 2,
						"DESCRIPTION"   => 'REVIEWS_ID='.$arFields['ID'].' USER_ID='.$usId,
					);
					$result = DiscountCouponTable::add($fieldsCoupon);
					if ($result->isSuccess()) {
						CIBlockElement::SetPropertyValuesEx($arFields['ID'], false, array($thisCouponCodeProp => $COUPON));
					}
				}
			}
		}
	}

    public static function OnBeforeReviewsProductUpdate(&$arFields)
    {
		if (
            $arFields['IBLOCK_ID'] != IBLOCK_PRODUCT_REVIEW
            || \Bitrix\Main\Loader::includeModule('sale') == false
        )
			return;

		$thisCouponCodeProp = self::$couponCodeProp;
		$thisUserCodeID = self::$userCodeID;
		$thisMailCodeID = self::$mailCodeID;

		$resRev = CIBlockElement::GetList(Array("SORT"=>"ASC", "ID"=>"DESC"), Array("IBLOCK_ID" => IBLOCK_PRODUCT_REVIEW, "ID" => $arFields["ID"]), false, Array("nTopCount"=>1), Array("ID", "ACTIVE", "IBLOCK_ID", "NAME", "PROPERTY_".$thisUserCodeID, "PROPERTY_".$thisCouponCodeProp, "PROPERTY_".$thisMailCodeID,));
		if($obRev = $resRev->GetNextElement())
		{
			$arFieldsRev = $obRev->GetFields();
			if ($arFields['ACTIVE'] == 'Y' && $arFieldsRev['ACTIVE'] == 'N') {
				$dt = new DateTime();
				$usId = $arFieldsRev["PROPERTY_".$thisUserCodeID."_VALUE"];
				$coupon = $arFieldsRev["PROPERTY_".$thisCouponCodeProp."_VALUE"];
				$mailValue = $arFieldsRev["PROPERTY_".$thisMailCodeID."_VALUE"];
				$couponIterator = DiscountCouponTable::getList(array(
					'select' => array('ID', 'COUPON'),
					'filter' => array(
						'ACTIVE' => 'N',
						'COUPON' => $coupon,
						array(
							'LOGIC' => 'OR',
							array(
								'<=ACTIVE_FROM' => $dt,
								'>=ACTIVE_TO' => $dt,
							),
							array(
								'ACTIVE_FROM' => false,
								'ACTIVE_TO' => false,
							),
						),
					)
				));
				if ($arCoupon = $couponIterator->fetch()){
					$result = DiscountCouponTable::update($arCoupon['ID'], array('ACTIVE' => 'Y'));
					if ($result->isSuccess()) {
						$arEventFields = array(
							"COUPON" => $coupon,
							"EMAIL" => $mailValue,
						);
						CEvent::Send(self::$typeSend, self::$idSyte, $arEventFields);
					}
				}
			}
		}
    }
}


// ОБРАБОТЧИК РАСЧЕТА РЕЙТИНГА ТОВАРОВ
include_once('include/calc_product_rating.php');
// ОБРАБОТЧИК ВЫГРУЗКИ 1С, УСТАНОВКА ЦЕН В ТОВАР ИЗ ТП
include_once('include/set_product_price.php');

// ДОБАВЛЕНИЕ В МЕНЮ ПУНКТА PUSH-УВЕДОМЛЕНИЯ
AddEventHandler("main", "OnBuildGlobalMenu", Array("PushClass", "OnBuildGlobalMenu"));

class PushClass
{
    function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
    {
        foreach ($aModuleMenu as $k => $v) {
            if ($v['parent_menu'] == 'global_menu_settings' && $v['page_icon'] == "fav_page_icon") {
                $aModuleMenu[$k]['items'][] = Array(
                    'text' => "Push-уведомления",
                    'title' => "Push-уведомления",
                    'url' => "/paoloconte_app/push_create.php",
                    'more_url' => array('push_create.php')
                );
            }
        }

        $aModuleMenu[] = [
            'parent_menu' => 'global_menu_citfact',
            'section' => 'citfact_tools_region_settings',
            'sort' => 200,
            'text' => 'Настройки обмена с 1с',
            'title' => 'Настройки обмена с 1с',
            'url' => '1c_settings.php',
            'items_id' => 'menu_citfact_1c_settings',
        ];
        $aModuleMenu[] = [
            'parent_menu' => 'global_menu_citfact',
            'section' => 'citfact_tools_region_settings',
            'sort' => 300,
            'text' => 'Настройки отправки SMS сообщений',
            'title' => 'Настройки отправки SMS сообщений при заказе',
            'url' => 'sms_settings.php',
            'items_id' => 'menu_citfact_1c_settings',
        ];
        $aModuleMenu[] = [
            'parent_menu' => 'global_menu_citfact',
            'section' => 'citfact_tools_region_settings',
            'sort' => 300,
            'text' => 'Настройки экспорта каталога',
            'title' => 'Настройки экспорта каталога',
            'url' => 'catalog_export.php',
            'items_id' => 'menu_citfact_1c_settings',
        ];
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/sms_settings.php')){
            $file = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/local/modules/citfact.tools/install/admin/sms_settings.php');
            if ($file){
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/sms_settings.php', $file);
            }
        }
        $aModuleMenu[] = [
            'parent_menu' => 'global_menu_citfact',
            'section' => 'citfact_tools_region_settings',
            'sort' => 300,
            'text' => 'Настройка словарей Sphinx',
            'title' => 'Настройка словарей Sphinx',
            'url' => 'citfact_sphinx_dictionary.php',
            'items_id' => 'menu_citfact_1c_settings',
        ];
    }
}

AddEventHandler("sale", "OnSaleStatusOrder", Array("OrderStatus", "onSaleStatusOrderHandler"));
class OrderStatus
{
    function onSaleStatusOrderHandler($id, $val)
    {
        if ($val != 'N') {
            self::pushMessage($id, $val);
        }
    }

    function pushMessage($id, $val)
    {
        $arOrder = CSaleOrder::GetByID($id);
        $arStatus = CSaleStatus::GetByID($val);

        $arStatus_opis = $arStatus["DESCRIPTION"];
        $arStatus = $arStatus["NAME"];

        $arEventFields = array(
            "ORDER_ID" => 'Номер заказа: ' . $id,
            "ORDER_STATUS" => 'Статус заказа: ' . $arStatus,
            "ORDER_DESCRIPTION" => 'Описание статуса: ' . $arStatus_opis,
        );

        $arMessages[] = array(
            "USER_ID" => $arOrder['USER_ID'],
            "TITLE" => 'Paoloconte',
            "APP_ID" => 'ru.paoloconte.app',
            "MESSAGE" => implode("; ", $arEventFields),
        );

        $pushMe = new CPushManager();
        $pushMe->SendMessage($arMessages);
    }
}

AddEventHandler('main', 'OnBeforeEventSend', Array('EventSendListener', "orderStatusS"));
class EventSendListener
{
    function orderStatusS(&$arFields, &$arTemplate)
    {
        if ($arTemplate['EVENT_NAME'] == 'SALE_STATUS_CHANGED_S') {
            $arFields['ORDER_LINK'] = $_SERVER['HTTP_ORIGIN'] . '/cabinet/orders/?ID=' . $arFields['ORDER_ID'];
        }
    }
}

function dump($var, $die = false, $restart = false)
{
    if ($restart){
      global $APPLICATION;
      $APPLICATION->RestartBuffer();
    }

    echo '<pre>';
    print_r($var);
    echo '</pre>';

    if ($die) die;
}


/**
 * Функция вывода массива
 *
 * @param array $var массив, который необходимо вывести
 * @param boolean $all выводить для всех на печать (по умолчанию выводит для адмиистраторов)
 * @param boolean $hide спрятать методом display:none
 **/
function pre($var, $all=false, $hide=false){
    global $USER;
    if($USER->IsAdmin()||$all){
        $trace = debug_backtrace();
        $arPre = array('file'=>$trace[0]['file'],'line'=>$trace[0]['line']);
        $pre = '<pre id="tester_id_tuta" style="'.(($hide)?'display:none;':'/*display:none;*/').'">'.print_r($var, true).'</pre>';
        $pre .= '<pre id="tester_id_tuta_file" style="display:none;">'.print_r($arPre, true).'</pre>';
        echo $pre;
    }
}


/**
 * Функция логирования
 * по умолчанию печатает в "/local/logs/printLogs.log"
 * обазательно добавьте файл .htaccess deny from all
 *
 * @param array $arFields массив, который необходимо записать в лог
 * @param string $namePrintFileLog куда печатать. Можно передать название, тогда по умолчанию будет печатать в /local/logs/
 **/
function printLogs($arFields, $namePrintFileLog = "printLogs.log"){
    $defaultFileDir = '/local/var/logs';
    $arDirFile = explode('/', $namePrintFileLog);
    if (count($arDirFile) > 1) {
        $fileName = array_pop($arDirFile);
        $dirFile = implode('/', $arDirFile);
    }else{
        $dirFile = $defaultFileDir;
        $fileName = $namePrintFileLog;
    }
    $trace = debug_backtrace();
    $date = date("Y-m-d H:i:s");
    $file = str_replace($_SERVER["DOCUMENT_ROOT"], '', $trace[0]['file']);
    $arInfo = array('file'=>$file,'line'=>$trace[0]['line'], 'date'=>$date);
    mkdir($_SERVER["DOCUMENT_ROOT"].$dirFile, 0775, true); // создаем директорию если ее нет, т.к. file_put_contents не делает этого
    file_put_contents($_SERVER["DOCUMENT_ROOT"].'/'.$dirFile.'/'.$fileName, print_r(array("PRINT_R" => $arFields, "INFO" => $arInfo), true), FILE_APPEND);
}



// Проверка юзера в черном списке.

function checkUserInBlackList(CUser $USER)
{
    $res = CUser::GetUserGroupList($USER->GetID());
    $temp = array();
    while ($r = $res->Fetch()) {
        $temp[] = $r['GROUP_ID'];
    }
    if (in_array(BLACK_LIST_GROUP, $temp)) {
        return true;
    }
    return false;
}

function print2Log($ID, $val, $recurringID, $arAdditionalFields)
{

    file_put_contents(
        $_SERVER['DOCUMENT_ROOT'] . '/local/logs/print2Log',
        print_r(
            array(
                '$ID' => $ID,
                '$val' => $val,
                '$recurringID' => $recurringID,
                '$arAdditionalFields' => $arAdditionalFields
            ), true
        ),
        FILE_APPEND
    );
}

function getCatalogFilter($params = array())
{
    $idPrice = ((int)$params['PRICE_ID'] > 0) ? $params['PRICE_ID'] : 2;
    $storeID = ((int)$params['STORE_ID'] > 0) ? $params['STORE_ID'] : 5;

    $arrFilter[] = array(
        'ACTIVE' => 'Y',
        'PROPERTY_HAS_PHOTO' => 'Y',
    );

    $arSubQuery = array(
        '=CATALOG_AVAILABLE' => "Y",
        ">CATALOG_STORE_AMOUNT_" . $storeID => "0",
    );

    $arrFilter[] = array(
        'LOGIC' => 'OR',
        array(
            'ID' => CIBlockElement::SubQuery('PROPERTY_CML2_LINK', $arSubQuery),
            '>CATALOG_PRICE_' . $idPrice => 0,
        ),
        array(
            "LOGIC" => "AND",
            array('OFFERS' => NULL),
            array(
                '=CATALOG_AVAILABLE' => "Y",
                ">CATALOG_STORE_AMOUNT_" . $storeID => 0,
                '>CATALOG_PRICE_' . $idPrice => 0,
            ),
        )
    );

    if ($params['CHECK_FILTER'] == 'Y') {
        $arOrder = array("SORT" => "ASC");
        $rsElements = CIBlockElement::GetList($arOrder, $arrFilter, FALSE, false);
        echo 'Товаров подходящих под фильтр: ' . $rsElements->AffectedRowsCount();
    }

    return $arrFilter;
}

function getCatalogFilterByType($params = array(), $types = array(), $sectionIds = array())
{
    $idPrice = ((int)$params['PRICE_ID'] > 0) ? $params['PRICE_ID'] : 2;
    $storeID = ((int)$params['STORE_ID'] > 0) ? $params['STORE_ID'] : 5;
    $arrFilter = [];

    $filterLogic = array(
        '=CATALOG_AVAILABLE' => 'Y',
    );

    if (in_array('PHOTO', $types)) {
        $arrFilter[] = array(
            'ACTIVE' => 'Y',
            'PROPERTY_HAS_PHOTO' => 'Y',
        );
    } else {
        $arrFilter[] = array(
            'ACTIVE' => 'Y',
        );
    }

    if (!empty($sectionIds)) {
        $arrFilter['SECTION_ID'] = $sectionIds;
    }

    if (in_array('LEFTOVERS', $types)) {
        $arSubQuery = array(
            '=CATALOG_AVAILABLE' => "Y",
            ">CATALOG_STORE_AMOUNT_" . $storeID => "0",
        );
        $filterLogic['>CATALOG_STORE_AMOUNT_' . $storeID] = '0';
    } else {
        $arSubQuery = array(
            '=CATALOG_AVAILABLE' => "Y",
        );
    }

    if (in_array('PRICE', $types)) {
        $filter1 = array(
            'ID' => CIBlockElement::SubQuery('PROPERTY_CML2_LINK', $arSubQuery),
            '>CATALOG_PRICE_' . $idPrice => 0,
        );
        $filterLogic['>CATALOG_PRICE_' . $idPrice] = '0';
    } else {
        $filter1 = array(
            'ID' => CIBlockElement::SubQuery('PROPERTY_CML2_LINK', $arSubQuery),
        );
    }

    $arrFilter[] = array(
        'LOGIC' => 'OR',
        $filter1,
        array(
            "LOGIC" => "AND",
            array('OFFERS' => NULL),
            $filterLogic,
        )
    );

    /*$arrFilter[] = array(
        'LOGIC' => 'OR',
        array(
            'ID' => CIBlockElement::SubQuery('PROPERTY_CML2_LINK', $arSubQuery),
            '>CATALOG_PRICE_' . $idPrice => 0,
        ),
        array(
            "LOGIC" => "AND",
            array('OFFERS' => NULL),
            array(
                '=CATALOG_AVAILABLE' => "Y",
                ">CATALOG_STORE_AMOUNT_" . $storeID => 0,
                '>CATALOG_PRICE_' . $idPrice => 0,
            ),
        )
    );*/

    if ($params['CHECK_FILTER'] == 'Y') {
        $arOrder = array("SORT" => "ASC");
        $rsElements = CIBlockElement::GetList($arOrder, $arrFilter, FALSE, false);
        echo 'Товаров подходящих под фильтр: ' . $rsElements->AffectedRowsCount();
    }

    return $arrFilter;
}

function getCatalogFilterDop()
{
    $arrFilter[] = array(
        'ACTIVE' => 'Y',
    );

    $valueYes = '';
    $property_enums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>IBLOCK_CATALOG, "CODE"=>"FLAG_AVAILABILITY"));
    while($enum_fields = $property_enums->GetNext())
    {
        if ($enum_fields["XML_ID"] == '001') {
            $valueYes = $enum_fields["VALUE"];
        }
    }
    if (!empty($valueYes)) {
        $arrFilter[] = array(
            'PROPERTY_FLAG_AVAILABILITY_VALUE' => $valueYes,
        );
    }

    return $arrFilter;
}

function activateProductsByOffersActivity()
{
    global $USER;
    if ($USER->IsAdmin()) {
        set_time_limit(36000);

        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule('sale');
        define('CATALOG_ID', 10);
        define("SKU_ID", 11);

        $rsGetItems = CIBlockElement::GetList(
            array(),
            array(
                "IBLOCK_ID" => CATALOG_ID,
                "ACTIVE" => "N"
            ),
            false,
            false,
            array(
                "ID",
                "NAME"
            )
        );

        while ($obGetItems = $rsGetItems->GetNext()) {

            $offers_db = CIBlockElement::GetList(
                array(),
                array(
                    "IBLOCK_ID" => SKU_ID,
                    "PROPERTY_CML2_LINK" => $obGetItems['ID'],
                    'ACTIVE' => 'Y'
                ),
                false,
                false,
                array(
                    "ID",
                    "NAME",
                    "CODE"
                )
            );
            if ($offers_db->AffectedRowsCount()) {
                $el = new CIBlockElement;

                $arLoadProductArray = array("ACTIVE" => "Y");

                if ($res = $el->Update($obGetItems['ID'], $arLoadProductArray))
                    echo "Update element: " . $obGetItems['NAME'] . ' - ' . $obGetItems['ID'] . '<br>' . "\r\n";
                else
                    echo "Error Update element: " . $obGetItems['NAME'] . ' - ' . $obGetItems['ID'] . ' - ' . $el->LAST_ERROR . '<br>';
            }
        }
        echo 'Activation finished.';
    }
}


function activateSku($params = array())
{
    $idPrice = ((int)$params['PRICE_ID'] > 0) ? $params['PRICE_ID'] : 2;
    $storeID = ((int)$params['STORE_ID'] > 0) ? $params['STORE_ID'] : 5;
    global $USER;
    if ($USER->IsAdmin()) {
        set_time_limit(36000);

        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule('sale');
        define('CATALOG_ID', 10);
        define("SKU_ID", 11);


        $rsGetItems = CIBlockElement::GetList(
            array(),
            array(
                "IBLOCK_ID" => SKU_ID,
                "ACTIVE" => "N",
                '=CATALOG_AVAILABLE' => "Y",
                ">CATALOG_STORE_AMOUNT_" . $storeID => "0",
                '>CATALOG_PRICE_' . $idPrice => 0
            ),
            false,
            false,
            array(
                "ID",
                "NAME"
            )
        );


        while ($obGetItems = $rsGetItems->GetNext()) {
            $el = new CIBlockElement;
            $arLoadProductArray = array("ACTIVE" => "Y");

            if ($res = $el->Update($obGetItems['ID'], $arLoadProductArray))
                echo "Update element: " . $obGetItems['NAME'] . ' - ' . $obGetItems['ID'] . '<br>' . "\r\n";
            else
                echo "Error Update element: " . $obGetItems['NAME'] . ' - ' . $obGetItems['ID'] . ' - ' . $el->LAST_ERROR . '<br>';
        }
    }
}

function jdump($var, $checkPermission = true)
{
    global $USER;
    $access = true;
    if ($checkPermission) {
        $access = $USER->IsAdmin();
    }
    if ($access) { ?>
        <script> if (typeof debug === 'undefined') {
                Debug = function () {
                    this.items = [];
                    this.add = function (info) {
                        this.items.push(info);
                        this.marker();
                    };
                    this.marker = function () {
                        var cont = document.getElementById('print_r_container');
                        if (this.items.length > 0) {
                            for (var i = 0; i < this.items.length; i++) {
                                var item = document.createElement('div');
                                item.setAttribute('itemId', i);
                                item.style.border = '1px solid black';
                                item.innerHTML = 'Файл: ' + this.items[i].call[0].file + '<br> Строка: ' + this.items[i].call[0].line + '<br>';
                                item.addEventListener('click', function () {
                                    var id = this.getAttribute('itemid');
                                    console.log(debug.items[id]);
                                });
                                cont.appendChild(item);
                            }
                        }
                    };
                    this.showContainer = function () {
                        var a = document.getElementById('print_r_container');
                        if (a.style.display == 'none') {
                            a.style.display = 'block';
                        } else {
                            a.style.display = 'none';
                        }
                    };
                    this.printItem = function (id) {
                        console.log(this.items[id]);
                    };
                    this.printAll = function () {
                        console.log(debug.items);
                    };
                    var container = document.createElement('div');
                    container.id = 'print_r_container';
                    container.style.display = 'none';
                    container.style.position = 'fixed';
                    container.style.left = '50px';
                    container.style.bottom = '0px';
                    container.style.background = '#6abc68';
                    container.style.float = 'left';
                    container.style.border = '1px solid #000000';
                    container.style.overflow = 'scroll';
                    container.style.width = '800px';
                    container.style.height = '600px';
                    container.style.zIndex = '9999';
                    container.style.color = 'black';
                    document.body.appendChild(container);
                    var div = document.createElement('div');
                    div.style.zIndex = 9999;
                    div.style.position = 'fixed';
                    div.style.width = '50px';
                    div.style.height = '50px';
                    div.style.left = '0';
                    div.style.bottom = '0';
                    div.style.background = 'red';
                    div.id = 'print_r';
                    div.addEventListener('click', this.showContainer);
                    document.body.appendChild(div);
                };
                var debug = new Debug();
            }
            debug.add(<?=json_encode(array('call' => debug_backtrace(false,1), 'var' => $var))?>); </script>
    <? }
}

function get_translit($name)
{
    $arParams = array("replace_space" => "-", "replace_other" => "-");
    $trans = Cutil::translit($name, "ru", $arParams);
    return $trans;
}


// Активируем скидку по карте, если установлена галочка "Активность дисконтной карты"
AddEventHandler("main", "OnBeforeUserUpdate", "OnBeforeUserUpdateHandler");
function OnBeforeUserUpdateHandler(&$arFields)
{
	if (!$arFields["UF_USE_LOYALTY_CARD"]) {
		return;
	}

	global $APPLICATION;
	if ($arFields["UF_USE_LOYALTY_CARD"] && !$arFields["UF_LOYALTY_CARD"]) {
		$APPLICATION->throwException("Не привязана дисконтная карта (либо уберите галочку \"Активность дисконтной карты\" либо укажите номер карты).");
		return false;
	}
	if ($arFields["UF_USE_LOYALTY_CARD"] && !$arFields["UF_CARD_DISCOUNT"]) {
		$APPLICATION->throwException("Не указана скидка (либо уберите галочку \"Активность дисконтной карты\" либо укажите процент скидки).");
		return false;
	}

	$arFilter = array(
		"UF_LOYALTY_CARD" => $arFields["UF_LOYALTY_CARD"],
		"UF_USE_LOYALTY_CARD" => 1,
		//"!ID" => $arFields["ID"]
	);
	$dbUsers = CUser::GetList(($by="ID"), ($order="asc"), $arFilter);
	if ($arUser = $dbUsers->Fetch()) {
        if ($arUser["ID"] == $arFields["ID"]) {
            return;
        }
		$APPLICATION->throwException("Уже есть пользователь с такой активной картой (ID = ".$arUser["ID"].")");
		return false;
	}

	try {
		\Citfact\Paolo::CreateCoupon($arFields["UF_LOYALTY_CARD"], $arFields["UF_CARD_DISCOUNT"], $arFields["ID"]);
	} catch (Exception $e) {
		if ($e instanceof \Citfact\IncorrectLoyaltyCardException) {
            $APPLICATION->throwException("Некорректный номер дисконтной карты (должен состоять из 13 цифр)");
        } elseif ($e instanceof \Citfact\SaleDiscountCreateCouponException) {
            $APPLICATION->throwException("Ошибка при создании купона по дисконтной карте\n".$e);
        } else {
            $APPLICATION->throwException("Неизвестная ошибка при создании купона по дисконтной карте\n".$e);
        }
        return false;
	}

    // Отправляем сообщение об активации карты
    $arEventFields = array(
        "CARD_NUMBER"   => $arFields["UF_LOYALTY_CARD"],
        "DISCOUNT"      => $arFields["UF_CARD_DISCOUNT"],
        "EMAIL"         => $arFields["EMAIL"],
    );
    CEvent::Send("LOYALTY_CARD_ACTIVATED", "s1", $arEventFields);
}


function randstr( $length, $chars = null ){
	if( $chars == null ){
		$chars = 'abcdefghijklmnoprstuvxyzABCDEFGHIJKLMNOPRSTUVXYZ0123456789';
	}
	else {
		$chars = (string) $chars;
	}

	mt_srand();
	$str = '';
	for( $c = 0; $c < $length; $c++ ) {
		$str .= $chars[ mt_rand(0, mb_strlen($chars) - 1) ];
	}

	return $str;

}

function generate_code(){

	global $DB;

	$code = randstr( 7, 'ABCDEFGHIJKLMNOPRSTUVXYZ' );

	$sql = 'SELECT * FROM b_sale_discount_coupon WHERE COUPON = "' . $DB->ForSql( $code ) . '"';

	$records = $DB->Query( $sql );
	$record = $records->Fetch();

	if($record !== false ){
		$code = generate_code();
	}

	return $code;

}

Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleComponentOrderOneStepFinal', 'MyOnSaleComponentOrderOneStepFinal');
function MyOnSaleComponentOrderOneStepFinal($ID, $arOrder, $arParams) {
    LoyaltyLogger::log('OnSaleComponentOrderOneStepFinal. OrderId: ' . $ID);
    $originalDiscount = DataLoyalty::getInstance()->getOriginalDiscount();
    $originalPrices = DataLoyalty::getInstance()->getOriginalPrices();


    /*проверяем user есть ли у него телефон*/
    $phoneThisUser='';
    $arsUser = CUser::GetList($by="id",$order="asc", array('ID'=>$arOrder['USER_ID']), array("SELECT"=>array("UF_LOYALTY_CARD")));
    while ($arUser = $arsUser->Fetch()) {
        $phoneThisUser = $arUser['PERSONAL_PHONE'];
        $idCard = $arUser['UF_LOYALTY_CARD'];
    }

    if(empty($phoneThisUser)) {
        /*если нет, то смотрим его заказ и берем из него телефон*/
        $arsSales = CSaleOrderPropsValue::GetOrderProps($ID);
        while ($arSales = $arsSales->Fetch()) {
            $thisSaleData[$arSales['CODE']] = $arSales;
        }
        $fields = Array(
            "PERSONAL_PHONE" => $thisSaleData['PHONE']['VALUE'],
        );
        $phoneThisUser = $thisSaleData['PHONE']['VALUE'];
    }
    if(empty($idCard)){
        if(!empty($thisSaleData['LOYALTY_CARD']['VALUE'])){
            $fields['UF_LOYALTY_CARD'] = str_replace('-','', $thisSaleData['LOYALTY_CARD']['VALUE']);
        }
        else{
            $cardNumber = Events::getCardIdForPhone($phoneThisUser);
            if(empty($cardNumber)){
                $fields['UF_LOYALTY_CARD'] = OperationManager::generateCardNumber();
            }else{
                $fields['UF_LOYALTY_CARD'] = $cardNumber;
            }

        }
    }

    if(!empty($fields)){
        $thisUser = new CUser;
        $thisUser->Update($arOrder['USER_ID'], $fields);
    }

    //получаем скидку по промокоду
    $promoCodeDiscount = OperationManager::getPromoCodeDiscount();
    //если есть скидка по промокоду и промокод еще не применен
    if ($promoCodeDiscount) {
        $order = \Bitrix\Sale\Order::load($arOrder["ID"]);
        $basket = $order->getBasket();
        $totalPrice = 0;
        foreach ($basket as $bitem) {
            if ($bitem->getField("DELAY") == "Y" || $bitem->getField("CAN_BUY") == "N") {
                continue;
            }
            if (!in_array($bitem->getField("ID"), $_SESSION["CL_CART_BASKET_IDS"])) {
                continue;
            }
            $totalPrice += ($bitem->getField("PRICE") * $bitem->getField("QUANTITY"));
        }
        $arNewPrice = Citfact\Tools::getNewPrice($basket, $totalPrice, $promoCodeDiscount);
        foreach ($basket as $bitem) {
            if (!in_array($bitem->getField("ID"), $_SESSION["CL_CART_BASKET_IDS"])) {
                continue;
            }
            $bitem->setField('CUSTOM_PRICE', "Y");
            $bitem->setField('PRICE', $arNewPrice[$bitem->getProductId()]['PRICE']);
        }
        $basket->save();
        $paymentCollection = $order->getPaymentCollection();
        foreach ($paymentCollection as $payment) {
            $payment->setField('SUM', $order->getPrice());
            break;
        }
        $order->doFinalAction(true);
        $order->save();
        OperationManager::setPromoCodeDiscount(0);
        OperationManager::deletePromoCodes();
    }
    //Обработка заказа при частичной оплате бонусами Cloud Loyalty
    LoyaltyLogger::log(DataLoyalty::getInstance()->getDataAll(), 'DataLoyalty');
    if (DataLoyalty::getInstance()->getUseCloudScore() == "Y")
    {
        LoyaltyLogger::log('start using cloud score');
        LoyaltyLogger::log($originalPrices, '$originalPrices');
        LoyaltyLogger::log($originalDiscount, '$originalDiscount');

        $bonusValue = intval(DataLoyalty::getInstance()->getCloudScoreApplied());
        $log[$ID]['BONUS_VALUE'] = $bonusValue;
        if ($bonusValue > 0 && strlen(DataLoyalty::getInstance()->getCardId()) > 0)
        {
            LoyaltyLogger::log('Бонусы больше нуля и номер карты есть');
            $isNeedCloudLoyaltyPayment = 'N';
            $order = \Bitrix\Sale\Order::load($arOrder["ID"]); //Обновляем свойство заказа "Заказ будет оплачен бонусами"
            $propertyCollection = $order->getPropertyCollection();
            foreach ($propertyCollection as $propertyItem)
            {
                $prop = $propertyItem->getField("CODE");
                if ($prop == "NEED_CLOUD_LOYALTY_PAYMENT")
                {
                    LoyaltyLogger::log('Set NEED_CLOUD_LOYALTY_PAYMENT(Заказ будет оплачен бонусами): Y'
                    . ' OrderId: ' . $ID);
                    $isNeedCloudLoyaltyPayment = $propertyItem->getValue();
                    $propertyItem->setValue("Y");
                }
            }
            if ($isNeedCloudLoyaltyPayment != 'Y') {
                LoyaltyLogger::log('NEED_CLOUD_LOYALTY_PAYMENT(Заказ будет оплачен бонусами) не установлен');
                //Пересчитываем заказ
                LoyaltyLogger::log('set order rebuild');

                $basket = $order->getBasket();
                $orderClDiscount = 0;
                $basketPrices = [];
                // В этом цикле сначала собираем цены, которые изменились от примененного купона
                foreach ($basket as $bitem) {
                    $itemProdId = $bitem->getProductId();
                    if (intval($originalPrices[$itemProdId]) > 0 && intval($originalDiscount[$itemProdId]) > 0) {
                        $basketPrices[$itemProdId] = $bitem->getPrice();
                        $log[$ID]['PRICE'][$itemProdId] = $basketPrices;
                    }
                }
                // В этом цикле потом отнимаем бонусы от цен
                $oldPriceSumBasketRound = 0;
                $oldPriceSumBasketNotRound = 0;
                $last_bitem = false;
                $last_clPrice = 0;
                foreach ($basket as $k_bitem => $bitem) {
                    $itemProdId = $bitem->getProductId();
                    $itemProdQuantity = $bitem->getQuantity();

                    if (intval($originalPrices[$itemProdId]) > 0 && intval($originalDiscount[$itemProdId]) > 0) {
                        $orderClDiscount += intval($originalDiscount[$itemProdId]);

                        $clPriceQuantity = intval($basketPrices[$itemProdId]) * $itemProdQuantity - intval($originalDiscount[$itemProdId]);
                        $clPriceNotRound = $clPriceQuantity / $itemProdQuantity;
                        $oldPriceSumBasketNotRound += $clPriceNotRound;
                        $clPrice = round($clPriceNotRound);
                        $oldPriceSumBasketRound += $clPrice;

                        $bitem->setField('CUSTOM_PRICE', "Y");
                        $bitem->setField('PRICE', $clPrice);

                        $last_bitem = $k_bitem;
                        $last_clPrice = $clPrice;

                        $log[$ID][$itemProdId]['ORDER_CL_PRICE'] = $orderClDiscount;
                        $log[$ID][$itemProdId]['BASKET_PRICE'] = $basketPrices[$itemProdId];
                        $log[$ID][$itemProdId]['CL_PRICE'] = $clPrice;
                    }
                }

                if ($last_bitem) {
                    $oldPriceSumBasketNotRound = ceil($oldPriceSumBasketNotRound);
                    $diffPrice = $oldPriceSumBasketNotRound - $oldPriceSumBasketRound;
                    $basket[$last_bitem]->setField('PRICE', $last_clPrice + $diffPrice);
                }

                $basket->save();
                LoyaltyLogger::log('basket saved');

                $paymentCollection = $order->getPaymentCollection();
                foreach ($paymentCollection as $payment) {
                    $payment->setField('SUM', $order->getPrice());
                    break;
                }
                $order->doFinalAction(true);
                LoyaltyLogger::log('order final action done');
                $order->save();
                LoyaltyLogger::log('order saved');
                LoyaltyLogger::log($log);
            }
        }
        DataLoyalty::getInstance()->clearBonusData(); //Обнуляем данные по возможной сумме списания бонусов
        DataLoyalty::getInstance()->setUseCloudScore("N"); //Отмена опции "Оплатить баллами Cloud Payment"
        DataLoyalty::getInstance()->setCloudScoreApplied(0);
        DataLoyalty::getInstance()->deleteOriginalPrices();
        DataLoyalty::getInstance()->deleteOriginalDiscount();
        //если скидка по промокоду, то применяем и удаляем промокод
        if ($promoCodeDiscount) {
            OperationManager::setPromoCodeDiscount(0);
            OperationManager::deletePromoCodes();
        }
    }
};

AddEventHandler("main", "OnEpilog", "Events404");
function Events404() {
    if(defined('ERROR_404') && ERROR_404=='Y' && !defined('ADMIN_SECTION')) {
        global $APPLICATION;
        if (strpos($APPLICATION->GetCurDir(), '/events/') !== false) {

        }
    }
}

Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleOrderSaved', 'OnSaleOrderSavedHandler');
function OnSaleOrderSavedHandler(Main\Event $event) {
    $order = $event->getParameter("ENTITY");
    $isNew = $event->getParameter("IS_NEW");

    if ($isNew) {
        $emailProp = $order->getPropertyCollection()->getUserEmail();
        $emailProp->setValue(str_replace(' ', '', $emailProp->getValue()));
    }

    $aQsiConf = [];
    include __DIR__ . "/aqsi/init.php";
    if (!empty($aQsiConf) && $aQsiConf["condition"]["STATUS_ID"] == $order->getField("STATUS_ID") && in_array((int) $order->getField("DELIVERY_ID"), $aQsiConf["condition"]["DELIVERY_ID"])) {
        global $aQsiUserGroupId;
        $aQsiUserGroupId = $aQsiConf["clients"]["groupId"];
        $aQsi = new Citfact\Aqsi\Orders\Create(include __DIR__ . "/aqsi/aqsi_key.php");
        $aQsi->setShop($aQsiConf["shop"]);
        $aQsi->add($order->getId());
        $aQsiResult = $aQsi->save();
        AddMessage2Log(print_r(["STATUS_ID" => $order->getField("STATUS_ID"), "DELIVERY_ID" => $order->getField("DELIVERY_ID"), "aQsiResult" => $aQsiResult], true));
    }
}

Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleShipmentEntitySaved', 'onSaleShipmentEntitySavedHandler');
function onSaleShipmentEntitySavedHandler(Main\Event $event) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/local/modules/sberbank.ecom/payment/payment_completion.php';
}

//AddEventHandler("main", "OnBeforeUserAdd", Array("\Citfact\CloudLoyalty\Events", "OnBeforeUserAddHandler"));
AddEventHandler("main", "OnAfterUserAdd", Array("\Citfact\CloudLoyalty\Events", "OnAfterUserAddHandler"));
AddEventHandler("main", "OnAfterUserUpdate", Array("\Citfact\CloudLoyalty\Events", "OnAfterUserUpdateHandler"));
AddEventHandler("sale", "OnSaleStatusOrder", Array("\Citfact\CloudLoyalty\Events", "OnSaleOrderFinalStatus"));

$eventManager->addEventHandler("sale", "onSaleCancelOrder", Array("\Citfact\CloudLoyalty\Events", "onSaleCancelOrder"));

$eventManager->addEventHandler('sale', 'OnSaleOrderBeforeSaved', ['\Citfact\CloudLoyalty\Events','addCloudLoyaltyPaySystemIfNeeded']);
$eventManager->addEventHandler("sale", "OnSaleOrderBeforeSaved", "OnSaleOrderBeforeSaved");
$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', ['\Citfact\CloudLoyalty\Events','setOrderAndBuyback']);
$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', ['\Citfact\CloudLoyalty\Events','payCloudLoyaltyPayment']);
//$eventManager->addEventHandler('sale', 'OnSaleBasketSaved', ['\Citfact\CloudLoyalty\Events','registrationUsersInCloudLoyalty']);
\Bitrix\Main\EventManager::getInstance()->addEventHandler("sale", "OnSaleStatusOrderChange", ['\Citfact\Smsc\Events','OnSaleStatusOrderSendSms']);
\Bitrix\Main\EventManager::getInstance()->addEventHandler("main", "OnAdminTabControlBegin", ['\Citfact\Smsc\Events','OnAdminOrderPage']);

AddEventHandler('catalog', 'OnSuccessCatalogImport1C', 'customCatalogImportStep');
function customCatalogImportStep()
{
    try {
        $productAvailabilityService = new ProductAvailability();
        $productAvailabilityService->setAvailabilityProductsExec('customCatalogImportStep');
    } catch (Exception $e) {
    }
}
// определение мобильного устройства
function checkMobileDevice() {
    $mobile_agent_array =
        array(
                'ipad', 'iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'cellphone', 'opera mobi',
            'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola',
            'smartphone', 'blackberry', 'playstation portable', 'tablet browser'
        );
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    foreach ($mobile_agent_array as $value) {
        if (strpos($agent, $value) !== false) return true;
    }
    return false;
}


//Перед сохранением заказа
function OnSaleOrderBeforeSaved(Bitrix\Main\Event $event){
    $request = Main\Application::getInstance()->getContext()->getRequest();
    if ($request->getPost("DELIVERY_ID") == ID_DELIVERY_STORE)
    {
        $storeId = (int)$request->getPost("delivery_store");
        if (!empty($storeId))
        {
            $idShops = \Citfact\Tools::getIdIblock(CODE_IBLOCK_SHOPS);
            $shops = CIBlockElement::GetList(false, array("ID"=> $storeId,"IBLOCK_ID"=>$idShops,"ACTIVE"=>"Y"),false,false,array("ID","PROPERTY_ADDRESS","","PROPERTY_STORE_ID","NAME"));
            while($shop = $shops->Fetch()){
                $parameters = $event->getParameters();
                $order = $parameters['ENTITY'];
                $propertyCollection = $order->getPropertyCollection();
                $idLocationOrder =  \Citfact\Tools::getIdPropertyOrder(CODE_PIKUP_ADDRESS);
                $propertyLocation = $propertyCollection->getItemByOrderPropertyId($idLocationOrder);
                $propertyLocation->setValue($shop["NAME"] ." - ".$shop["PROPERTY_ADDRESS_VALUE"]);
                if (!empty($shop["PROPERTY_STORE_ID_VALUE"])){
                    //Извлекаем внешний код и наименование склада для добавления в служебные свойства заказа
                    $listStore = CCatalogStore::GetList(
                        array(),
                        array('ID'=>$shop["PROPERTY_STORE_ID_VALUE"]),
                        false,
                        false,
                        array("XML_ID","TITLE")
                    );
                    while($storeElement = $listStore->fetch()){
                        $idExternalCodeStore =  \Citfact\Tools::getIdPropertyOrder(CODE_EXTERNAL_CODE_STORE);
                        $propertyExternalCodeStore = $propertyCollection->getItemByOrderPropertyId($idExternalCodeStore);
                        $propertyExternalCodeStore->setValue($storeElement["XML_ID"]);
                        $idNameStore =  \Citfact\Tools::getIdPropertyOrder(CODE_NAME_STORE);
                        $propertyNameStore = $propertyCollection->getItemByOrderPropertyId($idNameStore);
                        $propertyNameStore->setValue($storeElement["TITLE"]);
                    }
                    $shipments = $order->getShipmentCollection();
                    foreach($shipments as $shipment){
                        $shipment->setStoreId($shop["PROPERTY_STORE_ID_VALUE"]);
                    }
                }
            }
        }
    }
 }

// перед регистрацией нового пользователя начисляем ему 300 бонусных баллов CL
AddEventHandler('main', 'OnBeforeUserAdd', 'OnBeforeUserRegister');
function OnBeforeUserRegister(&$arFields)
{

    $isCardEmpty = false;

    if (empty($arFields["UF_LOYALTY_CARD"]))
    {
        $isCardEmpty = true;
        $arFields["UF_LOYALTY_CARD"] = OperationManager::generateCardNumber();
        Operation::log('newClCardGenerated: ' . $arFields["UF_LOYALTY_CARD"] . ' ' . date('Y-m-d H:i:s'));
    }

    if (empty($arFields['PERSONAL_PHONE'])){
        return;
    }

    $clUserCard = Events::checkUserInCloudloyaltyByPhone($arFields['PERSONAL_PHONE'], true);

    //Пользователь не существует
    if ($clUserCard === false)
    {
        $operationName = Operation::OPERATION_NEW_CLIENT;

        $bdate = '';
        if (!empty($arFields['PERSONAL_BIRTHDAY']))
        {
            $dateTime = new DateTime($arFields['PERSONAL_BIRTHDAY']);
            $timeZone = new \DateTimeZone('UTC');
            $dateTime->setTimeZone($timeZone);
            $bdate = $dateTime->format(\DateTime::RFC3339);
        }

        if (empty($arFields['NAME'])){
            $arFields['NAME'] = "";
        }
        if (empty($arFields['SECOND_NAME'])){
            $arFields['SECOND_NAME'] = "";
        }

        $parameters = [
            'client' => [
                'phoneNumber' => $arFields['PERSONAL_PHONE'],
                'email' => $arFields['EMAIL'],
                'name' => $arFields['NAME'],
                'surname' => $arFields['LAST_NAME'],
                'patronymicName' => $arFields['SECOND_NAME'],
                'fullName' => $arFields['NAME'] . ' ' . $arFields['SECOND_NAME'] . ' ' . $arFields['SECOND_NAME'],
                'card' => $arFields['UF_LOYALTY_CARD'],
                'extraFields' => ['noWelcomeBonus' => 0]
            ],
        ];
        if ($bdate){
            $parameters['client']['birthdate'] = $bdate;
        }
        $operation = new Operation($parameters, $operationName);
        Operation::log('registrationUsersInCloudLoyaltyOnRegister ' . date('Y-m-d H:i:s'));
        $operation->send();

    }
    //Пользователь существует, но карта не заполнена, обновляем информацию о пользователе
    else if (!$clUserCard)
    {
        $operationName = Operation::OPERATION_UPDATE_CLIENT;

        $parameters = [
            'phoneNumber' => $arFields['PERSONAL_PHONE'],
            'client' => [
                'card' => $arFields['UF_LOYALTY_CARD']
            ],
        ];
        $operation = new Operation($parameters, $operationName);
        Operation::log('OnAfterUserUpdateHandler ' . date('Y-m-d H:i:s'));
        $operation->send();
    }
    //Пользователь существует, с заполенной картой
    else
    {
        //Проверяем, совпадает ли карта в форме с картой реальной
        if ($clUserCard != $arFields['UF_LOYALTY_CARD'])
        {
            //Выводим сообщение о том, что карты не совпадают
            echo '<script>alert("К телефону привязан другой номер карты");</script>';
        }
    }

    return;
}

AddEventHandler('main', 'OnEndBufferContent', 'ValidateHTML');
function ValidateHTML(&$content)
{
    $content = str_replace(" type=\"text/javascript\"", '', $content);
    $content = str_replace(" type='text/javascript'", '', $content);
    $content = str_replace(" type='\"text/css\"", '', $content);
    $content = str_replace(" type='text/css'", '', $content);
}


/**
 * Обработчики событий разделов инфоблока.
 */
AddEventHandler("iblock", "OnAfterIBlockSectionAdd", ["SectionHandlers", "onAfterAdd"]);
AddEventHandler("iblock", "OnAfterIBlockSectionDelete", ["SectionHandlers", "onAfterDelete"]);

class SectionHandlers
{
    // ID инфоблока "Основной каталог товаров"
    protected static $сatalogIblockID = 10;

    function onAfterAdd(&$arFields)
    {
        switch ($arFields['IBLOCK_ID']) {
            case self::$сatalogIblockID: // обрабатываем инфоблок "Основной каталог товаров"
                self::addVariantInPropertySection($arFields); // Добавить значение в свойство "раздел" - SECTION
                break;
        }
    }

    // создаем обработчик события "OnAfterIBlockSectionDelete"
    function onAfterDelete(&$arFields)
    {
        switch ($arFields['IBLOCK_ID']) {
            case self::$сatalogIblockID: // обрабатываем инфоблок "Основной каталог товаров"
                self::deleteVariantPropertySection($arFields); // Удалить значение из свойства "раздел" - SECTION.
                break;
        }
    }

    /**
     * Добавить значение в свойство "раздел" - SECTION
     * @param $arFields
     */
    private static function addVariantInPropertySection($arFields)
    {
        $xmlID = 'iblock-' . self::$сatalogIblockID . '-section-' . $arFields['ID'];

        // Получить id свойства SECTION
        $arFilter = [
            'IBLOCK_ID' => self::$сatalogIblockID,
            'CODE' => 'SECTION',
        ];
        $rsProperty = \CIBlockProperty::GetList([], $arFilter);
        $propID = 0;
        while ($prop = $rsProperty->Fetch()) {
            $propID = $prop['ID'];
        }

        if ($propID !== 0) {
            $arFieldsNewValue = [
                'PROPERTY_ID' => $propID,
                'VALUE' => $arFields['NAME'],
                'XML_ID' => $xmlID,
            ];

            $propEnum = new \CIBlockPropertyEnum;
            $propEnum->Add($arFieldsNewValue);
        }
    }

    /**
     * Удалить значение из свойства "раздел" - SECTION.
     * @param $arFields
     */
    private static function deleteVariantPropertySection($arFields)
    {
        $xmlID = 'iblock-' . self::$сatalogIblockID . '-section-' . $arFields['ID'];
        $property_enums = \CIBlockPropertyEnum::GetList(["DEF" => "DESC", "SORT" => "ASC"], ["IBLOCK_ID" => self::$сatalogIblockID, "XML_ID" => $xmlID]);
        while ($enum_field = $property_enums->GetNext()) {
            \CIBlockPropertyEnum::delete($enum_field["ID"]);
        }
    }
}

/**
 * Обработчики событий элементов инфоблока.
 */
AddEventHandler("iblock", "OnAfterIBlockElementAdd", ["IblockElementsHandlers", "OnAfterProductAdd"]);
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", ["IblockElementsHandlers", "OnAfterProductUpdate"]);
class IblockElementsHandlers
{
    // ID инфоблока "Основной каталог товаров"
    protected static $сatalogIblockID = 10;

    function OnAfterProductAdd(&$arFields)
    {
        switch ($arFields['IBLOCK_ID']) {
            case self::$сatalogIblockID: // обрабатываем инфоблок "Основной каталог товаров"
                self::setVariantPropertySection($arFields); // Установить значение свойства "раздел" - SECTION, в соответствии с категориями.
                break;
        }
    }

    function OnAfterProductUpdate(&$arFields)
    {
        switch ($arFields['IBLOCK_ID']) {
            case self::$сatalogIblockID: // обрабатываем инфоблок "Основной каталог товаров"
                self::setVariantPropertySection($arFields); // Установить значение свойства "раздел" - SECTION, в соответствии с категориями.
                break;
        }
    }

    /**
     * При добавлении элемента
     * @param $arFields
     */
    private static function setVariantPropertySection(&$arFields)
    {
        $arSections = $arFields['IBLOCK_SECTION'];
        $propertyValues = $arFields['PROPERTY_VALUES'];

        // Получаем секции
        if (isset($arFields['IBLOCK_SECTION'])) { // Если через админку
            $arSections = $arFields['IBLOCK_SECTION'];
        } else { // Если через api
            $db_groups = \CIBlockElement::GetElementGroups($arFields['ID'], true);
            while($ar_group = $db_groups->Fetch()) {
                $arSections[] = $ar_group["ID"];
            }
        }

        // Получить id свойства SECTION
        $arFilter = [
            'IBLOCK_ID' => self::$сatalogIblockID,
            'CODE' => 'SECTION',
        ];
        $rsProperty = \CIBlockProperty::GetList([], $arFilter);
        $propID = 0;
        while ($prop = $rsProperty->Fetch()) {
            $propID = $prop['ID'];
        }

        $xmlIDs = [];
        foreach ($arSections as $sectionID) {
            $xmlIDs[] = 'iblock-' . self::$сatalogIblockID . '-section-' . $sectionID;
        }

        $propertyValues[$propID] = []; // Сбрасывает предыдущие значения чтобы нельзя было проставить из редактирования поля.

        // Заполняем новые значения.
        $property_enums = \CIBlockPropertyEnum::GetList(["DEF" => "DESC", "SORT" => "ASC"], ["IBLOCK_ID" => self::$сatalogIblockID, "XML_ID" => $xmlIDs]);
        while ($enum_field = $property_enums->GetNext()) {
            $propertyValues[$propID][] = ['VALUE' => $enum_field['ID']];
        }

        \CIBlockElement::SetPropertyValuesEx($arFields['ID'], self::$сatalogIblockID, $propertyValues);
    }
}

Main\EventManager::getInstance()->addEventHandler('main', 'OnAdminSaleOrderViewDraggable', array('\Citfact\EventListener\OrderDraggable', "onInit"));

//Main\EventManager::getInstance()->addEventHandler('sale', 'OnSaleBasketBeforeSaved', 'onSaleBasketBeforeSaved');
//
//function onSaleBasketBeforeSaved(Main\Event $event)
//{
//    static $call = false;
//    if ($call) {
//        return;
//    }
//    $call = true;
//    $basket = $event->getParameter('ENTITY');
//    foreach ($basket as $basketItem) {
//        $quantity = $basketItem->getQuantity();
//        if ($quantity > 1) {
//            $basketItem->setField('QUANTITY', 1);
//        }
//    }
//    $basket->save();
//}

Main\EventManager::getInstance()->addEventHandler('iblock', 'OnAfterIBlockElementUpdate', array('\Citfact\EventListener\SearchIndexSubscriber', "updateSearchIndex"));
Main\EventManager::getInstance()->addEventHandler('search', 'OnBeforeFullReindexClear', array('\Citfact\EventListener\SearchIndexSubscriber', "deleteRestartedSphinx"));
Main\EventManager::getInstance()->addEventHandler('search', 'BeforeIndex', array('\Citfact\EventListener\SearchIndexSubscriber', "addArticle"));