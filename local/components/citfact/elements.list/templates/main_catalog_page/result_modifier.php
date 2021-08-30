<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$cur_page = $APPLICATION->GetCurPage(true);
$cur_page_no_index = $APPLICATION->GetCurPage(false);

$arSectionIds = array();
foreach ($arResult['ITEMS'] as $arItem){
	if (!empty($arItem['PROPERTY_CATALOG_SECTION_VALUE'])){
		$arSectionIds = array_merge($arSectionIds, $arItem['PROPERTY_CATALOG_SECTION_VALUE']);
	}
}

if (!empty($arSectionIds)) {
	$arSections = array();
	$arFilter = array('IBLOCK_ID' => $arParams['IBLOCK_CATALOG_ID'], 'ID' => $arSectionIds);
	$rsParentSection = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter, false, array('UF_PHOTO_MOBILE'));
	while ($arParentSection = $rsParentSection->GetNext())
	{
		$parent_id = $arParentSection['ID'];
		$arSections[$parent_id]['NAME'] = $arParentSection['NAME'];
		$arSections[$parent_id]['URL'] = $arParentSection['SECTION_PAGE_URL'];
		$arSections[$parent_id]['UF_PHOTO_MOBILE'] = CFile::GetPath($arParentSection['UF_PHOTO_MOBILE']);

		$arFilter = array(
			'IBLOCK_ID' => $arParentSection['IBLOCK_ID'],
			'>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'],
			'<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'],
			'>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL'],
			'GLOBAL_ACTIVE' => 'Y',
			'!NAME' => 'Группа не определена',
			'PROPERTY' => array('>OFFERS_AMOUNT' => 0)
		); // выберет потомков без учета активности
		$rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'),$arFilter, false, array('ID', 'NAME', 'SECTION_PAGE_URL'));
		$arSections[$parent_id]['COUNT'] = 0;
		while ($arSect = $rsSect->GetNext())
		{
			$arTemp = array();
			$arTemp['NAME'] = $arSect['NAME'];
			$arTemp['CODE'] = $arSect['CODE'];
			$arTemp['URL'] = $arSect['SECTION_PAGE_URL'];
			$arTemp['SELECTED'] = CMenu::IsItemSelected($arSect['SECTION_PAGE_URL'], $cur_page, $cur_page_no_index) == true? 'SELECTED':'';
			$arSections[$parent_id]['SUBSECTIONS'][] = $arTemp;
			$arSections[$parent_id]['COUNT']++;
		}
	}
	$arResult['SECTIONS'] = $arSections;
}

foreach ($arResult['ITEMS'] as &$arItem){
	if (!empty($arItem['PROPERTY_CATALOG_SECTION_VALUE'])){
		foreach ($arItem['PROPERTY_CATALOG_SECTION_VALUE'] as $section_id){
			if (!empty($arResult['SECTIONS'][$section_id]['SUBSECTIONS'])) {
				foreach ($arResult['SECTIONS'][$section_id]['SUBSECTIONS'] as $arSubsect) {
					if ($arItem['SELECTED'] == '') {
						$arItem['SELECTED'] = $arSubsect['SELECTED'];
					}
				}
			}
		}
	}
	else if($arItem['PROPERTY_LINK_VALUE'] != ''){
		$arItem['SELECTED'] = CMenu::IsItemSelected($arItem['PROPERTY_LINK_VALUE'], $cur_page, $cur_page_no_index) == true? 'SELECTED':'';
	}
}

//echo "<pre style=\"display:none;\">"; print_r($arResult); echo "</pre>";