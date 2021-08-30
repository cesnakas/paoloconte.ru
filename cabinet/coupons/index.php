<?define('NEED_AUTH', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("tags", "промокод, купон, скидка");
$APPLICATION->SetPageProperty("keywords", "женская обувь, мужская обувь, аксессуары, цена, скидка, распродажа, купон, промо-код, промокод");
$APPLICATION->SetPageProperty("description", "Ваши скидочные купоны и промо-коды на покупку обуви и аксессуаров по выгодной цене в интернет-магазине Paolo Conte.");
$APPLICATION->SetTitle("Личный кабинет - купоны");
?>

            <div class="cabinet-wrap">
                Бонусные купоны позволяют Вам делать покупки по сниженным ценам в интернет-магазине Paolo Conte.
                <br>
                <br>
                <ul>
                    <li>Размер скидки — номинал купона, указанный в рублях.</li>
                    <li>Для того чтобы воспользоваться скидкой, активируйте Ваш купон. </li>
                    <li>Введите соответствующий промо-код и совершите выгодную покупку.</li>
                </ul> <br>
                Также Вы можете делать покупки по приятным ценам, участвуя в наших акциях и распродажах. Следите за <a href="http://paoloconte.ru/events/">событиями</a> на сайте. Узнавайте в <a href="http://paoloconte.ru/socialnye-sety/">соцсетях</a>!<br>
                <?$APPLICATION->IncludeComponent(
                    "citfact:coupons.list",
                    "cabinet",
                    Array(
                    )
                );?>
            </div>
    
    
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>