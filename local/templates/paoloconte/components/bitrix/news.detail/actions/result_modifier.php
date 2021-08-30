<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if($arParams['ADD_SECTIONS_CHAIN'] && !empty($arResult['NAME']))
{
	$arResult['SECTION']['PATH'][] = array(
		'NAME' => $arResult['NAME'],
		'PATH' => ' ');
	$component = $this->__component;
	$component->arResult = $arResult;
}

$curdate = date('Y-m-d');
if ($arResult['PROPERTIES']['DATE_END']['VALUE'] != '') {
	$arResult['DATE_DIFF'] = \Citfact\Tools::datediff($curdate, $arResult['PROPERTIES']['DATE_END']['VALUE']);
}

// Список городов
$arCities = $arResult['DISPLAY_PROPERTIES']['CITY']['LINK_ELEMENT_VALUE'];
if (!empty($arCities)){
	foreach ($arCities as $arCity) {
		$arTemp = array(
			'NAME' => $arCity['NAME'],
			'CODE' => $arCity['CODE']
		);
		$arResult['CITIES_LIST'][] = $arTemp;
	}
}

//Вставка слайдера
function asFindTools($where,$what){
    $pos1 = strpos($where, $what,0);
    $pos2 = strpos($where, ';',$pos1);

    $find = substr($where, $pos1+strlen($what), $pos2 - $pos1 - strlen($what));
    return trim($find);
}

function asGetBoolVal($str)
{
    if(mb_strtolower($str) == 'да' || mb_strtolower($str) == 'true')
    {
        return 'true';
    }
    else
        return 'false';
}

$newText = $arResult['DETAIL_TEXT'];

while (strpos($newText, '#slider:BEGIN#') !== false) {
    $sliderPosBegin = strpos($newText, '#slider:BEGIN#');
    //echo $sliderPosBegin . "!!";
    $sliderPosEnd = strpos($newText, '#slider:END#', $sliderPosBegin);
    //echo $sliderPosEnd;

    $sliderConfig = substr($newText, $sliderPosBegin, $sliderPosEnd - $sliderPosBegin + strlen('#slider:END#'));
    $sectionID = intval(asFindTools($sliderConfig, 'ID раздела инфоблока:'));
    $autoPlay = asGetBoolVal(asFindTools($sliderConfig, 'Автопрокрутка:'));
    $showPagination = asGetBoolVal(asFindTools($sliderConfig, 'Показывать предпросмотр:'));

    //echo $sectionID . "!!";
    //echo $autoPlay . "!!";
    //echo $showPagination . "!!";

    if (!CModule::IncludeModule("citfact.tools"))
        return;

    $arResult['SLIDE'] = array();

    $sliderArOrder = Array("SORT" => "ASC");
    $sliderArSelect = Array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM", "PROPERTY_*");
    $sliderArFilter = Array("IBLOCK_ID" => IBLOCK_AS_SLIDER, "SECTION_ID" => $sectionID, "ACTIVE" => "Y");

    $res = CIBlockElement::GetList($sliderArOrder, $sliderArFilter, false, Array(), $sliderArSelect);
    while ($ob = $res->GetNextElement())
    {
        $item = $ob->GetProperties();

        $rsFile = CFile::ResizeImageGet($item["SLIDE_IMAGE"]["VALUE"], array('width'=>1370, 'height'=>977), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
        $arResult['SLIDE'][] = array("TEXT" => $item["SLIDE_TEXT"]["VALUE"]["TEXT"], "TYPE" => $item["SLIDE_TEXT"]["VALUE"]["TYPE"], "IMAGE" => $rsFile['src']);
    }

    $replacementText = '';

    $replacementText .= '<div id="sliderWrapper' . $sectionID . '" class="owl-carousel owl-theme altSlider">';

    foreach ($arResult['SLIDE'] as $k => $arPic) {
        $replacementText .= '<div class="item"><img style="width: 100%" src="' . $arPic['IMAGE'] . '">';
        if ($arPic["TEXT"] != '') {
            if ($arPic["TYPE"] == 'html') $arPic["TEXT"] = htmlspecialcharsBack($arPic["TEXT"]);
            $replacementText .= '<div class="slideText">' . $arPic["TEXT"] . '</div>';
        }
        $replacementText .= '</div>';
    }

    $replacementText .= '</div>';

    if ($showPagination == 'true') {
        $replacementText .= '<div id="pagPlace' . $sectionID . '" class="slPagPlace">';

        foreach ($arResult['SLIDE'] as $k => $arPic) {
                $replacementText .= '<div class="pagItem">
                    <img src=' . $arPic['IMAGE'] . '>
                </div>';
        }
        $replacementText .= '</div>';
    }

    $replacementText .=
        '<script>$(document).ready(function(){' .
        'function afterOWLinit' . $sectionID . '() {' .
        '$("#pagPlace' . $sectionID . ' .pagItem").each(function (index) {' .
        '$(this).on("click", function () {' .
        'owl = $("#sliderWrapper' . $sectionID . '").data("owlCarousel");' .
        'owl.to(index);' .
        '});' .
        '});' .
        '}' .

        '$("#sliderWrapper' . $sectionID . '").owlCarousel(' .
        '{' .
        'items:1,' .
        'margin:40,' .
        'loop: true,' .
        'autoplay: ' . $autoPlay . ',' .
        'autoplayTimeout: 3000,' .
        'navigation: false,' .
        'pagination: false,' .
        'paginationSpeed : 1000,' .
        'goToFirstSpeed : 2000,' .
        'autoHeight : true,' .
        'afterInit: afterOWLinit' . $sectionID . '()' .
        '}' .
        ');' .
        '});' .
        '</script>';

    $newText = str_replace($sliderConfig,$replacementText,$newText);
}

$arResult['DETAIL_TEXT'] = $newText;

// Добавляем видео вместо специального тега #VIDEO#
if ($arResult['PROPERTIES']['VIDEO']['VALUE']) {
    if (strpos($arResult["DETAIL_TEXT"], "#VIDEO#") !== false) {
        $videoTag = '
            <video class="news_video" width="600" controls>
                <source src="'.$arResult['PROPERTIES']['VIDEO']['VALUE'].'" type="video/mp4">
            </video>';
        $arResult["DETAIL_TEXT"] = str_replace("#VIDEO#", $videoTag, $arResult["DETAIL_TEXT"]);
    }
}