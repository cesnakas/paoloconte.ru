<?
define('NEED_AUTH', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Личный кабинет покупателя Paolo Conte: корзина, оформление заказа, купоны на скидку, ожидаемые товары.");
$APPLICATION->SetPageProperty("keywords", "женская обувь, мужская обувь, оформить заказ, купить обувь, заказать обувь, интернет-магазин, корзина, купоны на скидку, личный кабинет");
$APPLICATION->SetTitle("Личный кабинет");
?><div class="cabinet-wrap">
	<div class="top-text"id="top-text-id">
        <?  Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("top-text-id");?>
		    Рады приветствовать Вас, Вы зарегистрированы в системе под логином <?=$USER->GetLogin();?>
        <? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("top-text-id", "");?>
        <br><br>
		<img style="width:1024px;max-height:447px;" alt="personal-cabinet.jpg" src="/upload/medialibrary/ff7/personal-cabinet.jpg" title="personal-cabinet.jpg"><br>
	</div>
	<div class="image">
 <br>
	</div>
	<div data-retailrocket-markup-block="5677d98b9872e525b08896a4"></div>
</div>
<br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>