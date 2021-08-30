<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
    'NAME' => Loc::getMessage('FACT_RESERVE_NAME'),
    'DESCRIPTION' => Loc::getMessage('FACT_RESERVE_DESCRIPTION'),
    'SORT' => 10,
    'CACHE_PATH' => 'Y',
    'PATH' => array(
        'ID' => 'citfact',
        'NAME' => Loc::getMessage('CITFACT_NAME'),
    ),
);
