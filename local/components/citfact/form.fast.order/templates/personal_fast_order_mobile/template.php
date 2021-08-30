<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<? \Bitrix\Main\Localization\Loc::loadMessages(__FILE__); ?>
<? if (!$arResult['BASKET_EMPTY']) { ?>
    <? $idTemplate = 'form'.$this->randString(); ?>
    <? $wrapForm = 'wrap'.$this->randString(); ?>
    <div class="bx_section personal-box form" id="<?= $wrapForm; ?>">
        <div class="errors_cont"></div>
        <div class="success_cont"></div>
        <form action="#" id="<?= $idTemplate; ?>">
            <?=bitrix_sessid_post()?>
            <input type="hidden" name="<?= $arParams['ID_COMPONENT']; ?>" value="Y" class="hide">
            <input type="text" name="yarobot" value="" class="hide">
            <div class="line">
                <input type="text" name="NAME" class="required" placeholder="Введите ваше имя">
            </div>
            <div class="line">
                <input type="tel" name="PHONE" class="mask-phone required" placeholder="Введите ваш телефон">
            </div>
            <div class="line">
                <a href="#" onclick="return formFastOrder($(this));" id="fast_order_submit" class="btn big btn-gray-dark mode2 icon-arrow-right full">Оформить заказ</a>
            </div>
        </form>
    </div>
    <script>
        BX.message({
            WRAP_FORM_<?= $idTemplate; ?>: '<? echo $wrapForm; ?>',
            TEMPLATE_PATH_<?= $idTemplate; ?>: '<? echo $this->__folder; ?>',
            COMPONENT_PATH_<?= $idTemplate; ?>: '<? echo $this->__component->__path; ?>',
            PARAMS_<?= $idTemplate; ?>: <?= CUtil::PhpToJSObject($arParams); ?>,
            SUCCESS_ORDER_MESSAGE_<?= $idTemplate; ?>: "<?= \Bitrix\Main\Localization\Loc::getMessage('CFOP_SUCCESS_ORDER'); ?>",
        });
    </script>
<? } ?>