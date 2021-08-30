<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
?>
<script>
	BX.message({
		TEMPLATE_PATH_fast_full_order: '<? echo $this->__folder ?>',
		COMPONENT_PATH_fast_full_order: '<? echo $this->__component->__path ?>',
		arParams_fast_full_order: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>

<div class="bx_section personal-box form personal-fast-order" id="fast_full_order_form">
    <div class="fast_full_order_form_disabled"></div>
	<form action="#">
        <div class="errors_cont"></div>
        <div class="success_cont"></div>

		<?=bitrix_sessid_post()?>
		<input type="text" name="yarobot" value="" class="hide">
		<? foreach ($arResult['SHOW_PROPERTIES'] as $arProp) {
			$idElement = !empty($arProp['ID']) ? 'identificator'.$arProp['ID'] : ''; ?>
			<?
            switch ($arProp['PARAMS_TYPE']) {
                case 'text': ?>
                    <div class="line">
                        <input id="<?=$idElement?>" type="text" name="<?=$arProp['CODE']?>" class="style2 <?=$arProp['CLASS']?>" placeholder="<?=$arProp['PLACEHOLDER']?>">
                    </div>
                    <? break;
                case 'tel': ?>
                    <div class="line">
                        <input id="<?=$idElement?>" type="tel" name="<?=$arProp['CODE']?>" class="style2 <?=$arProp['CLASS']?>" placeholder="<?=$arProp['PLACEHOLDER']?>">
                    </div>
                    <? break;
                case 'checkbox': ?>
                    <div class="line check-box">
                        <input id="<?=$idElement?>" type="checkbox" name="<?=$arProp['CODE']?>" class="style2 <?=$arProp['CLASS']?>" value="Y">
                        <label for="<?=$idElement?>">
                            Нажатием на кнопку «Оформить заказ» я даю согласие на обработку персональных данных в соответствии с указанными <a href="/help/dogovor-oferty/" style="color: #e4b461; margin: 0;" target="_blank">здесь</a> условиями.
                        </label>
                    </div>
                    <? break;
                case 'hidden': ?>
                    <input id="<?=$idElement?>" type="hidden" name="<?=$arProp['CODE']?>" value="<?=$arProp['VALUE']?>" class="<?=$arProp['CLASS']?>"/>
                    <? break;
            }
		} ?>
		<div class="line">
			<a href="#" id="fast_full_order_submit" class="btn full btn-gray-dark">Оформить заказ</a>
		</div>
	</form>
</div>
