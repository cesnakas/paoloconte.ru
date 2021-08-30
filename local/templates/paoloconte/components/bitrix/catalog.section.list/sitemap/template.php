<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
?>

<div class="container sitemap">
    <? if (!empty($arResult["SECTIONS"])) { ?>
        <ul class="shops-nav tablist" role="tablist">
            <? foreach ($arResult["SECTIONS"] as $arSection) { ?>
                <? if ($arSection["DEPTH_LEVEL"] != 1) {
                    continue;
                } ?>

                <li role="presentation" class="btn btn--transparent<?= ($arSection["ID"] == $arResult["SECTIONS"][0]["ID"]) ? ' active' : ''; ?>">
                    <a href="#<?= $arSection["ID"]; ?>"
                       aria-controls="<?= $arSection["ID"]; ?>"
                       role="tab"
                       data-toggle="tab">
                        <?= $arSection["NAME"]; ?>
                    </a>
                </li>
            <? } ?>
        </ul>

        <div class="tab-content">
            <? $previousLevel = $cnt = 0; $closed = true; $firstLevelClosed= true; $isClothes = false;?>
            <? foreach ($arResult["SECTIONS"] as $arSection) { ?>
                <?if ($firstLevelClosed == false && $arSection["DEPTH_LEVEL"] == 1) {?>
                </div></div>
                <?}?>
                <? if ($previousLevel && $arSection["DEPTH_LEVEL"] < $previousLevel) {?>
                    <?=str_repeat("</div></div>", ($previousLevel - $arSection["DEPTH_LEVEL"]));?>
                <? } ?>

                <? if ($previousLevel && $arSection["DEPTH_LEVEL"] < $previousLevel && !$closed) { ?>
                    </div>
                <? } ?>

                <? if ($previousLevel == 2 && ($arSection['DEPTH_LEVEL'] == 1)) {?>
                    </div>
                    </div>
                <? } ?>

                <? if ($previousLevel == 2 && ( $arSection['DEPTH_LEVEL'] == 2)) {?>
                    </div>
                    </div>
                <? } ?>

                <? if ($arSection["DEPTH_LEVEL"] < 3) { ?>
                    <?$cnt = 0;?>
                    <? if ($arSection["DEPTH_LEVEL"] == 1) {
                    $firstLevelClosed = false;?>
                        <div role="tabpanel"
                             class="tab-pane fade <?= ($arSection["ID"] == $arResult["SECTIONS"][0]["ID"]) ? 'in active' : ''; ?>"
                             id="<?= $arSection["ID"]; ?>">
                            <div class="subsection-wrap">
                    <? } else {
                    $firstLevelClosed = true;?>
                        <div class="subsection">
                            <div class="subsection-title">
                                <a href="<?= $arSection["SECTION_PAGE_URL"]; ?>">
                                    <?= $arSection["NAME"]; ?>
                                </a>
                            </div>
                            <div class="subsection-d3">
                    <? } ?>
                <? } else {
                    $firstLevelClosed = true;?>
                    <? $cnt++; $closed = false;?>
                    <? if (($cnt == 1) || (($cnt-1) % 4) == 0) { ?>
                        <div class="column">
                    <? } ?>

                    <span class="d3-title">
                        <a href="<?= $arSection["SECTION_PAGE_URL"]; ?>">
                            <?= $arSection["NAME"]; ?>
                        </a>
                    </span>

                    <? if ($cnt % 4 == 0) { ?>
                        <? $closed = true;?>
                        </div>
                    <? } ?>
                <? } ?>
                <?$previousLevel = $arSection["DEPTH_LEVEL"];?>
            <? } ?>

            <? if ($previousLevel > 1) { ?>
                <?=str_repeat("</div></div>", ($previousLevel-1) );?>
            <? } ?>
        </div>
    <? } ?>
</div>
