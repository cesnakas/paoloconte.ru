<?
$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;
require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');

if(!Loader::IncludeModule("iblock"))
{
    ShowError("IBLOCK_MODULE_NOT_INSTALLED");
    return;
}
if(!Loader::IncludeModule("sale") || !Loader::IncludeModule("catalog"))
{
    ShowError("SALE_MODULE_NOT_INSTALLED");
    return;
}
global $USER;
global $DB;


$arOrder = array("ID" => "ASC");
$arFilter = array('IBLOCK_ID' => IBLOCK_CATALOG, 'ACTIVE' => 'Y', 'SECTION_GLOBAL_ACTIVE' => 'Y');
$arSelectFields = array("ID", "ACTIVE", "NAME", 'CATALOG_GROUP_'.PRICE_ID_MOSCOW, 'CATALOG_GROUP_'.PRICE_ID_MOSCOW_ACTION,
    'PROPERTY_BOKS_DLINA_MARKETING', 'PROPERTY_BOKS_SHIRINA_MARKETING', 'PROPERTY_BOKS_VYSOTA_MARKETING',
    'PROPERTY_CML2_ARTICLE',
);
$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
while($arElement = $rsElements->GetNext()) {
    //echo "<pre style=\"display:block;\">"; print_r($arElement); echo "</pre>";
    if ($arElement['CATALOG_PRICE_'.PRICE_ID_MOSCOW] > 0) {
        $arDiscounts = CCatalogDiscount::GetDiscountByProduct(
            $arElement['ID'],
            2,                    // Группа всех пользователей
            "N",
            PRICE_ID_MOSCOW,
            SITE_ID
        );
        if ($arElement['CATALOG_PRICE_'.PRICE_ID_MOSCOW_ACTION] > 0){
            $price = $discount_price = $arElement['CATALOG_PRICE_'.PRICE_ID_MOSCOW_ACTION];
        }
        else{
            $price = $discount_price = $arElement['CATALOG_PRICE_'.PRICE_ID_MOSCOW];
        }
        if (!empty($arDiscounts)) {
            foreach ($arDiscounts as $arDiscount) {
                if ($arDiscount["VALUE_TYPE"] == "F") {
                    $discount_price = ($price - $arDiscount["VALUE"]);
                }
                if ($arDiscount["VALUE_TYPE"] == "P") {
                    $discount_price = ($price - ($price/100 * $arDiscount["VALUE"]));
                }
            }
        }
        //echo "<pre style=\"display:block;\">"; print_r($discount_price); echo "</pre>";
        CIBlockElement::SetPropertyValuesEx($arElement['ID'], false, array('PRICE_PROP' => $discount_price));
    }


    // Заполняем свойство наличия фотографий
    // Формируем основные пути фотографий
    $catalog_img_path = CATALOG_IMG.$arElement['PROPERTY_CML2_ARTICLE_VALUE'].'/';
    $catalog_img_photo_path = CATALOG_IMG.$arElement['PROPERTY_CML2_ARTICLE_VALUE'].CATALOG_IMG_PHOTO;
    $has_photo = 'Y';

    //если не существуют корневой каталог с фото к товару, то ни че делать не будем
    //если не существуют каталог с конкретными фото к товару, то ни че делать не будем
    if( !file_exists($_SERVER['DOCUMENT_ROOT'].$catalog_img_path) || !file_exists($_SERVER['DOCUMENT_ROOT'].$catalog_img_photo_path) ){
        $has_photo = 'N';
    }

    CIBlockElement::SetPropertyValuesEx($arElement['ID'], false, array('HAS_PHOTO' => $has_photo));


    // Заполняем габариты у торговых предложений и наличие ТП у родительского товара + артикул у ТП
    $arFilterSKU = array('IBLOCK_ID' => IBLOCK_SKU, /*'ACTIVE' => 'Y',*/ 'PROPERTY_CML2_LINK' => $arElement['ID']);
    $rsElementsSKU = CIBlockElement::GetList(array(), $arFilterSKU, FALSE, FALSE, array('ID', 'IBLOCK_ID', 'ACTIVE'));
    $mult = 10;
    $arFields = array(
        "LENGTH" => $arElement['PROPERTY_BOKS_DLINA_MARKETING_VALUE'] * $mult,
        "WIDTH" => $arElement['PROPERTY_BOKS_SHIRINA_MARKETING_VALUE'] * $mult,
        "HEIGHT" => $arElement['PROPERTY_BOKS_VYSOTA_MARKETING_VALUE'] * $mult,
    );
    $arIdsOffers = array();
    $hasOffers = false;
    while ($arSKU = $rsElementsSKU->GetNext()) {
        $arFields['ID'] = $arSKU['ID'];
        if (CCatalogProduct::Add($arFields))
        {
            //echo "Добавили параметры товара к элементу каталога " . $arSKU['ID'] . '<br>';
        }
        else
        {
            //echo 'Ошибка добавления параметров<br>';
        }

        // Заполняем артикул
        CIBlockElement::SetPropertyValuesEx($arSKU['ID'], false, array('CML2_ARTICLE' => $arElement['PROPERTY_CML2_ARTICLE_VALUE']));

        if ($arSKU['ACTIVE'] == 'Y') {
            $arIdsOffers[] = $arSKU['ID'];
        }
        $hasOffers = true;
    }

    if (
        empty($arIdsOffers) // Если не нашли активных ТП, ставим ID самого товара
        && !$hasOffers
    ){
        $arIdsOffers[] = $arElement['ID'];
    }

    $offers_amount = 0;
    if (!empty($arIdsOffers)) {
        $rsStore = CCatalogStoreProduct::GetList(array(), array('PRODUCT_ID' => $arIdsOffers, 'STORE_ID' => 5), false, false, array());
        while ($arStore = $rsStore->Fetch()) {
            if ($arStore['AMOUNT'] > 0) {
                $offers_amount += $arStore['AMOUNT'];
            }
        }
    }

//    pre(array($arElement['ID'], $hasOffers, $offers_amount));

    CIBlockElement::SetPropertyValuesEx($arElement['ID'], false, array('OFFERS_AMOUNT' => $offers_amount));
}
