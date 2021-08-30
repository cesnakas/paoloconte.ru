<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<div class="coupons-wrap">
	<?if (!empty($arResult['ITEMS'])):?>
		<?foreach($arResult['ITEMS'] as $arItem) { ?>
			<?
			$tarif = '';
			if ($arItem['ONE_TIME'] == 'Y'){
				$tarif = 'Одноразовый, на одну позицию заказа.';
			}
			elseif ($arItem['ONE_TIME'] == 'O'){
				$tarif = 'Одноразовый, на весь заказ.';
			}
			else {
				$tarif = 'Многоразовый';
			}
			?>
			<div class="item">
				Промо-код купона: <?=$arItem['COUPON']?><br>
				Ценность: <?=$arItem['VALUE']?><br>
				<?/*Действительность: до 15.12.2014<br>*/?>
				Тариф: <?=$tarif?>
				<div class="status">
					<?
					$text=' <span class="round-label true"><i class="fa fa-check"></i></span> Действителен';
					/*switch (rand(1,2)) {
						case 1:
							$text=' <span class="round-label true"><i class="fa fa-check"></i></span> Действителен';
							break;
						case 2:
							$text='<span class="round-label false"><i class="fa fa-times"></i></span> Не действителен';
							break;
					}*/
					echo $text;
					?>
				</div>
				<div class="desc">
					Скидка не действует на товары по акции!
				</div>
			</div>
		<? } ?>
	<?else:?>
		У вас еще нет купонов.
	<?endif?>
</div>