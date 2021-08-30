<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<?
use Bitrix\Main\Page\Asset;
Asset::getInstance()->addCss($componentPath . "/style.css");
Asset::getInstance()->addJs($componentPath . "/clipboard.js");
?>

<div class="coupons-wrap">
	<?if (!empty($arResult['ITEMS'])):?>
		Поздравляем! <br>
        У Вас есть купон(ы) на получение скидки при оформлении заказа.
<br><br>
		<?foreach($arResult['ITEMS'] as $arItem) { ?>
			<?
			$tarif = '';
			if ($arItem['ONE_TIME'] == 'Y'){
				$tarif = 'Одноразовый, на одну позицию заказа.';
			} elseif ($arItem['ONE_TIME'] == 'O'){
				$tarif = 'Одноразовый, на весь заказ.';
			} elseif ($arItem['TYPE'] == 2) {
				$tarif = 'Одноразовый, на весь заказ.';
			} elseif ($arItem['TYPE'] == 1) {
				$tarif = 'Одноразовый, на одну позицию заказа.';
			} else {
				$tarif = 'Многоразовый';
			}
			?>
			<div class="item">
<?if ($arItem['VALUE']):?>
                Размер скидки: <?=$arItem['VALUE']?><br>
                <?endif?>
                <?if($arItem["ACTIVE_TO"]):?>
                <?$dateExpired = $arItem["ACTIVE_TO"]->toString();?>
                <?$dateExpired = substr($dateExpired, 0, strpos($dateExpired, " "));?>
                Действует до: <?=$dateExpired?><br>
                <?endif?>
                
                Статус: 
					<?if($arItem["ACTIVE"]=="Y"):?>
					Можно использовать<br>
                    <?elseif($arItem["ACTIVE"]=="X"):?>
                    Просрочен<br>
                    <?else:?>
                    Использован<br>
                    <?endif?>
                
                Тип купона: <?=$tarif?><br>
                
				Промокод купона: 
                <span id="container<?=$arItem['COUPON']?>" onclick="CopyToClipboard('<?=$arItem['COUPON']?>')" class="promo-item<?if($arItem["ACTIVE"] != "Y"):?> inactive<?endif?>"><?=$arItem['COUPON']?>
                <span class="promo-tooltip"></span>
                </span><br>
			</div>
		<?endforeach?>
	<?else:?>
		У вас еще нет купонов.
	<?endif?>
</div>