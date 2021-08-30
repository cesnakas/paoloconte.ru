<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?$this->setFrameMode(true);?>
<?foreach($arResult["ITEMS"] as $arItem){?>
    <?
        if ($arItem['PROPERTY_TYPE_BLOCK_VALUE'] != $arParams['TYPE_BLOCK']) {
            continue;
        }
    ?>
    <? if ($arParams['TYPE_BLOCK'] == '1' || $arParams['TYPE_BLOCK'] == '7') { ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <?$path = CFile::GetPath($arItem['PROPERTY_IMAGE_VALUE']);?>
        <div class="main-catalog">
            <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="main-left main-catalog__img">
                <img alt="" class="lazy" src="<?=IMAGE_PLACEHOLDER?>" data-src="<?=$path?>">
            </a>
            <div class="main-right">
                <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="main-title">
                    <?=$arItem['PROPERTY_TITLE_VALUE']?>
                </a>
                <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="btn btn--black"><?=$arItem['PROPERTY_BUTTON_TEXT_VALUE']?></a>
            </div>
        </div>
    <? } elseif ($arParams['TYPE_BLOCK'] == '2') { ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <?$path = CFile::GetPath($arItem['PROPERTY_IMAGE_VALUE']);?>
        <div class="main-catalog">
            <div class="main-left">
                <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="main-title">
                    <?=$arItem['PROPERTY_TITLE_VALUE']?>
                </a>
                <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="btn btn--black"><?=$arItem['PROPERTY_BUTTON_TEXT_VALUE']?></a>
            </div>
            <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="main-right main-catalog__img">
                <img class="lazy" src="<?=IMAGE_PLACEHOLDER?>" data-src="<?=$path?>" alt="">
            </a>
        </div>
    <? } elseif ($arParams['TYPE_BLOCK'] == '3') { ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <?$path = CFile::GetPath($arItem['PROPERTY_IMAGE_VALUE']);?>
        <div class="main-left">
            <img class="lazy" src="<?=IMAGE_PLACEHOLDER?>" data-src="<?=$path?>" alt="">
            <div class="main-accessories__inner">
                <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="main-title">
                    <?=$arItem['PROPERTY_TITLE_VALUE']?>
                </a>
                <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="btn btn--black"><?=$arItem['PROPERTY_BUTTON_TEXT_VALUE']?></a>
            </div>
        </div>
    <? } elseif ($arParams['TYPE_BLOCK'] == '4') { ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <?$path = CFile::GetPath($arItem['PROPERTY_IMAGE_VALUE']);?>
        <div class="main-right">
            <img class="lazy" src="<?=IMAGE_PLACEHOLDER?>" data-src="<?=$path?>" alt="">
            <div class="main-accessories__inner">
                <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="main-title">
                    <?=$arItem['PROPERTY_TITLE_VALUE']?>
                </a>
                <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="btn btn--black"><?=$arItem['PROPERTY_BUTTON_TEXT_VALUE']?></a>
            </div>
        </div>
    <? } elseif ($arParams['TYPE_BLOCK'] == '5') { ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <?$path = CFile::GetPath($arItem['PROPERTY_IMAGE_VALUE']);?>
        <div class="main-inst__left">
            <img class="lazy" src="<?=IMAGE_PLACEHOLDER?>" data-src="<?=$path?>" alt="">
        </div>
    <? } elseif ($arParams['TYPE_BLOCK'] == '6') { ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <?$path = CFile::GetPath($arItem['PROPERTY_IMAGE_VALUE']);?>
        <div class="main-inst__right">
            <img class="lazy" src="<?=IMAGE_PLACEHOLDER?>" data-src="<?=$path?>" alt="">
        </div>
    <? } ?>
<? } ?>
