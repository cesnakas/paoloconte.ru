<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
//$GLOBALS['APPLICATION']->AddHeadScript($templateFolder . '/script.js');
//$GLOBALS['APPLICATION']->SetAdditionalCSS($templateFolder . '/style.css');
?>
<?$this->setFrameMode(true);?>
<?
$maxCountMnuItem = 10; // кол-во элементов в одном столбце меню
?>
<ul class="top-menu"   itemscope itemtype="http://www.schema.org/SiteNavigationElement" >
<?foreach($arResult['ITEMS'] as $arItem):?>
    <li class="top-menu__item<?=$arItem['SELECTED'] == 'SELECTED'? ' active':''?>">
        <a  itemprop="url"  href="<?=$arItem['PROPERTY_LINK_VALUE'] ?: '#'?>" class="top-menu__link">
            <meta itemprop="name" content="<?=$arItem['NAME']?>" />
            <?=$arItem['NAME']?>
        </a>
        <?if (!empty($arItem['PROPERTY_CATALOG_SECTION_VALUE'])):?>
            <div class="top-menu__dropdown">
                <div class="top-menu__items">
                    <?$arBanner = array();?>
                    <?foreach($arItem['PROPERTY_CATALOG_SECTION_VALUE'] as $parent_id):?>
                        <div class="top-menu__inner<?=$arResult['SECTIONS'][$parent_id]['COUNT'] >= $maxCountMnuItem + 1 ? ' double':''?>">
                            <div class="top-menu__title">
                                <a itemprop="url"  href="<?=$arResult['SECTIONS'][$parent_id]['URL']?>">
                                <meta itemprop="name" content="<?=$arResult['SECTIONS'][$parent_id]['NAME']?>" />
                                <?=$arResult['SECTIONS'][$parent_id]['NAME']?>
                                </a>
                            </div>
                            <div class="top-menu__content">
                                <ul>
                                    <?$count = 0;?>
                                    <?foreach ($arResult['SECTIONS'][$parent_id]['SUBSECTIONS'] as $arSubsect):?>
                                    <?$count++;?>
                                    <li itemprop="name" ><a itemprop="url"  href="<?=$arSubsect['URL']?>"><?=$arSubsect['NAME']?></a></li>
                                    <?if ($count >= $maxCountMnuItem): $count = 0;?></ul><br><ul><?endif?>
                                    <?endforeach?>
                                </ul>
                            </div>
                            <?if (!empty($arResult['SECTIONS'][$parent_id]['BANNER'])):?>
                                <?$arBanner = $arResult['SECTIONS'][$parent_id]['BANNER'];?>
                            <?endif?>
                        </div>
                    <?endforeach?>

                    <? if (!empty($arBanner)): ?>
                        <div class="top-menu__inner top-menu__inner--img">
                            <div class="top-menu__title">
                                <a href="<?= $arBanner['PROPERTY_LINK_VALUE'] ?>">
                                    <?= $arBanner['NAME'] ?>
                                </a>
                            </div>

                            <a href="<?= $arBanner['PROPERTY_LINK_VALUE'] ?>">
                                <? $file = CFile::ResizeImageGet($arBanner['PROPERTY_IMAGE_VALUE'], array('width' => 250, 'height' => 200), BX_RESIZE_IMAGE_EXACT); ?>
                                <img src="<?= $file['src'] ?>" alt="<?= $arBanner['NAME'] ?>"
                                     title="<?= $arBanner['NAME'] ?>">
                            </a>
                        </div>
                    <? endif; ?>
                </div>
            </div>
        <?endif?>
    </li>
<?endforeach?>
</ul>
