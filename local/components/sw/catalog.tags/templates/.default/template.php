<?php  if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 * @var string $templateFolder
 */

Loc::loadMessages(__FILE__);
$this->setFrameMode(true);
?>
<!-- start catalog.tags template -->
<?php if (!empty($arResult['ITEMS'])){ ?>
    <div class="catalog-tags">
        <div class="catalog-tags__title"><?= ($arParams['BLOCK_TITLE'] ? $arParams['BLOCK_TITLE'] : Loc::getMessage('STC_TITLE'))?></div>
        <div class="catalog-tags__tags-wrapper">
            <ul class="catalog-tags__tags">
                <?php foreach ($arResult['ITEMS'] as $tag){?>
                    <li class="catalog-tags__tag-item">
                        <a class="catalog-tags__tag-name" href="<?=$tag['TARGET_URL']?>">
                            <?=$tag['NAME']?>
                        </a>
                    </li>
                <?php }?>
            </ul>
        </div>
    </div>
<?php } ?>
<!-- end catalog.tags template -->