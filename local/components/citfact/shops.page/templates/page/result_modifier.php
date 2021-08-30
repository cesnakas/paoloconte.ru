<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

foreach ($arResult['SHOPS_CURRENT'] as &$arShop) {
    $tmp = [];
    foreach ($arShop["PROPERTY_IMAGES_VALUE"] as $key => $file) {
        $file = CFile::ResizeImageGet($arShop["PROPERTY_IMAGES_VALUE"][0], array('width' => 180, 'height' => 190), BX_RESIZE_IMAGE_EXACT, true);
        $img = '<img src="' . $file['src'] . '" alt="' . $arShop['NAME'] . '" title="' . $arShop['NAME'] . '" />';
        $tmp[$key] = $img;
    }
    $arShop["PROPERTY_IMAGES_VALUE"] = $tmp;
}
unset($arShop);
