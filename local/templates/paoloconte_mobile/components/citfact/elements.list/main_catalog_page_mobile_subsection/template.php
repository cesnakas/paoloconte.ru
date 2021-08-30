<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

?>
<? if (!empty($arResult['SECTIONS'])): ?>
    <div class="table-emulate-subsection full table-section subsection">
        <? foreach ($arResult['SECTIONS'] as $section): ?>
            <div class="cell-emulate-subsection align-center valign-middle half">
                <a class="main-a-catalog" href="<?= $section['URL']; ?>">
                    <div class="img-block">
                        <img src="<?= $section['PHOTO']; ?>">
                    </div>
                    <?= $section['NAME']; ?>
                </a>
            </div>
        <? endforeach; ?>
    </div>
<? endif ?>
