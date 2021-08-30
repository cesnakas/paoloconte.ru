<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?$this->setFrameMode(true);?>

<?foreach($arResult['SECTIONS'] as $arSection):?>
    <div class="footer-nav__item" data-toggle-wrap>
        <div class="footer-nav__title" data-toggle-btn-m>
            <?=$arSection['NAME']?>
            <div class="check"></div>
        </div>
        <div class="footer-nav__list" data-toggle-list>
            <?foreach($arSection['ITEMS'] as $arItem):?>
                <? if (CModule::IncludeModule("iblock")):
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
                endif; ?>
                <a href="<?=$arItem['PROPERTY_LINK_VALUE']?>" class="footer-nav__link"><?=$arItem['NAME']?></a>
            <?endforeach?>
        </div>
    </div>
<?endforeach?>
