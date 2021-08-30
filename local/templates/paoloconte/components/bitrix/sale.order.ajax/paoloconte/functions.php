<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

function getCodePropByID($props, $id)
{
    $result = false;
    foreach ($props as $prop) {
        if ($prop['ID'] == $id) {
            $result = $prop['CODE'];
        }
    }
    return $result;
}

function isHideProp($arProperties, $isAuthorized)
{
    $hidden = false;
    $hiddenif = false;
    if (!empty($value) && $isAuthorized === true) {
        $hiddenif = true;
    }
    if (
        $arProperties["FIELD_NAME"] == "ORDER_PROP_35"
        || $arProperties['CODE'] == 'sdek_hidden_pvz_adress'
        || $arProperties["ID"] == 42
        || $hiddenif

    ) {
        $hidden = true;
    }
    if (($arProperties['CODE'] == 'LOYALTY_CARD' || $arProperties['CODE'] == 'PHONE') && empty($value)) {
        $hidden = false;
    }
    if ($arProperties['CODE'] == 'FIO') {
        $hidden = true;
    }
    if (in_array($arProperties['CODE'], ['NAME', 'SECOND_NAME', 'SURNAME'])) {
        $hidden = false;
    }

    return $hidden;
}