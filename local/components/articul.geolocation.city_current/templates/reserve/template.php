<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<div>
    <?
    $this->setFrameMode(true);
    $frame = $this->createFrame()->begin('');
    ?>
    Ваш город: <?= $arResult["CURRENT"]["NAME"] ?>
    <? $frame->end(); ?>
</div>
