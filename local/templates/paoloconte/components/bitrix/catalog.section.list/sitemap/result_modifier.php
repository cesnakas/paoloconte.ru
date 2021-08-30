<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!function_exists('getHrefWithLastSectionCode')) {
    function getHrefWithLastSectionCode($path)
    {
        if (strpos($path, '/catalog/') !== 0) {
            return false;
        }

        $lastSectionCode = end(array_filter(explode('/', $path)));
        return '/catalog/' . $lastSectionCode . '/';
    }
}

if (!empty($arParams["EXCLUDE_SECTION_CODES"]) && !empty($arResult["SECTIONS"])) {
    foreach ($arResult["SECTIONS"] as $key => $arSection) {
        if (in_array($arSection["CODE"], $arParams["EXCLUDE_SECTION_CODES"])) {
            $leftMargin = $arSection["LEFT_MARGIN"];
            $rightMargin = $arSection["RIGHT_MARGIN"];
            unsetSectionByMargin($leftMargin, $rightMargin, $arResult["SECTIONS"]);
        }
    }
}

function unsetSectionByMargin($leftMargin, $rightMargin, &$arSections)
{
    foreach ($arSections as $key => $arSection) {
        if ($arSection["LEFT_MARGIN"] >= $leftMargin && $arSection["RIGHT_MARGIN"] <= $rightMargin) {
            unset($arSections[$key]);
        }
    }
}

foreach ($arResult['SECTIONS'] as &$section) {
    $section['SECTION_PAGE_URL'] = getHrefWithLastSectionCode($section['SECTION_PAGE_URL']);
}
unset($section);
reset($arResult['SECTIONS']);

$clothesSect = $clothesSectKey = null;

$cnt = 0;
foreach ($arResult['SECTIONS'] as $key => $arSection) {
    $cnt++;
    if ($arSection["DEPTH_LEVEL"] == 1) {
        $isClothes = $arSection["CODE"] == 'odezhda';
        if ($arSection["DEPTH_LEVEL"] ==1 && $arSection["CODE"] == 'odezhda') {
            $clothesSect = $arSection;
            $clothesSect['DEPTH_LEVEL']++;
            $clothesSectKey = $cnt;
        }
    }
    if ($isClothes && $arSection['DEPTH_LEVEL'] > 1) {
        $arResult['SECTIONS'][$key]['DEPTH_LEVEL']++;
        if ($arSection["DEPTH_LEVEL"] >= 3) {
            //unset($arResult['SECTIONS'][$key]);
        }
    }
}
reset($arResult['SECTIONS']);
$arSections = $arResult['SECTIONS'];

if ($clothesSect && !is_null($clothesSectKey)) {

    $res = array_slice($arSections, 0, $clothesSectKey, true) +
        ['new' => $clothesSect] +
        array_slice($arSections, $clothesSectKey, count($arSections)-1, true);

    $arResult['SECTIONS'] = $res;
}
