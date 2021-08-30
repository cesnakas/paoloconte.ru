<?php

use Citfact\CloudLoyalty\DataLoyalty;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


if (!empty($arResult['ITEMS'])) {
    $arEmptyPreview = false;
    $strEmptyPreview = $this->GetFolder() . '/images/no_photo.png';
    $arResult['NOPHOTO'] = $strEmptyPreview;
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strEmptyPreview)) {
        $arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'] . $strEmptyPreview);
        if (!empty($arSizes)) {
            $arEmptyPreview = array(
                'SRC' => $strEmptyPreview,
                'WIDTH' => intval($arSizes[0]),
                'HEIGHT' => intval($arSizes[1])
            );
        }
        unset($arSizes);
    }
    unset($strEmptyPreview);
}

/*ПОДГОТОВКА КАРТИНОК*/
$imageConfig = array(
    'TYPE' => 'ONE',
    'SIZE' => array(
        'SMALL' => array('W' => 225, 'H' => 300)
    )
);


// Проверяем наличие размеров на складе интернет-магазина
$arIdsMain = array();
$arIdsOffers = array();
$arIdsOffersAll = array();
$arResult['OFFERS'] = array();
$arResult['OFFERS_AMOUNT'] = array();
$arResult['SIZES_AMOUNT'] = array();
$arResult['ARTICLES'] = $productToArticle = $offerToArticle = [];



foreach ($arResult["GRID"]["ROWS"] as $key => $arItem) {
    $arIdsOffers[$key] = $arItem['PRODUCT_ID'];
}

$arCatalogInfo = array();
$res = CIBlockElement::GetList(array(), array('ID' => $arIdsOffers), false, array('nTopCount' => count($arIdsOffers)), array("ID", "IBLOCK_ID", "NAME", "PROPERTY_CML2_ARTICLE", "PROPERTY_CML2_LINK"));
while ($ob = $res->GetNext()) {
    $arCatalogInfo[$ob['ID']] = $ob;
}


foreach ($arResult["GRID"]["ROWS"] as $key => $arItem) {
    $arResult["GRID"]["ROWS"][$key]['CATALOG'] = $arCatalogInfo[$arItem['PRODUCT_ID']];
    $articul = trim($arCatalogInfo[$arItem['PRODUCT_ID']]['PROPERTY_CML2_ARTICLE_VALUE']);
    $arResult["GRID"]["ROWS"][$key]['CATALOG_PHOTO'] = \Citfact\Paolo::getProductImage($articul, $imageConfig);

    if ($arCatalogInfo[$arItem['PRODUCT_ID']]['PROPERTY_CML2_LINK_VALUE'] != '') {
        $arIdsMain[] = $arCatalogInfo[$arItem['PRODUCT_ID']]['PROPERTY_CML2_LINK_VALUE'];
        $arIdsOffers[] = $arItem['PRODUCT_ID'];
    }
}

if (!empty($arIdsOffers)) {
    // Смотрим наличие торговых предложений в складе «Интернет-магазин»
    // Достаем все предложения для основных товаров
    if (!empty($arIdsMain)) {
        $productToArticle =
        $arFilter = array('IBLOCK_ID' => IBLOCK_CATALOG, 'ID' => $arIdsMain);
        $arSelectFields = array("ID", "ACTIVE", 'IBLOCK_ID', 'PROPERTY_CML2_ARTICLE');
        $rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
        while ($arElement = $rsElements->GetNext()) {
            $productToArticle[$arElement['ID']] = $arElement['PROPERTY_CML2_ARTICLE_VALUE'];
        }

        $arOrder = array();
        $arFilter = array('IBLOCK_ID' => IBLOCK_SKU, 'ACTIVE' => 'Y', 'PROPERTY_CML2_LINK' => $arIdsMain);
        $arSelectFields = array("ID", "ACTIVE", 'PROPERTY_CML2_LINK', 'PROPERTY_RAZMER');
        $rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
        while ($arOffer = $rsElements->GetNext()) {
            $arResult['OFFERS'] [$arOffer['PROPERTY_CML2_LINK_VALUE']] [$arOffer['PROPERTY_RAZMER_VALUE']] = $arOffer;
            $arIdsOffersAll[] = $arOffer['ID'];
            $offerToArticle[$arOffer['ID']] = $productToArticle[$arOffer['PROPERTY_CML2_LINK_VALUE']];
        }
        $arResult['ARTICLES'] = $offerToArticle;

    }

    // Проверяем наличие
    $rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' => $arIdsOffersAll, 'STORE_ID' => 5), false, false, array());
    while ($arStore = $rsStore->Fetch()) {
        if ($arStore['AMOUNT'] > 0) {
            $arResult['OFFERS_AMOUNT'] [$arStore['PRODUCT_ID']] = $arStore['AMOUNT'];
        }
    }

    // Заполняем итоговый массив
    $arTemp = array();
    foreach ($arResult['OFFERS'] as $arItem) {
        foreach ($arItem as $arOffer) {
            if (in_array($arResult['OFFERS_AMOUNT'][$arOffer['ID']], $arResult['OFFERS_AMOUNT'])) {
                $arTemp[$arOffer['PROPERTY_CML2_LINK_VALUE']] [$arOffer['PROPERTY_RAZMER_VALUE']] = $arResult['OFFERS_AMOUNT'] [$arOffer['ID']];
            }
        }
    }
    $arResult['SIZES_AMOUNT'] = $arTemp;
}




//Доставка из магазина (массив товаров отображаемых в корзине)

$_SESSION["GOODS_DELIVERY_STORE"] = array();

// Fix presentation of base price and discount


foreach ($arResult['GRID']['ROWS'] as &$item) {
    $basePrice = CPrice::GetBasePrice($item['PRODUCT_ID']);
    $item['FULL_PRICE'] = (float)$basePrice['PRICE'];
    $item['DISCOUNT_PRICE_PERCENT'] = round((($item['FULL_PRICE'] - $item['PRICE']) / $item['FULL_PRICE']) * 100);
    $item['DISCOUNT_PRICE_PERCENT_FORMATED'] = CSaleBasketHelper::formatQuantity($item['DISCOUNT_PRICE_PERCENT']) . '%';
    // Добавляем в массив id товаров для проверки наличия в компоненте ajax.order
    if ($item["DELAY"] == "N" && $item["CAN_BUY"] == "Y"){
        $_SESSION["GOODS_DELIVERY_STORE"][] = $item["PRODUCT_ID"];
    }

}

    /* Скидка при списании баллов Cloud Loyalty */
    //Исключаем из обработки Уход за обувью
    $arExcluded = [];
    $rs = \CIBlockSection::GetList(
        [],
        ['ID' => CLOYALITY_EXCLUDED_SECTIONS, 'IBLOCK_ID' => IBLOCK_CATALOG]
    );
    while ($ar = $rs->GetNext()) {
        $rs2 = \CIBlockSection::GetList(
            ['LEFT_MARGIN' => 'ASC'],
            [
                'IBLOCK_ID' => IBLOCK_CATALOG,
                '>LEFT_MARGIN' => $ar['LEFT_MARGIN'],
                '<RIGHT_MARGIN' => $ar['RIGHT_MARGIN']
            ]
        );

        while ($ar2 = $rs2->fetch()) {
            $arExcluded[] = $ar2["ID"];
        }
    }
    $_SESSION["cloyalty_excluded_sections"] = $arExcluded;

    $bonusData = \Citfact\CloudLoyalty\Events::calculatePurchase(false, DataLoyalty::getInstance()->getUseCloudScore());
    $promoCodeResponse = \Citfact\CloudLoyalty\Events::getPromoCodeResponse();
    if($promoCodeResponse !== false && !$promoCodeResponse['applied']) {
        $bonusData = \Citfact\CloudLoyalty\Events::calculatePurchase(false, DataLoyalty::getInstance()->getUseCloudScore());
    }
    //Максимальная величина примененной скидки равна 33%
//    if ($_SESSION["CL_CART_MAX_APPLY"] > 0 && $bonusData["maxToApplyForThisOrder"] > $_SESSION["CL_CART_MAX_APPLY"])
//    {
//        $bonusData["maxToApplyForThisOrder"] = $_SESSION["CL_CART_MAX_APPLY"];
//    }
    DataLoyalty::getInstance()->setBonusData($bonusData);
    if (intval($bonusData['maxToApplyForThisOrder']) > 0)
    {
        DataLoyalty::getInstance()->setCloudScoreApplied(intval($bonusData['maxToApplyForThisOrder']));
    }

    $fuser = \Bitrix\Sale\Fuser::getId();
    $basket = \Bitrix\Sale\Basket::loadItemsForFUser($fuser, SITE_ID);

    $promoCodeDiscount = 0;
    if ($promoCodeResponse) {
        $promoCodeDiscount = \Citfact\CloudLoyalty\OperationManager::getPromoCodeDiscount();
        if ($promoCodeDiscount) {
            $basketData = [];
            $totalPrice = 0;
            foreach ($basket as $bitem) {
                if ($bitem->getField("DELAY") == "Y" || $bitem->getField("CAN_BUY") == "N") {
                    continue;
                }

                if (!in_array($bitem->getField("ID"), $_SESSION["CL_CART_BASKET_IDS"])) {
                    continue;
                }

                $arBasketItem = array();
                $arBasketItem["PRODUCT_ID"] = $bitem->getField("PRODUCT_ID");
                $arBasketItem["BASKET_ITEM_ID"] = $bitem->getField("ID");
                $arBasketItem["QUANTITY"] = $bitem->getField("QUANTITY");
                $arBasketItem["PRICE"] = $bitem->getField("PRICE");
                $totalPrice += ($bitem->getField("PRICE") * $bitem->getField("QUANTITY"));
                $basketData["PRODUCTS"][$arBasketItem["PRODUCT_ID"]] = $arBasketItem;
            }

            $arNewPrice = Citfact\Tools::getNewPrice($basket, $totalPrice, $promoCodeDiscount);

            $basketData["PRICE_TOTAL"] = $totalPrice;
            $basketData["FUSER"] = $fuser;
            $basketData["CLOUD_PROMO_SCORE"] = intval($promoCodeDiscount);

            foreach ($basketData["PRODUCTS"] as $key => $product) {
                $basketData["PRODUCTS"][$product['PRODUCT_ID']]["CLOUD_PROMO"] = $product["PRICE"] - $arNewPrice[$product['PRODUCT_ID']]['PRICE'];
            }
            $_SESSION['ORIGINAL_DISCOUNT_PROMOCODE'] = $arNewPrice;
            $_SESSION['ORIGINAL_DISCOUNT_PROMOCODE_APPLY'] = $promoCodeDiscount;
            $arResult["BASKET_CLOUD_PROMO_DATA"] = $basketData;
            $arResult["PROMOCODE_DISCOUNT"] = $promoCodeDiscount;
        }
    }

if (DataLoyalty::getInstance()->getUseCloudScore() == "Y"){
    
    $basketData = array();

    foreach ($basket as $bitem)
    {               
        if ($bitem->getField("DELAY") == "Y" || $bitem->getField("CAN_BUY") == "N"){
            continue;
        }
        
        if (!in_array($bitem->getField("ID"), $_SESSION["CL_CART_BASKET_IDS"]))
        {
            continue;
        }

        $arBasketItem = array();
        $arBasketItem["PRODUCT_ID"]     = $bitem->getField("PRODUCT_ID");
        $arBasketItem["BASKET_ITEM_ID"] = $bitem->getField("ID");
        $arBasketItem["QUANTITY"]       = $bitem->getField("QUANTITY");
        $arBasketItem["PRICE"]          = $bitem->getField("PRICE");
        $basketData["PRODUCTS"][]       = $arBasketItem;
    }

    $totalPrice = 0;
    foreach ($basketData["PRODUCTS"] as $bitem)
    {
        $totalPrice += intval($bitem["PRICE"]);
    }
    $basketData["PRICE_TOTAL"] = $totalPrice;
    $basketData["FUSER"] = $fuser;
    $basketData["CLOUD_SCORE"] = intval($bonusData['maxToApplyForThisOrder']);

    $discountRatio = $basketData["CLOUD_SCORE"] / $totalPrice ;
    $discountRound = 0;
    foreach($basketData["PRODUCTS"] as $key=>$product)
    {
        $basketData["PRODUCTS"][$key]["CLOUD_DISCOUNT"] = floor($product["PRICE"] * $discountRatio / 10) * 10;
        $discountRound += $basketData["PRODUCTS"][$key]["CLOUD_DISCOUNT"];
    }
    $discountDelta = $basketData["CLOUD_SCORE"] - $discountRound;
    $basketData["PRODUCTS"][0]["CLOUD_DISCOUNT"] += $discountDelta;

    $arResult["BASKET_CLOUD_DATA"] = $basketData;
}
