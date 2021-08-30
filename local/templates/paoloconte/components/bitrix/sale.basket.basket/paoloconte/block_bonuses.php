<?php

use Citfact\CloudLoyalty\DataLoyalty;
use Citfact\CloudLoyalty\Events;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (array_key_exists('maxToApplyForThisOrder', $bonusData) &&
    $bonusData['maxToApplyForThisOrder'] > 0 || !$USER->IsAuthorized()) {

    if ($USER->IsAuthorized()) {
        if ($checked) {
            $blockBonusesTextBtn = 'Отменить списание';
            $blockBonusesText1 = 'Списано';
            $blockBonusesClassBtn = 'btn--black';
        } else {
            $blockBonusesTextBtn = 'Cписать бонусы';
            $blockBonusesText1 = 'Доступно для списания';
            $blockBonusesClassBtn = 'btn--black';
        }
    } else {
        $blockBonusesText1 = 'Списать бонусы';
    }
    ?>
    <div class="block-bonuses cb-checkbox" data-onload-content>
        <div class="block-bonuses__title title-4">Клуб Paolo conte</div>
        <div class="block-bonuses__body">
            <div class="block-bonuses__subtitle">
                <?= $blockBonusesText1 ?>
            </div>
            <? if ($USER->IsAuthorized()) { ?>
                <div class="order-errors-cont errors_cont" id="CLerrorContainer"></div>
                <div class="block-bonuses__inner">
                    <span class="block-bonuses__value">
                        <span><?= number_format(DataLoyalty::getInstance()->getCloudScoreApplied(), 0, '', ' ') ?> Б</span>
                        <span> из <?= number_format(Events::getBalanceForPage($USER->GetID()), 0, '', ' ') ?></span>
                    </span>
                    <label class="b-checkbox__label<?= $disabled ?>">
                        <input type="checkbox" id="need_bonus_false" name="need_bonus_false"
                               class="b-checkbox__input"<?= $checked ?><?= $disabled ?>>
                        <span class="btn <?= $blockBonusesClassBtn ?>"><?= $blockBonusesTextBtn ?></span>
                    </label>
                </div>
                <div class="block-bonuses__note">*Бонусами можно оплатить до 30% стоимости товаров. 1 бонус = 1 рубль.</div>
                <a class="block-bonuses__link" href="/help/club-paoloconte/">Подробнее о бонусной программе</a>
            <? } else { ?>
                <span class="block-bonuses__auth">
                    <a href="#" data-toggle="modal" data-target="#enterModal">Авторизуйтесь</a>
                    для того, чтобы воспользоваться бонусами.
                </span>
            <? } ?>
        </div>
    </div>
    <?php
}
