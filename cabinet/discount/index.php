<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет - скидка");
?>
<div class="cabinet-wrap">

	<div class="discount-wrap" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/content/discount.png')">
		<div class="number">
			№ карты: 6582 0320 3948 2342 <a href="#">Изменить</a>
		</div>
		<div class="value">
			Ваша скидка составляет: 9%
		</div>
		<div class="date">
			Последнее обновление:  12 января в 19:00
		</div>

		<div class="btn-box">
			<a href="#" class="btn btn-gray-dark mode2 small icon-arrow-right">Проверить сейчас</a>
		</div>

		<div class="desc">

			<i class="fa fa-question-circle"></i> <a href="#">Правила применения скидки</a>
		</div>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>