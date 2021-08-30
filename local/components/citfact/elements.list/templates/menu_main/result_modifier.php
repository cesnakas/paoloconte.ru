<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$cur_page = $APPLICATION->GetCurPage(true);
$cur_page_no_index = $APPLICATION->GetCurPage(false);

//del
$arResult['page'] = [];
$arResult['page'][0] = $cur_page;
$arResult['page'][1] = $cur_page_no_index;
//del

$arSectionIds = array();
foreach ($arResult['ITEMS'] as $arItem) {
    if (!empty($arItem['PROPERTY_CATALOG_SECTION_VALUE'])) {
        $arSectionIds = array_merge($arSectionIds, $arItem['PROPERTY_CATALOG_SECTION_VALUE']);
    }
}

if (!empty($arSectionIds)) {

    // Ищем баннеры
    $arOrder = array();
    $arFilter = array('IBLOCK_ID' => 17, 'PROPERTY_CATALOG_SECTION' => $arSectionIds, 'ACTIVE' => 'Y');
    $arSelectFields = array("ID", "ACTIVE", "NAME", 'PROPERTY_CATALOG_SECTION', 'PROPERTY_IMAGE', 'PROPERTY_LINK');
    $rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE, FALSE, $arSelectFields);
    $arBanners = array();
    while ($arElement = $rsElements->GetNext()) {
        $arBanners[$arElement['PROPERTY_CATALOG_SECTION_VALUE']] = $arElement;
    }

    $arSections = array();
    $arFilter = array('IBLOCK_ID' => $arParams['IBLOCK_CATALOG_ID'], 'ID' => $arSectionIds);
    $rsParentSection = CIBlockSection::GetList(array('left_margin' => 'asc'), $arFilter);
    while ($arParentSection = $rsParentSection->GetNext()) {
        $parent_id = $arParentSection['ID'];
        $arSections[$parent_id]['NAME'] = $arParentSection['NAME'];
        $arSections[$parent_id]['URL'] = $arParentSection['SECTION_PAGE_URL'];
        $arSections[$parent_id]['BANNER'] = $arBanners[$parent_id];

        $arFilter = array(
            'IBLOCK_ID' => $arParentSection['IBLOCK_ID'],
            '>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'],
            '<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'],
            '>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL'],
            'GLOBAL_ACTIVE' => 'Y',
            'ACTIVE' => 'Y',
            '!NAME' => 'Группа не определена',
            'PROPERTY' => array('>OFFERS_AMOUNT' => 0)
        ); // выберет потомков без учета активности
        $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'), $arFilter, false, array('ID', 'NAME', 'SECTION_PAGE_URL'));
        $arSections[$parent_id]['COUNT'] = 0;


        while ($arSect = $rsSect->GetNext()) {
            $arTemp = array();
            $arTemp['NAME'] = $arSect['NAME'];
            $arTemp['CODE'] = $arSect['CODE'];
            $arTemp['URL'] = $arSect['SECTION_PAGE_URL'];
            $arTemp['SELECTED'] = CMenu::IsItemSelected(getHrefWithLastSectionCode($arSect['SECTION_PAGE_URL']), $cur_page, $cur_page_no_index) == true ? 'SELECTED' : '';
            $arSections[$parent_id]['SUBSECTIONS'][] = $arTemp;
            $arSections[$parent_id]['COUNT']++;

        }
    }
    $arResult['SECTIONS'] = $arSections;
}


$isSelectedFind = false; // если выбранный найден, то цикл завершится
foreach ($arResult['ITEMS'] as &$arItem) { // main цикл

    if(!$isSelectedFind) {

        if ($arItem['PROPERTY_LINK_VALUE'] != '') { // для События или Бесплатная доставка  (скорее всего сломается, если 'бесплатная доставка' будет проверяться позже чем 'события')
            $arItem['SELECTED'] = CMenu::IsItemSelected(getHrefWithLastSectionCode($arItem['PROPERTY_LINK_VALUE']), $cur_page, $cur_page_no_index) == true ? 'SELECTED' : '';
            if($arItem['SELECTED'] != "") {$isSelectedFind = true; break;} //выход из цикла main
        }

        if (!empty($arItem['PROPERTY_CATALOG_SECTION_VALUE'])) {

            foreach ($arItem['PROPERTY_CATALOG_SECTION_VALUE'] as $section_id) { // цикл 1

                foreach ($arResult['SECTIONS'][$section_id]['SUBSECTIONS'] as $arSubsect) { // цикл 2
                    if ($arItem['SELECTED'] == '') {
                        $arItem['SELECTED'] = $arSubsect['SELECTED'];
                        if($arItem['SELECTED'] != "") {$isSelectedFind = true; break;} // выход из цикла 2
                    }
                }
                if($isSelectedFind) break; // выход из цикла 1
            }

            if (empty($arItem['SELECTED'])) {
                foreach ($arItem['PROPERTY_CATALOG_SECTION_VALUE'] as $containingSection) { // цикл 3
                    $id = (int)$containingSection;
                    if (array_key_exists($id, $arResult['SECTIONS']) && empty($arItem['SELECTED'])) {
                        $arItem['SELECTED'] = CMenu::IsItemSelected(getHrefWithLastSectionCode($arResult['SECTIONS'][$id]['URL']), $cur_page, $cur_page_no_index) == true ? 'SELECTED' : '';
                        if($arItem['SELECTED'] != "") {$isSelectedFind = true; break;} // выход из цикла 3
                    }
                }
            }
        }
    }
}

foreach ($arResult['SECTIONS'] as &$section) {
    $section['PROPERTY_LINK_VALUE'] = getHrefWithLastSectionCode($section['PROPERTY_LINK_VALUE']);

    foreach ($section['SUBSECTIONS'] as &$subsection) {
        $subsection['URL'] = getHrefWithLastSectionCode($subsection['URL']);
    }
}

function getHrefWithLastSectionCode($path)
{
    if (strpos($path, '/catalog/') !== 0) {
        return false;
    }
    $lastSectionCode = end(array_filter(explode('/', $path)));
    return '/catalog/' . $lastSectionCode . '/';

}