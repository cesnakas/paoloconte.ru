<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);
//_c($arResult);

if (!empty($arResult['ITEMS'])):?>
	<div class="product-similar">
        <div class="title-3">
            Похожие товары <?/*С этим товаром покупают*/?>
        </div>
        
        <div class="product-similar__slider">
            <?foreach ($arResult['ITEMS'] as $arItem):?>
                <a href="<?=$arItem['DETAIL_PAGE_URL']?>">
                    <div class="c-g-item__img">
                        <?if($arItem['CATALOG_PHOTO']):?>
                            <img src="<?=$arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>">
                        <?else:?>
                            <img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
                        <?endif;?>
                    </div>
                    <span class="c-g-item__title">
                        title
                    </span>
                    <div class="c-g-item-price">
                        <?if(!empty($arItem['NEW_PRICE'])):?>
                            <div class="c-g-item-price__old">
                                <?=$arItem['OLD_PRICE'];?>
                            </div>
                            <div class="c-g-item-price__current">
                                <?=$arItem['NEW_PRICE'];?>
                            </div>
                        <?else:?>
                            <div class="c-g-item-price__current">
                                <?=$arItem['OLD_PRICE'];?>
                            </div>
                        <?endif;?>
                    </div>
                </a>
            <?endforeach;?>
        </div>
	</div>
<?endif?>