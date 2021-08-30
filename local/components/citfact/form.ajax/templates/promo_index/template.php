<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<?$this->setFrameMode(true);?>
<script>
    BX.message({
        TEMPLATE_PATH: '<? echo $this->__folder ?>',
        COMPONENT_PATH: '<? echo $this->__component->__path ?>',
        arParams_promo_index: <?=CUtil::PhpToJSObject($arParams)?>
    });
</script>
<div class="deal-item">
    <div class="deal-body">
        <form action="#" id="citfact_form_promo_index">
            <?=bitrix_sessid_post()?>
            <input type="text" name="yarobot" value="" class="hide">

            <div class="item-head">
                <div class="title">
                    Подпишитесь&nbsp;&mdash;
                </div>
                <div class="tite-desc">
                    и получите в подарок 300 рублей
                </div>
            </div>
            <div class="item-body">
                <input type="hidden" name="FROM_PROMO" value="Y" />
                <input type="hidden" name="HASH" value="<?= $this->randString(10); ?>" />
                <input type="text" name="EMAIL" class="required email" placeholder="Введите Ваш e-mail" />
            </div>

            <div class="item-footer">
                <a href="#" id="promo_index_submit" class="btn btn-gold full small mode2 icon-arrow-right">Отправить</a>
                <div class="oferta-mini">
                    <?$APPLICATION->IncludeFile(
                        SITE_DIR."/include/oferta_send.php",
                        Array(),
                        Array("MODE"=>"text")
                    );?>
                </div>
                <div class="errors_cont"></div>
                <div class="success_cont"></div>
            </div>
        </form>
    </div>
</div>