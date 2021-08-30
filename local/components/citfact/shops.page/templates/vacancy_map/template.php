<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
$pathToYmaps = 'https://api-maps.yandex.ru/2.1/?apikey=' .
    \COption::GetOptionString("main", "map_yandex_keys") . '&lang=ru_RU';
if($arParams['NOMAPS']!='Y')
    $APPLICATION->AddHeadString('<script src="'.$pathToYmaps.'" type="text/javascript"></script>');
?>
<script>
	BX.message({
		SHOPS_TO_MAP: <? echo CUtil::PhpToJSObject($arResult['SHOPS']) ?>,
		CITY_CURRENT: <? echo CUtil::PhpToJSObject($arResult['CITY_CURRENT']) ?>
	});
</script>

<div id="vacancy-page-map" class="map-container" style="height: 500px;"></div>