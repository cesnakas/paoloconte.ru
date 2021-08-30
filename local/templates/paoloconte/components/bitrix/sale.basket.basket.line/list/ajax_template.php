<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->IncludeLangFile('template.php');
$cartId = $arParams['cartId'];
?>
<? require(realpath(dirname(__FILE__)) . '/top_template.php'); ?>
    <div class="moved-panel-body">
        <ul class="item-list">
            <? if ($arParams["SHOW_PRODUCTS"] == "Y" && $arResult['NUM_READY'] > 0): ?>
                <? foreach ($arResult["CATEGORIES"] as $category => $items):
                    if (empty($items) || $category != 'READY')
                        continue;
                    ?>
                    <? foreach ($items as $v): ?>
                    <li class="item emulate-table full animation right-to-left">
                        <div class="image emulate-cell align-center valign-middle">
                            <? if ($v['CATALOG_PHOTO']): ?>
                                <img src="<?= $v['CATALOG_PHOTO']['PHOTO'][0]['SMALL'] ?>" alt="<?= $v['NAME'] ?>">
                            <? else: ?>
                                <img src="<?= $arResult['NOPHOTO'] ?>" alt="нет фото">
                            <? endif; ?>
                        </div>
                        <div class="info emulate-cell valign-top">
                            <div class="index"><a href="<?= $v["DETAIL_PAGE_URL"] ?>"><?= $v["NAME"] ?></a></div>
                            <? if ($v['CATALOG']['PROPERTIES']['RAZMER']['VALUE'] != ''): ?>
                                <div class="option">Размер: <?= $v['CATALOG']['PROPERTIES']['RAZMER']['VALUE'] ?></div>
                            <? endif ?>

                            <div class="count"><?= $v["QUANTITY"] ?> шт х <span
                                        class="price rouble"><?= str_replace(' руб.', '', $v["PRICE_FORMATED"]) ?></span>
                            </div>
                            <span class="del-cart-item close-small"
                                  onclick="<?= $cartId ?>.removeItemFromCart(<?= $v['ID'] . "," . $v['PRODUCT_ID'] ?>)"
                                  title="<?= GetMessage("TSB1_DELETE") ?>"></span>
                        </div>
                    </li>
                <? endforeach ?>
                <? endforeach ?>
            <? endif ?>

            <? // Empty cart ?>
            <? if ($arResult['NUM_READY'] == 0): ?>
                <li class="empty-cart-content emulate-table full animation right-to-left">
                    <div class="emulate-cell align-center valign-middle">
                        Ваша корзина пуста :(
                    </div>
                </li>
                <li class="emulate-table full animation right-to-left">
                    <div class="emulate-cell align-center valign-middle">
                        <a href="/catalog/" class="btn btn-green full big-2 fa mode1 fa-shopping-cart"><span>Начать покупки</span></a>
                    </div>
                </li>

                <? if ($arResult['NUM_DELAY'] > 0): ?>
                    <li class="emulate-table full animation right-to-left">
                        <div class="emulate-cell align-center valign-middle">
                            <a href="/cabinet/favorites/" class="favorite btn btn-gray full big-2"><i
                                        class="fa fa-heart"></i> Желаемых товаров: <?= $arResult['NUM_DELAY'] ?></a>
                        </div>
                    </li>
                <? endif; ?>
            <? endif ?>
        </ul>
    </div>

<? if ($arResult['NUM_READY'] > 0): ?>
    <div class="moved-panel-footer">
        <div class="emulate-table full animation right-to-left">
            <div class="emulate-cell align-center valign-middle btn-box">
                <a href="<?= $arParams["PATH_TO_ORDER"] ?>" class="btn btn-green full big-2 mode2 icon-arrow-right">Оформить
                    заказ</a>
                <? if ($arResult['NUM_DELAY'] > 0): ?>
                    <a href="/cabinet/favorites/" class="favorite btn btn-gray full big-2"><i class="fa fa-heart"></i>
                        Желаемых товаров: <?= $arResult['NUM_DELAY'] ?></a>
                <? endif; ?>
            </div>
        </div>
    </div>
<? endif ?>