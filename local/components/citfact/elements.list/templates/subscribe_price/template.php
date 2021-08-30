<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$count_items = 0;
$arId = array_column($arResult["ITEMS"], 'PROPERTY_TOVAR_ID_VALUE');//Собираем массив ID товаров для поиска торговых предложений
foreach($arResult["ITEMS"] as $arItem){
	if (array_key_exists($arItem['PROPERTY_TOVAR_ID_VALUE'], $arResult['TOVARS'])){
		$count_items++;
	}
}
// Получаем список торговых предложений для товаров
$arSKU = CCatalogSKU::getOffersList($arId, 0, array('ACTIVE' => 'Y'), array('*', "PROPERTY_RAZMER","PROPERTY_CAN_BUY"));
$Id[] = 0;
foreach ($arSKU as $items)
{
    foreach ($items as $item) {
        //var_dump($item);
        $Id[$item['ID']] = $item['ID'];
    }
}

$dbBasketItems = CSaleBasket::GetList(
    array(
        "NAME" => "ASC",
        "ID" => "ASC"
    ),
    array(
        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
        "LID" => SITE_ID,
        "PRODUCT_ID" => $Id, //ID текущего товара
        "ORDER_ID" => "NULL",
        "DELAY" => "N" //Исключая отложенные
    ),
    false,
    false,
    array("PRODUCT_ID")
);
while ($arItemsBasket = $dbBasketItems->Fetch()) {
    $itInBasket[$arItemsBasket['PRODUCT_ID']] = $arItemsBasket['PRODUCT_ID'];
}
foreach ($itInBasket as $basketId)
{
    echo ('<div id=' . $basketId . ' data-item-marker-id=' . $basketId . '></div>');
}

// Если нет избранных товаров, показываем сообщение и ссылку на каталог
if ($count_items == 0){
	?>
	<div class="top-text align-center">
		У вас еще нет подписок.
	</div>

	<div class="btn-box align-center">
		<a href="/catalog/" class="btn btn--black" style="width: 340px;max-width:100%;"><span>Перейти в каталог товаров</span></a>
	</div>
<?
}
else{
?>
    
<div class="basket">
    <div class="basket-item-wrap basket-item-wrap--favorite">
        <?foreach($arResult["ITEMS"] as $arItem){
            $tovar_id = $arItem['PROPERTY_TOVAR_ID_VALUE'];
            if (array_key_exists($tovar_id, $arResult['TOVARS'])):?>
                <?
                $price_str = 'CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID'];
                $price_str_action = 'CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID_ACTION'];
                $price = $arResult['TOVARS'] [$tovar_id] [$price_str];
                $price_action = $arResult['TOVARS'] [$tovar_id] [$price_str_action];
                $price_formatted = number_format($price, 0, ',', ' ');
                $price_formatted_action = number_format($price_action, 0, ',', ' ');
            
                $price_subscribe_formatted = number_format($arItem['PROPERTY_PRICE_VALUE'], 0, ',', ' ');
            
                $tovar_url = $arResult['TOVARS'] [$tovar_id] ['DETAIL_PAGE_URL'];
                ?>
            
                <?
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                ?>
                <div class="basket-item">
                    <div class="basket-item__cell basket-item__cell--img">
                        <?if($arResult['TOVARS'] [$tovar_id] ['CATALOG_PHOTO']):?>
                            <a href="<?=$tovar_url?>"><img src="<?=$arResult['TOVARS'] [$tovar_id] ['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>"></a>
                        <?else:?>
                            <img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
                        <?endif;?>
                    </div>
                    <div class="basket-item__cell product-size">
                        <div class="product-size__top">
                            <div class="product-size__title">Размер</div>
                        </div>

                        <div class="product-size__inner" ">
                        <?foreach($arSKU[$arItem['PROPERTY_TOVAR_ID_VALUE']] as $item):?>
                            <label class="<?= $item['ACTIVE'] == 'Y' ? '' : 'lost' ?>">
                                <?=$item['PROPERTY_RAZMER_VALUE']?>
                                <input type="radio"
                                       value="<?= $item['ID'] ?>"
                                       name="r<? echo $item['ID']; ?>"
                                    <?= $item['ACTIVE']  == 'Y' ? '' : 'disabled' ?>
                                       class="radio-offer"
                                       data-product-id="<?= $arResult['TOVARS'] [$tovar_id]['ID']?>"
                                       data-id="<?= $item['ID'] ?>"
                                       data-name="<?= $item['NAME'] ?> (<?= $item['PROPERTY_RAZMER_VALUE']  ?>)"
                                >
                            </label>
                        <?endforeach;?>
                    </div>
                </div>
                    <div class="basket-item__cell basket-item__cell--info">
                        <a href="<?=$tovar_url?>" class="basket-item__title"><?=$arResult['TOVARS'] [$tovar_id] ['NAME']?></a>
                        <?/*<div class="index"><?=$arResult['TOVARS'] [$tovar_id] ['ARTICUL']?></div>*/?>
                    </div>
                    <div class="basket-item__cell basket-item__cell--price">
                        <div class="basket-item-price">
                            <?if ($price_action != ''):?>
                                <div class="basket-item-price__old rouble">
                                    <?=$price_formatted?>
                                </div>
                                <div class="basket-item-price__current rouble">
                                    <?=$price_formatted_action?>
                                </div>
                            <?else:?>
                                <div class="basket-item-price__current rouble">
                                    <?=$price_formatted?>
                                </div>
                            <?endif;?>
                        </div>
                    </div>
                    <div class="basket-item__cell basket-item__cell--sum">
                        <div class="rouble">
                            <?=$price_subscribe_formatted?>
                        </div>
                    </div>
                    <div class="basket-item__cell basket-item__cell--mail">
                        <?=$arItem['PROPERTY_EMAIL_VALUE']?>
                    </div>
                    <div class="basket-item__cell basket-item__cell--add">
                        <?if ($arResult['TOVARS'][$tovar_id]['OFFERS_AMOUNT'] > 0):?>
                            <a href="#"
                               class="btn btn--black btn-tobasket-insubscribe btn-tobasket"
                               data-is-basket= false
                               data-product-id="<?=$arResult['TOVARS'] [$tovar_id]['ID']?>"
                               data-product-id-marker="<?=$arResult['TOVARS'] [$tovar_id]['ID']?>"
                               >
                                <span id="S<?=$arResult['TOVARS'] [$tovar_id]['ID']?>">В корзину</span>
                            </a>
                        <?else:?>
                            Нет на складе
                        <?endif;?>
                    </div>

                    <div class="basket-item__cell basket-item__cell--del">
                        <a href="#"
                           class="del-subscribe"
                           data-element-id="<?=$arItem['ID']?>"
                           title="Удалить подписку">
                            <span class="plus plus--cross"></span>
                        </a>
                    </div>
                </div>
            <?endif?>
        <? } ?>
    </div>
</div>
<?}?>