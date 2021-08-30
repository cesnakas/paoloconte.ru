<?php
if (array_key_exists('PAGEN_' . $arResult['NAV_RESULT_NAV_NUM'], $_GET)) {

    $curPage = $APPLICATION->GetCurPage(false);
    $APPLICATION->SetPageProperty('canonical', 'https://' . $_SERVER['SERVER_NAME'] . $curPage);
}
