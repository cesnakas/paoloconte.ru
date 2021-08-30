<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (isset($arParams["TEMPLATE_THEME"]) && !empty($arParams["TEMPLATE_THEME"]))
{
	$arAvailableThemes = array();
	$dir = trim(preg_replace("'[\\\\/]+'", "/", dirname(__FILE__)."/themes/"));
	if (is_dir($dir) && $directory = opendir($dir))
	{
		while (($file = readdir($directory)) !== false)
		{
			if ($file != "." && $file != ".." && is_dir($dir.$file))
				$arAvailableThemes[] = $file;
		}
		closedir($directory);
	}

	if ($arParams["TEMPLATE_THEME"] == "site")
	{
		$solution = COption::GetOptionString("main", "wizard_solution", "", SITE_ID);
		if ($solution == "eshop")
		{
			$theme = COption::GetOptionString("main", "wizard_eshop_adapt_theme_id", "blue", SITE_ID);
			$arParams["TEMPLATE_THEME"] = (in_array($theme, $arAvailableThemes)) ? $theme : "blue";
		}
	}
	else
	{
		$arParams["TEMPLATE_THEME"] = (in_array($arParams["TEMPLATE_THEME"], $arAvailableThemes)) ? $arParams["TEMPLATE_THEME"] : "blue";
	}
}
else
{
	$arParams["TEMPLATE_THEME"] = "blue";
}

$arParams["FILTER_VIEW_MODE"] = (isset($arParams["FILTER_VIEW_MODE"]) && $arParams["FILTER_VIEW_MODE"] == "horizontal") ? "horizontal" : "vertical";
$arParams["POPUP_POSITION"] = (isset($arParams["POPUP_POSITION"]) && in_array($arParams["POPUP_POSITION"], array("left", "right"))) ? $arParams["POPUP_POSITION"] : "left";

// Убираем товарную группу на последнем уровне каталога
$level = count(explode('/',$APPLICATION->GetCurDir()));
$arChildSectionsID = Citfact\Sections::getChildSections(10, $arParams['SECTION_ID'], [], 'ID');

foreach ($arResult['ITEMS'] as $key => $arItem){
	if ($arItem['CODE'] == 'TOVARNAYA_GRUPPA_MARKETING' && $level >= 6){
		unset($arResult['ITEMS'][$key]);
	}
    if (empty($arChildSectionsID) && $arItem['CODE'] == 'SECTION')
    {
        unset($arResult['ITEMS'][$key]);
    }
}

// Сортируем свойства
$arResult['ITEMS_SORTED'] = array();
$arResult['ITEMS_SORTED'][] = $arResult['ITEMS'][249];
$arResult['ITEMS_SORTED'][] = $arResult['ITEMS'][267];
foreach ($arResult['ITEMS'] as $key => $arItem){
	if ($key != 249 && $key != 267){
		$arResult['ITEMS_SORTED'][] = $arItem;
	}
}
$arResult['ITEMS'] = $arResult['ITEMS_SORTED'];

global $APPLICATION;
$dir = $APPLICATION->GetCurDir();

// Сортирую фильтры костылём. По хорошему бы переделать на сортировку полю sort в свойствах.
$arSorted = [];
foreach ($arResult['ITEMS'] as $key => $property) {
    if ($property['CODE'] == 'SECTION')
    {
        $sectionKey = $key;
        $arSorted[] = $arResult['ITEMS'][$sectionKey]; // Первым ставим фильтр "Категория"
    }


}

if (!empty($sectionKey))
{
    foreach ($arResult['ITEMS'] as $key => $property) {
        if ($key == $sectionKey)
            continue;

        $arSorted[] = $property;
    }
    $arResult['ITEMS'] = $arSorted;
}

// END. Сортирую фильтры костылём. По хорошему бы переделать на сортировку полю sort в свойствах.

// Переименовываем некоторые свойства для фильтра
foreach ($arResult['ITEMS'] as $key => &$property) {
    switch ($property['CODE']) {
        case 'MATERIAL_VERKHA_FILTR':
            $property['NAME'] = 'Материал верха';
            break;
        case 'MATERIAL_PODKLADKI_FILTR':
            $property['NAME'] = 'Материал подкладки';
            break;
        case 'SECTION': // Задача № 74687. Обработка фильтра по свойству SECTION - "Раздел"
            if ($dir == '/catalog/' || $dir == '/catalog/rasprodazha/') { // не показываем в этих разделах фильтр "раздел". Т.к дублировались категории "кросовки", "кросовки" - мужские и женские.
                unset($arResult['ITEMS'][$key]);
            } else {
                /* Задача № 74687. Решение проблемы дублирования категорий в фильтре. Показываем только подкатегории текущей категории */
                $IBLOCK_ID = 10;
                // Выбираем все дочерние категории.
                $arChildSectionsID = Citfact\Sections::getChildSections(10, $arParams['SECTION_ID'], [], 'ID');

                $xmlIDs = [];
                foreach ($arChildSectionsID as $sectionID) {
                    $xmlIDs[] = 'iblock-' . $IBLOCK_ID . '-section-' . $sectionID;
                }
                $catIDEnumID = array_flip(array_values($sectionIDsUnique)); // [catID => enumID]
                $propID = 0;
                $property_enums = \CIBlockPropertyEnum::GetList(["DEF" => "DESC", "SORT" => "ASC"], ["IBLOCK_ID" => $IBLOCK_ID, "XML_ID" => $xmlIDs]);
                while ($enum_field = $property_enums->GetNext()) {
                    $sectID = explode('-section-', $enum_field['XML_ID'])[1];
                    $catIDEnumID[$sectID] = $enum_field['ID'];
                }

                foreach ($arResult['ITEMS'][$key]['VALUES'] as $enumID => $item) {
                    if (!in_array($enumID, $catIDEnumID))
                        unset($arResult['ITEMS'][$key]['VALUES'][$enumID]);
                }
                /* Задача № 74687. Решение проблемы дублирования категорий в фильтре. Показываем только подкатегории текущей категории */
            }
            break;
    }
}
unset($property);

global $sotbitFilterResult;  
$sotbitFilterResult = $arResult;