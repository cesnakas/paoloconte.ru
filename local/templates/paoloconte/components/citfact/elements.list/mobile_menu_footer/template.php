<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$this->setFrameMode(true);
?>

<div class="header-m-toggle header-m-toggle--catalog">
    <? foreach ($arResult['SECTIONS'] as $arSection) { ?>
        <div class="header-m-toggle__item" data-toggle-wrap>
            <div class="header-m-toggle__title header-m-toggle__title--grey">
                <div data-toggle-btn><?= $arSection['NAME'] ?></div>
                <div class="check" data-toggle-btn></div>
            </div>

            <div class="header-m-toggle__list" data-toggle-list>
                <? foreach ($arSection['ITEMS'] as $arItem) { ?>
                    <? if (CModule::IncludeModule("iblock")) {
                        $iblock_id = 15;
                        $elements = CIBlockElement::GetList(
                            Array("ID" => "ASC"),
                            Array("IBLOCK_ID" => $iblock_id,
                                "ID" => $arItem["ID"]),
                            false,
                            false,
                            Array('ID', 'NAME', 'DETAIL_PAGE_URL', 'PREVIEW_PICTURE', 'DETAIL_PICTURE')
                        );

                        while ($ar_fields = $elements->GetNext()) {
                            $img_path = CFile::GetPath($ar_fields["PREVIEW_PICTURE"]);
                        }
                    } ?>
                    <div class="header-m-toggle__inner active">
                        <div style="display: block">
                            <a href="<?= $arItem['PROPERTY_LINK_VALUE'] ?>"
                               class="header-m-toggle__link"><?= $arItem['NAME'] ?></a>
                        </div>
                    </div>

                <? } ?>
            </div>
        </div>
    <? } ?>
</div>
