<?php

/*
 * This file is part of the Studio Fact package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Sale\Internals;
use Bitrix\Main\Type\DateTime;

Loader::includeModule('iblock');
Loader::includeModule('catalog');

Loc::loadMessages(__FILE__);

$app = Application::getInstance();
$request = $app->getContext()->getRequest();

global $USER;
$user_id = $USER->GetID();

$dt = new DateTime();

$arFilterCoup = array(
	//'ACTIVE' => 'Y',
	'%DESCRIPTION' => 'USER_ID='.$user_id,
	/*array(
		'LOGIC' => 'OR',
		array(
                 '<=ACTIVE_FROM' => $dt,
			     '>=ACTIVE_TO' => $dt,
			),
			array(
                'ACTIVE_FROM' => false,
                'ACTIVE_TO' => false,
		),
	),    */
);
$arSelectCoup = array();
$recCoup = CCatalogDiscountCoupon::GetList(
	array(),
	$arFilterCoup,
	false,
	false,
	$arSelectCoup
);

$arResult['ITEMS'] = array();
$arDiscoutsIds = array();
while($coupon = $recCoup->GetNext()){
    //$coupon["STEP"] = "STEP 1";
	$coupon["COUPON_TYPE"] = "catalog";

	if ($coupon["ACTIVE_TO"]>0)
	{
    	$date_expire = $coupon["ACTIVE_TO"];    
    	if ($dt > $date_expire){    //--В случае просрочки купона, изменяем доступность
            if ($coupon["ACTIVE"] == "Y")
            {
                $coupon["ACTIVE"] = "X";   
            }
    	}
	}      
    
	$arResult['ITEMS'][] = $coupon;
	$arDiscoutsIds[] = $coupon['DISCOUNT_ID'];
}


// Достаем скидки
$dbProductDiscounts = CCatalogDiscount::GetList(
	array(),
	array(
		'ID' => $arDiscoutsIds,
	),
	false,
	false,
	array(

	)
);
$arResult['DISCOUNTS'] = array();
while ($arDiscount = $dbProductDiscounts->Fetch())
{
	$arResult['DISCOUNTS']["CATALOG"][$arDiscount['ID']] = $arDiscount;
}

// Купоны правил корзины
$getListCouponsParams = array(
	"select" => array("*"),   
	"filter"	=> array("USER_ID" => $user_id),
    "order" => Array("ACTIVE_TO"=>"DESC", "ACTIVE"=>"DESC"), 
);

$coupons_db = Internals\DiscountCouponTable::getList($getListCouponsParams);
$arSaleDiscoutsIds = array();
while ($coupon_res = $coupons_db->Fetch()) {
	$coupon_res["COUPON_TYPE"] = "sale";
    //--Добавляем проверку на просроченность купона, если просрочено, меняем активность
	if ($coupon_res["ACTIVE_TO"])
	{
		$date_expire = $coupon_res["ACTIVE_TO"];    
		if ($dt > $date_expire)
		{
            if ($coupon_res["ACTIVE"] == "Y")
            {
			     $coupon_res["ACTIVE"] = "X"; //Expired                
            }
		}
	}
    //$coupon_res["STEP"] = "STEP 2";
	$arResult['ITEMS'][] = $coupon_res;
	$arSaleDiscoutsIds[] = $coupon_res['DISCOUNT_ID'];
}


$getListSaleDiscountParams = array(
	"select" => array("*"),
	"filter"	=> array("ID" => $arSaleDiscoutsIds),
	"order"		=> array(),
);
$sale_discount_db = Internals\DiscountTable::getList($getListSaleDiscountParams);
while ($sale_discount = $sale_discount_db->Fetch()) {
	$arResult['DISCOUNTS']["SALE"][$sale_discount['ID']] = $sale_discount;
}

foreach ($arResult['ITEMS'] as $ID => $arItem) {
	if ($arItem["COUPON_TYPE"] == "catalog") {
		$val = (int)$arResult['DISCOUNTS']["CATALOG"][$arItem['DISCOUNT_ID']] ['VALUE'];
		if ($arResult['DISCOUNTS']["CATALOG"][$arItem['DISCOUNT_ID']] ['VALUE_TYPE'] == 'F'){
			$val .= ' рублей';
		}
		if ($arResult['DISCOUNTS']["CATALOG"][$arItem['DISCOUNT_ID']] ['VALUE_TYPE'] == 'P'){
			$val .= '%';
		}
		$arResult['ITEMS'][$ID]['VALUE'] = $val;
	} elseif ($arItem["COUPON_TYPE"] == "sale") {
               
        $discount_data = false;
        //--Прописываем только первое условие в правиле, если оно сложное
        foreach($arResult['DISCOUNTS']["SALE"][$arItem['DISCOUNT_ID']]['ACTIONS_LIST']["CHILDREN"] as $discItem)
        {
            $discount_data = $discItem["DATA"];
            break;
        }
        
		$val = (int)$discount_data["Value"];
		if ($discount_data["Unit"] == 'CurEach'){
			$val .= ' рублей за каждый товар';
		}
		if ($discount_data["Unit"] == 'CurAll'){
			$val .= ' рублей';
		}
		if ($discount_data["Unit"] == 'Perc'){
			$val .= '%';
		}
		$arResult['ITEMS'][$ID]['VALUE'] = $val;
	}
}

$couponIterator = Internals\DiscountCouponTable::getList(array(
	"select" => array("*"),     
	'filter' => array('%=DESCRIPTION' => '%USER_ID='.$user_id.'%',       ),
    "order" => Array("ACTIVE_TO"=>"DESC", "ACTIVE"=>"DESC"),        //--Добавлена сортировка по сроку завершения    
));
$arCouponsSale = array();
$arDiscountIds = array();
while ($coupon = $couponIterator->fetch()){
    if ($coupon["USER_ID"] != 0 && $coupon["USER_ID"] != $user_id) continue;
    
    //--Проверка на дубликаты
    foreach($arResult['ITEMS'] as $arItem){
        if ($arItem["COUPON"] == $coupon["COUPON"]) continue 2; 
    }
    
	if ($coupon["ACTIVE_TO"]>0)
	{
    	$date_expire = $coupon["ACTIVE_TO"];    
    	if ($dt > $date_expire){    //--В случае просрочки купона, изменяем доступность
            if ($coupon["ACTIVE"] == "Y")
            {
                $coupon["ACTIVE"] = "X";   
            }
    	}
	}    
    //$coupon["STEP"] = "STEP 3";
	$arCouponsSale[] = $coupon;
	$arDiscountIds[] = $coupon['DISCOUNT_ID'];
}



/*
Для инфы о скидке правил работы с корзиной
*/

$discountIterator = Internals\DiscountTable::getList(array(
	'select' => array('*'),
	'filter' => array('ID' => $arDiscountIds)
));
$arDiscounts = array();
while ($discount = $discountIterator->fetch()){
	$arDiscounts[$discount['ID']] = $discount;
}


foreach ($arCouponsSale as $key => $coupon) {
	if (!empty($arDiscounts[$coupon['DISCOUNT_ID']])) {
        $discountType = $arDiscounts[$coupon['DISCOUNT_ID']]["SHORT_DESCRIPTION_STRUCTURE"]["VALUE_TYPE"];
        $discountVal = $arDiscounts[$coupon['DISCOUNT_ID']]["SHORT_DESCRIPTION_STRUCTURE"]["VALUE"];
        switch($discountType){
            case "P": $arCouponsSale[$key]['VALUE'] = $discountVal . "%"; break;
            case "S": $arCouponsSale[$key]['VALUE'] = $discountVal . " рублей"; break;
        }		
	}
}

foreach ($arCouponsSale as $arItem)
{
	array_push($arResult['ITEMS'], $arItem);
}

//--Делаем массив уникальным
$arResultClone["ITEMS"] = $arResult["ITEMS"];
foreach($arResult["ITEMS"] as $id => $item){
    $arResult["ITEMS"][$id]["DUPLICATE"] = false;
    foreach($arResultClone["ITEMS"] as $cloneID => $cloneItem){
        if ( $cloneID != $id &&  $item["COUPON"] == $cloneItem["COUPON"] )
        {
            $arResult["ITEMS"][$id]["DUPLICATE"] = true;
            continue 2;
        }
    }
}


//Пересобираем купоны в зависимости от статуса
$arCouponsY = array();
$arCouponsN = array();
$arCouponsX = array();


foreach ($arResult["ITEMS"] as $arItem)
{
    if ($arItem["DUPLICATE"])
    {
        continue;
    }
    switch($arItem["ACTIVE"])
    {
        case "Y": array_push( $arCouponsY, $arItem ); break;
        case "N": array_push( $arCouponsN, $arItem ); break;
        case "X": array_push( $arCouponsX, $arItem ); break;
    }
}

$arResult["ITEMS"] = array();

foreach( $arCouponsY as $item ){ array_push($arResult["ITEMS"], $item); }
foreach( $arCouponsX as $item ){ array_push($arResult["ITEMS"], $item); }
foreach( $arCouponsN as $item ){ array_push($arResult["ITEMS"], $item); }


$this->IncludeComponentTemplate();