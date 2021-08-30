<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arBlocksIds = array(
	797 => 'top',
	798 => 'bottom'
);

$cur_page = $APPLICATION->GetCurPage(true);
$cur_page_no_index = $APPLICATION->GetCurPage(false);

$arResult['BLOCKS']['top'] = $arResult['BLOCKS']['bottom'] = array();
$arResult['BLOCKS']['about'] = array();
$arResult['BLOCKS']['help'] = array();

foreach($arResult['ITEMS'] as &$arItem){
	if ($arParams['AUTH'] == 'Y' && $arItem['PROPERTY_SHOW_NOT_AUTH_VALUE'] == ''
		|| $arParams['AUTH'] == 'N' && $arItem['PROPERTY_SHOW_NOT_AUTH_VALUE'] != ''
	) {
		$arItem['SELECTED'] = CMenu::IsItemSelected($arItem['PROPERTY_LINK_VALUE'], $cur_page, $cur_page_no_index) == true ? 'SELECTED' : '';
		$arResult['BLOCKS'][$arBlocksIds[$arItem['PROPERTY_BLOCK_ENUM_ID']]][] = $arItem;
	}
    if ($arParams['FILTER']['SECTION_CODE'] == 'about') {
        $arItem['SELECTED'] = ($arItem['PROPERTY_LINK_VALUE'] == $APPLICATION->GetCurPage(false)) ? 'SELECTED' : '';
        if ($arItem['SELECTED'] == '') {
            $arItem['SELECTED'] = (($arItem['PROPERTY_LINK_VALUE'] == '/events/') && (strpos($APPLICATION->GetCurPage(false), '/events/') !== false)) ? 'SELECTED' : '';
        }
        $arResult['BLOCKS'][$arParams['FILTER']['SECTION_CODE']][] = $arItem;
    }
    if ($arParams['FILTER']['SECTION_CODE'] == 'help') {
        $arItem['SELECTED'] = ($arItem['PROPERTY_LINK_VALUE'] == $APPLICATION->GetCurPage(false)) ? 'SELECTED' : '';
        $arResult['BLOCKS'][$arParams['FILTER']['SECTION_CODE']][] = $arItem;
    }
}
