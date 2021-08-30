<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

$arResult['NUM_READY'] = 0;
$arResult['NUM_DELAY'] = 0;
foreach ($arResult["CATEGORIES"] as $category => $items) {
	if (empty($items))
		continue;

	foreach ($items as $key => $arItem) {
		if ($category == 'READY')
			$arResult['NUM_READY']++;
		if ($category == 'DELAY')
			$arResult['NUM_DELAY']++;
	}
}
?>