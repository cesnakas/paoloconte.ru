<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>

<a href="/cabinet/basket/" class="header-cart__link">
    <svg class='i-icon'>
        <use xlink:href='#cart'/>
    </svg>
</a>
<a href="/cabinet/basket/">
    <span>Корзина</span>
</a>

<? $frame = $this->createFrame()->begin(''); ?>
<div class="header-m-cart__count">
    <?= $arResult['NUM_READY'] ?>
</div>
<? $frame->end(); ?>
