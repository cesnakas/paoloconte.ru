<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
?>
<script>
	BX.message({
		TEMPLATE_PATH: '<? echo $this->__folder ?>',
		COMPONENT_PATH: '<? echo $this->__component->__path ?>',
		arParams_fast_order: <?=CUtil::PhpToJSObject($arParams)?>
	});
</script>

<div class="emulate-cell  valign-bottom image">
	<img src="<?=$arParams['PRODUCT_IMAGE']?>">
</div>
<div class="emulate-cell valign-top form">
    <div class="modal-title-desc-form">
        Достаточно заполнить два поля
    </div>
	<form action="#" class="form">
		<?=bitrix_sessid_post()?>
		<input type="text" name="yarobot" value="" class="hide">
		<?foreach ($arResult['SHOW_PROPERTIES'] as $arProp):?>
			<?
			$idElement = !empty($arProp['ID'])? 'id="'.$arProp['ID'].'"': '';
			?>
			<?if ($arProp['PARAMS_TYPE'] == 'text'):?>
				<div class="form__item">
					<input <?=$idElement?> type="text" name="<?=$arProp['CODE']?>" class="<?=$arProp['CLASS']?> <?=$required?> <?=$phonemask?>" placeholder="<?=$arProp['PLACEHOLDER']?>">
				</div>
			<?elseif($arProp['PARAMS_TYPE'] == 'hidden'):?>
				<input <?=$idElement?> type="hidden" name="<?=$arProp['CODE']?>" value="<?=$arProp['VALUE']?>" class="<?=$arProp['CLASS']?> <?=$required?>"/>
			<?endif;?>
		<?endforeach?>

        <div class="modal-pp">
            <a href="#" id="fast_order_submit" class="btn btn--black fast-order" onclick="yaCounter209275.reachGoal('Quick_order');"><span>Оформить заказ</span></a>
            <div class="modal-pp__text">
                <?$APPLICATION->IncludeFile(
                    SITE_DIR."/include/oferta_order.php",
                    Array(),
                    Array("MODE"=>"text")
                );?>
            </div>
        </div>
        
        <div class="errors_cont"></div>
		<div class="success_cont"></div>
	</form>
</div>
 
<script>
	$(document).ready(function () {
		var template_path = BX.message('TEMPLATE_PATH');
		var component_path = BX.message('COMPONENT_PATH');
		var arParams = BX.message('arParams_fast_order');
		//console.log(arParams);

		$('#fast_order_submit').click(function () {
			var form = $(this).parents('form');
			var data = {formdata: form.serialize(), params: arParams, sessid: $('input#sessid').val()};
			var result_cont = form.find('.success_cont');
			var errors_cont = form.find('.errors_cont');
			var id_el = GetQueryStringParams(data.formdata, 'PRODUCT_ID');
			var price = GetQueryStringParams(data.formdata, 'PRICE');
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
							var order_id = new Date().getTime() + '' + Math.floor((Math.random() * 1000) + 1);
							(window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
								try {
									rrApi.order({
										transaction: order_id,
										items: [{
											id: id_el,
											qnt: 1,
											price: price
										}]
									});
								} catch (e) {}
							});
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
		
		if (typeof GetQueryStringParams != 'function')
			function GetQueryStringParams(sPageURL, sParam){
				var sURLVariables = sPageURL.split('&');
				for (var i = 0; i < sURLVariables.length; i++){
					var sParameterName = sURLVariables[i].split('=');
					if (sParameterName[0] == sParam){
						return sParameterName[1];
					}
				}
			}

	}); // end document ready
</script>