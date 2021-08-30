<?
use Citfact\CloudLoyalty\Events;
use Citfact\CloudLoyalty\OperationManager;

define('NEED_AUTH', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Мои бонусы");
global $USER;
$balance = Events::getBalance($USER->GetID(), true);
?><? if ($balance['client']['cardString']) { ?> <? $card = preg_replace('/(\d{4})(\d{4})(\d{4})(\d)/', '$1 $2 $3 $4', $balance['client']['cardString']) ?>
<div class="card">
	<div class="bonuses__name">
		Бонусная карта
	</div>
	<div class="btn btn--black">
		 <?= $card ?>
	</div>
</div>
 <? } ?>
<div class="bonuses">
	 <?
        $expiredStr = '-';
        $nearDate = null;
        foreach ($balance['bonuses'] as $arrBonus) {
            if ($arrBonus['expireAt']) {
                if (!$nearDate) {
                    $nearDate = DateTime::createFromFormat(DateTime::RFC3339, $arrBonus['expireAt']);
                    $expiredStr = $nearDate->format(\Bitrix\Main\Type\Date::getFormat())
                        . " сгорит " . \Citfact\Tools::declension(number_format($arrBonus['amount']), ['бонус', 'бонуса', 'бонусов']);
                } else {
                    $tmp = DateTime::createFromFormat(DateTime::RFC3339, $arrBonus['expireAt']);
                    if ($nearDate > $tmp) {
                        $nearDate = $tmp;
                        $expiredStr = $nearDate->format(\Bitrix\Main\Type\Date::getFormat())
                            . " сгорит " . \Citfact\Tools::declension(number_format($arrBonus['amount']), ['бонус', 'бонуса', 'бонусов']);
                    }
                }
            }
        }
        ?>
	<div class="bonuses__property">
		<div class="bonuses__name">
			 Доступно бонусов
		</div>
		<div class="bonuses__value">
			<?= number_format($balance['client']['bonuses'], 0, '', ' '); ?>
		</div>
	</div>
	<div class="bonuses__property">
		<div class="bonuses__name">
			Будет начислено бонусов
		</div>
		<div class="bonuses__value">
			<?= OperationManager::getPendingBonuses($USER->GetID()) ?>
		</div>
	</div>
	 <? if ($expiredStr) { ?>
	<div class="bonuses__property">
		<div class="bonuses__name">
			Ближайшая дата сгорания
		</div>
		<div class="bonuses__value">
 <span><?= $expiredStr ?> </span>
		</div>
	</div>
	 <? } ?>
</div>
<div class="bonuses-questions">
	<h4>Как получить бонусные баллы?</h4>
	<p>
		Бонусными баллами можно оплатить до 30% стоимости заказа.
	</p>
	<p>
		Кроме того, мы дарим баллы. Следите <a target="_blank" href="/events" class="link">за новостями</a>.
	</p>
	<p>
		Больше информации <a href="/help/club-paoloconte" class="link" target="_blank">в правилах</a> бонусной программы.
	</p>
	<h4>Как потратить?</h4>
	<p>
		Бонусными баллами можно оплатить до 30% стоимости заказа.
	</p>
	<p>
		1 балл = 1 рубль.
	</p>
	<h4>Как сохранить бонусную карту на телефоне?</h4>
	<p>
		Вы можете сохранить карту на телефоне, чтобы накопленные баллы всегда были под рукой.
	</p>
	<p>
		Для этого достаточно ввести номер карты в приложение Apple Wallet / Google Pay или нажать одну из кнопок:
	</p>
</div>
<div class="bonuses-btns">
 <a href="javascript:void(0);" class="btn btn--black"> <img src="/images/wallet_app.png" alt=""> <span>Добавить
	 в Apple Wallet</span> </a> <a href="javascript:void(0);" class="btn btn--black"> <img src="/images/gpay.png" alt="">
	Сохранить на телефоне </a>
</div><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>