<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$frame = $this->createFrame()->begin('');
//_c($arResult);

if (!empty($arResult['ITEMS'])):?>
	<div class="product-watched">
            
        <div class="title-3">
            Вы недавно смотрели
        </div>
            
        <div class="product-watched__slider">
            <?foreach ($arResult['ITEMS'] as $arItem):?>
                <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="product-watched__item">
                    <div class="product-watched__img">
                        <?if($arItem['CATALOG_PHOTO']):?>
                            <img src="<?=$arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>">
                        <?else:?>
                            <img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
                        <?endif;?>
                    </div>
                </a>
            <?endforeach;?>
        </div>
	</div>
<?endif?>
<?$frame->end();?>