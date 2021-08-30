<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
foreach ($arResult['ITEMS'] as $item)
{
	$arResult['SLIDER'] = CFile::GetPath($item['PROPERTIES']['IMAGE']['VALUE']);
}


?>