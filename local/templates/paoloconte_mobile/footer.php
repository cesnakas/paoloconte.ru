<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>
</div>
</section>
</main>
<footer>
    <div class="container">

        <div class="social-box align-center">
            Мы в социальных сетях:
            <div class="items">
                <? $APPLICATION->IncludeFile(SITE_DIR . "include/footer_social_links.php", Array(), Array("MODE" => "text")); ?>
            </div>
        </div>
        <a href="<?= URL_FULL_VERSION ?><?= $APPLICATION->GetCurDir() ?>?m=0">
            Полная версия сайта
        </a>
    </div>
</footer>

</div>
</div>


<!--modals-->

<div class="modal fade fastOrderModal" id="fastOrderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
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

<div class="modal fade cityModal" id="cityModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <? $APPLICATION->IncludeComponent("citfact:elements.list", "mobile_list_cities_modal", Array("IBLOCK_ID" => 20, "PROPERTY_CODES" => array('OBLAST', 'MAIN'), "CURRENT_CITY_ID" => $_SESSION["CITY_ID"],)); ?>
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
                <br/>
                <div>
                    <a class="btn btn-green full" href="/cabinet/basket/">Перейти в корзину</a>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="addToBasketModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 99999;">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>

            <div class="modal-body">
                <div class="modal-title">
                    Добавлен в корзину
                </div>
                <ul class="add-link">
                    <li style="border:0;">
                        <a style="text-decoration:none;" href="/cabinet/basket/">ПЕРЕЙТИ В КОРЗИНУ</a>
                    </li>
                    <br>
                    <li>
                        <a href="" style="text-decoration:none;" data-dismiss="modal">ВЕРНУТЬСЯ К ТОВАРУ</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!--end modals-->
<? /*<script type="text/javascript">
	var gaq = gaq || [];
	_gaq.push(['_setAccount', 'UA-10107368-1']);
	_gaq.push(['_trackPageview']);
	_gaq.push(['_addOrganic', 'images.yandex.ru', 'q', true]);
	// Поиск по блогам
	_gaq.push(['_addOrganic', 'blogsearch.google.ru', 'q', true]);
	_gaq.push(['_addOrganic', 'blogs.yandex.ru', 'text', true]);
	// Поисковики России
	_gaq.push(['_addOrganic', 'go.mail.ru', 'q']);
	_gaq.push(['_addOrganic', 'nova.rambler.ru', 'query']);
	_gaq.push(['_addOrganic', 'nigma.ru', 's']);
	_gaq.push(['_addOrganic', 'webalta.ru', 'q']);
	_gaq.push(['_addOrganic', 'aport.ru', 'r']);
	_gaq.push(['_addOrganic', 'poisk.ru', 'text']);
	_gaq.push(['_addOrganic', 'km.ru', 'sq']);
	_gaq.push(['_addOrganic', 'liveinternet.ru', 'ask']);
	_gaq.push(['_addOrganic', 'quintura.ru', 'request']);
	_gaq.push(['_addOrganic', 'search.qip.ru', 'query']);
	_gaq.push(['_addOrganic', 'gde.ru', 'keywords']);
	_gaq.push(['_addOrganic', 'gogo.ru', 'q']);
	_gaq.push(['_addOrganic', 'ru.yahoo.com', 'p']);
</script>*/ ?>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">(function (d, w, c) {
        (w[c] = w[c] || []).push(function () {
            try {
                w.yaCounter209275 = new Ya.Metrika({
                    id: 209275,
                    webvisor: true,
                    clickmap: true,
                    trackLinks: true,
                    accurateTrackBounce: true,
                    triggerEvent: true
                });
            } catch (e) {
            }
        });
        var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () {
            n.parentNode.insertBefore(s, n);
        };
        s.type = "text/javascript";
        s.async = true;
        s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";
        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else {
            f();
        }
    })(document, window, "yandex_metrika_callbacks");</script>
<noscript>
    <div><img src="//mc.yandex.ru/watch/209275" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript><!-- /Yandex.Metrika counter -->

</body>
</html>