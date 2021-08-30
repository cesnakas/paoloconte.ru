<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//echo'Det <pre>';print_r($arResult['PROPERTIES']);echo'</pre>';
if (is_array($arResult['PROPERTIES']['IMAGE']['VALUE'])) 
{
	foreach ($arResult['PROPERTIES']['IMAGE']['VALUE'] as $k => $prop)
	{
		$arResult['SLIDER'][$k]['ID'] = $prop;
		$arResult['SLIDER'][$k]['TEXT'] = $arResult['PROPERTIES']['IMAGE']['DESCRIPTION'][$k];
	}
}
if (strlen($arResult['DETAIL_PICTURE']['ID']) > 0) 
{
	$arResult['SLIDER'][$k+1]['ID'] = $arResult['DETAIL_PICTURE']['ID'];
	$arResult['SLIDER'][$k+1]['TEXT'] = $arResult['DESCRIPTION'];
}
?>