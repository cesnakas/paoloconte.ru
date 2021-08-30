<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
</div>
</section>
</main>
<?/*
    <footer>
        <div class="container">

            <div class="social-box align-center">
                Мы в социальных сетях:
                <div class="items">
                    <?$APPLICATION->IncludeFile(
                        SITE_DIR."include/footer_social_links.php",
                        Array(),
                        Array("MODE"=>"text")
                    );?>
                </div>
            </div>
            <a href="<?=URL_FULL_VERSION?>?version=full">Полная версия сайта</a>
        </div>
    </footer>
*/?>
</div>
</div>


<!--modals-->

<div class="modal fade fastOrderModal" id="fastOrderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>

            <div class="modal-body">
                <div class="modal-title">
                    Быстрый заказ
                </div>
                <div class="modal-title-desc">
                    Вам достаточно заполнить только три поля
                </div>

                <div class="fast-order-wrap emulate-table full">
                    <div class="emulate-cell  valign-bottom image">
                        <img src="images/content/detail-item-big-1.png">
                    </div>
                    <div class="emulate-cell valign-top form">
                        <form action="#">
                            <div class="line">
                                <input type="text" class="style2" placeholder="Как к Вам можно обращаться?">
                            </div>
                            <div class="line">
                                <input type="text" class="style2" placeholder="Укажите Ваш номер телефона">
                            </div>
                            <div class="line">
                                <input type="submit" class="btn full btn-gray-dark" value="Оформить заказ">
                            </div>
                        </form>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

<div class="modal fade cityModal menu_modal_app" id="cityModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
                <?$APPLICATION->IncludeComponent(
                    "citfact:elements.list",
                    "app_list_cities_modal",
                    Array(
                        "IBLOCK_ID" => 20,
                        "PROPERTY_CODES" => array('OBLAST', 'MAIN'),
                        "CURRENT_CITY_ID" => $_SESSION["CITY_ID"],
                    )
                );?>
        </div>
    </div>
</div>

<div class="modal fade" id="chooseSizeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>

            <div class="modal-body">
                <div class="modal-title">
                    Выберите размер
                </div>
                <div>
                    Чтобы добавить товар в корзину, сначала выберите размер товара.
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="addCartMobile" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>

            <div class="modal-body">
                <div class="modal-title">
                    Товар добавлен в корзину.
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="noneCartMobile" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>

            <div class="modal-body">
                <div class="modal-title">
                    Данный товар уже добавлен в корзину.
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="chooseSizeModalForSubscribe" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <div class="modal-body">
                <div class="modal-title">
                    Выберите размер
                </div>
                <div>
                    Чтобы подписаться на товар, сначала выберите размер товара.
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="offerInbasketModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 99999;">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>

            <div class="modal-body">
                <div class="modal-title">
                    Товар уже есть в корзине
                </div>
                <div>
                    Вы уже добавили этот товар в корзину.
                </div>
            </div>

        </div>
    </div>
</div>

<div id="side-login" class="modal fade menu_modal_app" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 99999;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="moved-panel-wrap">
                <div class="moved-panel-head app_modal_head">
                    <h6>
                        Авторизация
                    </h6>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="moved-panel-body">
                    <div class="aside-box">
                        <div class="review-form app-no-margin">
                            <?if (!$USER->IsAuthorized()):?>
                                <?$APPLICATION->IncludeComponent(
                                    "citfact:authorize.ajax",
                                    "app",
                                    Array(
                                        "REDIRECT_TO" => '',
                                        "FORM_ID" => 'popup'
                                    )
                                );?>
                            <?endif;?>
                        </div>
                    </div>
                    <ul class="add-link">
                        <li>
                            <a class="close-modal" href="/paoloconte_app/forgotpassword/">Забыли пароль?</a>
                        </li>
                        <li>
                            <div class="menu-items">
                                <a class="close-modal" href="/paoloconte_app/register/">Регистрация</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="side-to-favorite" class="modal fade side-to-favorite app-styles-modal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 99999;">
    <div class="modal-dialog">
        <div class="modal-content">
    <div class="moved-panel-wrap">
        <div class="moved-panel-head app_modal_head">
            <h6>
                Товар добавлен в избранное
            </h6>
            <button type="button" class="close" data-dismiss="modal"></button>
        </div>
        <div class="moved-panel-body">
            <div class="aside-box">
                <a href="#">
                    <div class="image">
                        <img src="<?=SITE_TEMPLATE_PATH?>/images/content/catalog-item-<?echo rand(1,3);?>.jpg">
                    </div>
                </a>

                Товар добавлен в избранное.
                Вы можете найти его в соответствующем разделе “Желаемые товары” в личном кабинете.
                <div class="btn-box">

                    <a href="/paoloconte_app/cabinet/favorites/" class="btn btn-gray-dark full" <?/*data-type="getMovedPanel" data-target="#side-login"*/?>><span>Личный кабинет</span></a>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>
</div>

<? $APPLICATION->ShowViewContent('detail_add_review_block'); ?>

<? $APPLICATION->ShowViewContent('detail_subscribe_price_block'); ?>

<? $APPLICATION->ShowViewContent('detail_fast_order_block'); ?>

<!--end modals-->

</body>
</html>