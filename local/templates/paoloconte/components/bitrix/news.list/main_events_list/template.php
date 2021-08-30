<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<div class="main-news">
    
    <? foreach($arResult["ITEMS"] as $arItem) { ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        $file = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width'=>300, 'height'=>300), BX_RESIZE_IMAGE_EXACT);
        
        $str_time = '';
        if (!empty($arItem['DATE_DIFF'])) {
            $days = $arItem['DATE_DIFF']['days'];
            $invert = $arItem['DATE_DIFF']['invert'];
            if ($days > 0 && $invert != 1)
                $str_time = \Citfact\Tools::declension($days, array("день", "дня", "дней")) . ' до завершения акции';
            elseif ($days == 0)
                $str_time = 'Акция завершается сегодня';
            elseif  ($days > 0 && $invert == 1)
                $str_time = 'Акция завершена';
        }
        ?>
        <a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="sale__item">
            <div class="main-news__sale">
                <div class="main-news__img lazy" data-src="<?=$file['src']?>"></div>
                <div class="sale-subtitle">
                    <?=TruncateText($arItem['NAME'], 100)?>
                </div>
                <div class="sale-text">
                    <?echo TruncateText($arItem["PREVIEW_TEXT"], 200);?>
                </div>
                <div class="sale-text">
                    <?=$str_time?>
                </div>
            </div>
        </a>
    <? } ?>


    <div class="main-news-item">
        <div class="main-news-item__inner">
            <div class="main-title">
                Пресс-центр
            </div>
            <a href="/events/" class="btn btn--transparent">
                Все новости
            </a>
        </div>
    </div>
</div>
