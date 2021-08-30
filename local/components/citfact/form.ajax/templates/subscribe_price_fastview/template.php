<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<script>
	BX.message({
		TEMPLATE_PATH: '<? echo $this->__folder ?>',
		COMPONENT_PATH: '<? echo $this->__component->__path ?>',
		arParams_subscribe_price: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>


<div class="title-1">
    Узнать о снижении цены
</div>
<div class="modal-text">
    Вы получите письмо, когда стоимость станет ниже указанной вами цены.
</div>
<form action="#">
	<?=bitrix_sessid_post()?>
	<input type="text" name="yarobot" value="" class="hide">

	<div class="form-box">
		<?foreach ($arResult['SHOW_PROPERTIES'] as $arProp):?>
			<div class="form__item">
				<?if ($arProp['PARAMS_TYPE'] == 'text'):?>
					<input type="text" name="<?=$arProp['CODE']?>" value="<?=$arProp['VALUE']?>" class="<?=$arProp['REQUIRED'] == 'Y'? 'required':''?> <?=$arProp['CODE'] == 'USERPHONE'? 'mask-phone':''?> <?=$arProp['CODE'] == 'EMAIL'? 'email':''?> <?=$arProp['CODE'] == 'PRICE'? 'mask-price':''?>" placeholder="<?=$arProp['PLACEHOLDER']?>"/>
				<?elseif($arProp['PARAMS_TYPE'] == 'textarea'):?>
					<textarea name="<?=$arProp['CODE']?>" cols="30" rows="10" class="<?=$arProp['REQUIRED'] == 'Y'? 'required':''?>" placeholder="<?=$arProp['PLACEHOLDER']?>"></textarea>
				<?elseif ($arProp['PARAMS_TYPE'] == 'hidden'):?>
					<input type="hidden" name="<?=$arProp['CODE']?>" class="<?=$arProp['REQUIRED'] == 'Y'? 'required':''?>" value="<?=$arProp['VALUE']?>"/>
				<?endif;?>
			</div>
		<?endforeach?>
        
        <div class="b-checkbox">
            <label class="b-checkbox__label">
                <input id="a1" type="checkbox" name="SUBSCRIBE" value="Y" class="b-checkbox__input">
                <span class="b-checkbox__box">
                    <span class="b-checkbox__line b-checkbox__line--short"></span>
                    <span class="b-checkbox__line b-checkbox__line--long"></span>
                </span>
                <span class="b-checkbox__text">Новинки, акции и эксклюзивные предложения</span>
            </label>
        </div>

        <div class="modal-pp">
            <a href="#" id="subscribe_price_submit" class="btn btn-gray-dark">Отправить заявку</a>
            <div class="modal-pp__text">
                <?$APPLICATION->IncludeFile(
                    SITE_DIR."/include/oferta_application.php",
                    Array(),
                    Array("MODE"=>"text")
                );?>
            </div>
        </div>

		<div class="errors_cont"></div>
		<div class="success_cont"></div>
	</div>
</form>

<script>
	$(document).ready(function () {
		var template_path = BX.message('TEMPLATE_PATH');
		var component_path = BX.message('COMPONENT_PATH');
		var arParams_subscribe_price = BX.message('arParams_subscribe_price');

		$('#subscribe_price_submit').click(function () {
			var form = $(this).parents('form');
			var data = {formdata: form.serialize(), params: arParams_subscribe_price, sessid: $('input#sessid').val()};
			var result_cont = form.find('.success_cont');
			var errors_cont = form.find('.errors_cont');
			if ( validate_ajax_form(form) ) {
				$.ajax({
					type: "POST",
					url: component_path + "/ajax.php",
					data: data,
					success: function (data) {
						var json = JSON.parse(data);
						result_cont.html('');
						errors_cont.html('');
						if (json.errors.length > 0) {
							for (var key in json.errors) {
								errors_cont.append('<p class="">' + json.errors[key] + '</p>');
							}
							errors_cont.hide().fadeIn();
						}
						else {
							for (var key in json.result) {
								result_cont.append('<p class="">' + json.result[key] + '</p>');
							}
							result_cont.hide().fadeIn();
							form[0].reset();
							//window.location.reload();
						}
					}
				});
			}
			else{
				// прокручиваем к первой ошибке
				//var pos = form.find('input.error:first').offset();
				//$('html,body').animate({scrollTop:pos.top-70},500);
			}
			return false;
		});


		function validate_ajax_form(form){
			flag = true;
			form.find('input.required, textarea.required').each(function(){
				//console.log($(this).find('input').val());
				var input = $(this);
				var val = input.val();
				var placeholder = input.attr('placeholder');

				if (val=='' || val==placeholder || val.length<3
						// если телефон, то не меньше 6 символов
					|| (input.hasClass('phone') && val.length<6)) {
					$(this).addClass('error');
					$(this).parent().find('.label-alert').show();
					flag=false;
				}
				else {
					var re = /^[0-9- ]*$/;
					var re_email = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
					if (input.hasClass('phone') && !re.test(val)){
						$(this).addClass('error');
						flag=false;
					}
					else if (input.hasClass('email') && !re_email.test(val)){
						$(this).addClass('error');
						flag=false;
					}
					else{
						$(this).removeClass('error'); $(this).parent().find('.tip-error').hide(); $(this).parent().find('.tip-error-phone').hide();
					}
				}
			});

			//console.log(flag);
			return flag;
		}

	}); // end document ready
</script>