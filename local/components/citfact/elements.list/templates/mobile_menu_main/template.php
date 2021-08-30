<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);

$maxCountMnuItem = 10; // кол-во элементов в одном столбце меню
?>

<div class="header-m-toggle header-m-toggle--catalog">
    <? foreach ($arResult['ITEMS'] as $arItem) { ?>
        <div class="header-m-toggle__item" data-toggle-wrap>
            <div class="header-m-toggle__title <?if($arItem['NAME'] == 'Акции' || $arItem['NAME'] == 'Магазины'):?> header-m-toggle__title--grey<?endif;?>">
                <? if (!empty($arItem['PROPERTY_CATALOG_SECTION_VALUE'])) { ?>
                    <a href="<?= $arItem['PROPERTY_LINK_VALUE'] ?>" ><?= $arItem['NAME'] ?></a>
                    <div class="check" data-toggle-btn></div>
                <? } else { ?>
                    <a href="<?= $arItem['PROPERTY_LINK_VALUE'] ?>">
                        <?= $arItem['NAME'] ?>
                    </a>
                <? } ?>
            </div>

            <? if (!empty($arItem['PROPERTY_CATALOG_SECTION_VALUE'])) { ?>
                <div class="header-m-toggle__list" data-toggle-list>
                    <? foreach ($arItem['PROPERTY_CATALOG_SECTION_VALUE'] as $parent_id) { ?>
                        <?if($arResult['SECTIONS'][$parent_id]['URL'] != "/catalog/odezhda/"){?>
                        <div class="header-m-toggle__inner" data-toggle-wrap>
                            <div>
                                <? if (!empty($arResult['SECTIONS'][$parent_id]['SUBSECTIONS'])) { ?>
                                    <a href="<?= $arResult['SECTIONS'][$parent_id]['URL'] ?>" class="header-m-toggle__link">
                                        <?= $arResult['SECTIONS'][$parent_id]['NAME'] ?>
                                    </a>

                                    <div class="plus" data-toggle-btn></div>
                                <? } else { ?>
                                    <a href="<?= $arResult['SECTIONS'][$parent_id]['URL'] ?>" class="header-m-toggle__link">
                                        <?= $arResult['SECTIONS'][$parent_id]['NAME'] ?>
                                    </a>
                                <? } ?>
                            </div>

                            <div data-toggle-list>
                                <? foreach ($arResult['SECTIONS'][$parent_id]['SUBSECTIONS'] as $arSubsect) { ?>
                                    <a href="<?= $arSubsect['URL'] ?>"><?= $arSubsect['NAME'] ?></a>
                                <? } ?>
                            </div>
                        </div>
                            <?}else{?>
                            <div class="header-m-toggle__inner active">
                                <div style="display: block">
                                    <?$i=0;?>
                                    <? foreach ($arResult['SECTIONS'][$parent_id]['SUBSECTIONS'] as $arSubsect) { ?>
                                        <a href="<?= $arSubsect['URL'] ?>"><?= $arSubsect['NAME'] ?></a>
                                        <?$i++?>
                                    <? } ?>
                                </div>
                            </div>
                            <?}?>
                    <? } ?>
                </div>
            <? } ?>
        </div>
    <? } ?>
</div>
