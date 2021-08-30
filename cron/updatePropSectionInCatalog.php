<?
$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

ini_set('memory_limit', '4096M');

\CModule::IncludeModule('iblock');

$IBLOCK_ID = 10;

$arFilter = [
    'IBLOCK_ID' => $IBLOCK_ID,
];
$arSelect = [
    'ID',
];
$rs = \CIBlockElement::GetList (
    false,
    $arFilter,
    false,
    false,
    $arSelect
);

$arElements = [];
$sectionIDsUnique = [];
while($arElement = $rs->Fetch()) {
    $rsGroups = \CIBlockElement::GetElementGroups($arElement['ID'], true, ['ID']);
    while ($sect = $rsGroups->Fetch()) {
        $arElement['IBLOCK_SECTION_ID'][] = $sect['ID'];
    }

    $arElements[] = $arElement;

    foreach ($arElement['IBLOCK_SECTION_ID'] as $sectID) {
        $sectionIDsUnique[] = $sectID;
    }
}

$sectionIDsUnique = array_unique($sectionIDsUnique);

$xmlIDs = [];
foreach ($sectionIDsUnique as $sectionID) {
    $xmlIDs[] = 'iblock-' . $IBLOCK_ID . '-section-' . $sectionID;
}
$catIDEnumID = array_flip(array_values($sectionIDsUnique)); // [catID => enumID]
$propID = 0;
$property_enums = \CIBlockPropertyEnum::GetList(["DEF" => "DESC", "SORT" => "ASC"], ["IBLOCK_ID" => $IBLOCK_ID, "XML_ID" => $xmlIDs]);
while ($enum_field = $property_enums->GetNext()) {
    $sectID = explode('-section-', $enum_field['XML_ID'])[1];
    $catIDEnumID[$sectID] = $enum_field['ID'];
    $propID = $enum_field['PROPERTY_ID'];
}

$key = 0;
foreach ($arElements as $element) {
    $prop = [];
    foreach ($element['IBLOCK_SECTION_ID'] as $sectID) {
        $prop[$propID][] = ['VALUE' => $catIDEnumID[$sectID]];
    }

    \CIBlockElement::SetPropertyValuesEx($element['ID'], $IBLOCK_ID, $prop);
}