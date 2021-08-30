<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
//$GLOBALS['APPLICATION']->AddHeadScript($templateFolder . '/script.js');
//$GLOBALS['APPLICATION']->SetAdditionalCSS($templateFolder . '/style.css');
?>
<?$this->setFrameMode(true);?>
<?foreach($arResult['SECTIONS'] as $arSection):?>
    <div class="col-5 col">
        <div class="title" itemprop="name" >
            <?=$arSection['NAME']?>
        </div>
        <ul>
        <?foreach($arSection['ITEMS'] as $arItem):?>
            <li itemprop="name" ><a itemprop="url" href="<?=$arItem['PROPERTY_LINK_VALUE']?>"><?=$arItem['NAME']?></a></li>
        <?endforeach?>
        </ul>
    </div>
<?endforeach?>