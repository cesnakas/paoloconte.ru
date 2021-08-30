<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//delayed function must return a string
if (empty($arResult))
    return "";

$strReturn = '<div class="breadcrumb">';

$num_items = count($arResult);
for ($index = 0, $itemSize = $num_items; $index < $itemSize; $index++) {
    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);

    $nextRef = ($index < $itemSize - 2 && $arResult[$index + 1]["LINK"] <> "" ? ' itemref="bx_breadcrumb_' . ($index + 1) . '"' : '');
    $child = ($index > 0 ? ' itemprop="child"' : '');


    if ($arResult[$index]["LINK"] <> "" && $index != $itemSize - 1) {
        $strReturn .= $arrow . '
			<span class="breadcrumb__item" id="bx_breadcrumb_' . $index . '" itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"' . $child . $nextRef . '>
				<a href="' . $arResult[$index]["LINK"] . '" title="' . $title . '" itemprop="url">
					<span itemprop="title">' . $title . '</span>
				</a>
			</span>';
    } else {
        $strReturn .= $arrow . '
		<span class="breadcrumb__item">
			<span>' . $title . '</span>
		</span>';
    }
}

$strReturn .= '</div>';

return $strReturn;
