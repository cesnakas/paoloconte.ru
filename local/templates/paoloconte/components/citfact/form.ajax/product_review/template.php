<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
//_c($arResult);
?>
<script>
	BX.message({
		TEMPLATE_PATH: '<? echo $this->__folder ?>',
		COMPONENT_PATH: '<? echo $this->__component->__path ?>',
		arParams_product_review: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>
<form action="#">
	<div class="errors_cont"></div>
	<div class="success_cont"></div>
	<?=bitrix_sessid_post()?>
	<input type="text" name="yarobot" value="" class="hide">
	<div class="line">
		<div>
			<b>ВЫ ЗАШЛИ КАК:</b> <?=$arParams['USER_NAME']?>
		</div>
		<div>
			<b>ВАШ E-MAIL:</b> <?=$arParams['USER_EMAIL']?>
		</div>
	</div>
	<div class="line">
		<a href="#" class="get-modal" data-toggle="modal" data-target="#enterModal">Сменить учетную запись</a>
	</div>
	<div class="clear-after modal-basket__info modal-basket__info--review">
		<?foreach ($arResult['SHOW_PROPERTIES'] as $key => $arProp){
			if ($arProp['PARAMS_TYPE'] == 'ready' && !empty($arProp['VALUE'])){
				$idElement = !empty($arProp['ID'])? 'id="'.$arProp['ID'].'"': '';?>
				<div class="ready_props"><?=$arProp['VALUE'];?></div>
				<input <?=$idElement?> class="<?=$arProp['CLASS']?>" type="hidden" name="<?=$arProp['CODE']?>" value="<?=$arProp['VALUE']?>"/>
				<?unset($arResult['SHOW_PROPERTIES'][$key]);
			}elseif($arProp['PARAMS_TYPE'] == 'img' && !empty($arProp['VALUE'])){
				$idElement = !empty($arProp['ID'])? 'id="'.$arProp['ID'].'"': '';?>
                <div class="modal-basket__img">
                    <img src="<?=$arProp['VALUE'];?>" class="ready_props" />
                </div>
				<input <?=$idElement?> class="<?=$arProp['CLASS']?>" type="hidden" name="<?=$arProp['CODE']?>" value="<?=$arProp['VALUE']?>"/>
				<?unset($arResult['SHOW_PROPERTIES'][$key]);
			}
		}?>
	</div>
	<?foreach ($arResult['SHOW_PROPERTIES'] as $arProp):?>
		<?
		$idElement = !empty($arProp['ID'])? 'id="'.$arProp['ID'].'"': '';
		?>
		<?if ($arProp['PARAMS_TYPE'] == 'textarea'):?>
			<div class="line line_relative">
				<div class="show_error"><?=$arProp['ERROR']?></div>
				<div class="show_length"><span>0</span> <p>символов</p></div>
				<textarea <?=$idElement?> class="<?=$arProp['CLASS']?>" name="<?=$arProp['CODE']?>" placeholder="<?=$arProp['PLACEHOLDER']?>" data-length_message="0"></textarea>
			</div>
		<?elseif($arProp['PARAMS_TYPE'] == 'hidden'):?>
			<input <?=$idElement?> class="<?=$arProp['CLASS']?>" type="hidden" name="<?=$arProp['CODE']?>" value="<?=$arProp['VALUE']?>"/>
		<?elseif($arProp['PARAMS_TYPE'] == 'stars'):?>
			<div class="line">
				<div class="rating_stars">
					<?for($i=1; $i<=$arProp['VALUE']; $i++):?>
						<?$checked = $i==4? 'checked' : ''?>
						<input type="radio" name="<?=$arProp['CODE']?>" title="<?=$i?>" value="<?=$i?>" class="<?=$arProp['CLASS']?>" <?=$checked?>/>
					<?endfor?>
				</div>
			</div>
		<?elseif($arProp['PARAMS_TYPE'] == 'checkbox'):?>
            <div class="b-checkbox">
                <label class="b-checkbox__label">
                    <input id="subscribe_me"
                           checked="checked"
                           type="checkbox"
                           class="b-checkbox__input"
                           name="<?=$arProp['CODE']?>"
                           value="<?=$arProp['VALUE']?>">
                    <span class="b-checkbox__box">
                        <span class="b-checkbox__line b-checkbox__line--short"></span>
                        <span class="b-checkbox__line b-checkbox__line--long"></span>
                    </span>
                    <span class="b-checkbox__text">Подписаться на новости об акциях и скидках</span>
                </label>
            </div>
		<?endif;?>
	<?endforeach?>
	<div class="modal__btns">
		<a href="#" id="product_review_submit" class="btn btn--black mode2">Отправить отзыв</a>
		<a href="#" class="link" data-dismiss="modal">Отмена</a>
	</div>
</form>

<script>
	$(document).ready(function () {
		var template_path = BX.message('TEMPLATE_PATH');
		var component_path = BX.message('COMPONENT_PATH');
		var arParams = BX.message('arParams_product_review');
		//console.log(arParams);
	
		$('.message_type').keyup(function(){
			$(this).parent().removeClass('error_mes');
			var lengthMes = $(this).val().length;
			if (lengthMes != 0) {
				$('.show_length').show().find('span').text(lengthMes);
				$('.show_length').find('p').text(wordend(lengthMes, 'символ', '', 'а', 'ов'));
			}else{
				$('.show_length').hide();
			}
		});

		$('#product_review_submit').click(function () {
			var form = $(this).parents('form');
			var data = {formdata: form.serialize(), params: arParams, sessid: $('input#sessid').val()};
			var result_cont = form.find('.success_cont');
			var errors_cont = form.find('.errors_cont');
			var mesinput = form.find('.message_type');
			$('.show_length').hide();
			if ( validate_ajax_form(form)) {
                mesinput.data('length_message', mesinput.val().length);
				if (mesinput.data('length_message') == 0 || mesinput.data('length_message') > 99) {
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
				}else{
					clearForm(form);
					mesinput.data('length_message', 0);
				}
			}
			else{
				// прокручиваем к первой ошибке
				//var pos = form.find('input.error:first').offset();
				//$('html,body').animate({scrollTop:pos.top-70},500);
				mesinput.data('length_message', mesinput.val().length);
			}
			return false;
		});
		
		function clearForm(form){
			form.find('input.required, textarea.required').each(function(){
				$(this).val('');
				$(this).removeClass('error').parent().removeClass('error_mes');
				$(this).parent().find('.tip-error').hide();
				$(this).parent().find('.tip-error-phone').hide();
			});
		}
		
		function validate_ajax_form(form){
			flag = true;
			form.find('input.required, textarea.required').each(function(){
				//console.log($(this).find('input').val());
				var input = $(this);
				var val = input.val();
				var placeholder = input.attr('placeholder');

				if (
					(input.hasClass('message_type') && val.length < 100) || // если сообщение, то не меньше 100 символов 
					val=='' || val==placeholder || val.length<3 ||
					(input.hasClass('phone') && val.length<6) // если телефон, то не меньше 6 символов
				) {
					$(this).addClass('error').parent().addClass('error_mes');
					$(this).parent().find('.label-alert').show();
					flag=false;
				} else {
					var re = /^[0-9- ]*$/;
					var re_email = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
					if (input.hasClass('phone') && !re.test(val)){
						$(this).addClass('error').parent().addClass('error_mes');
						flag=false;
					}
					else if (input.hasClass('email') && !re_email.test(val)){
						$(this).addClass('error').parent().addClass('error_mes');
						flag=false;
					}
					else{
						$(this).removeClass('error').parent().removeClass('error_mes');
						$(this).parent().find('.tip-error').hide();
						$(this).parent().find('.tip-error-phone').hide();
					}
				}
			});

			//console.log(flag);
			return flag;
		}
		
		function wordend(number, word, end1, end2, end3){
			if(Number(String(number).length) > 2) number = Number(String(number).substr(-2));
			if(number == 11 || number == 12 || number == 13 || number == 14){
				return word+end3;
			} else {
				switch(Number(String(number).substr(-1))){
					case 1: return word+end1; break;
					case 2:case 3:case 4: return word+end2; break;
					case 5:case 6:case 7:case 8:case 9:case 0: return word+end3; break;
				}
			}
		}


	}); // end document ready
</script>