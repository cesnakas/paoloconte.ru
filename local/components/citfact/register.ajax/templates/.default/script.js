$(document).ready(function () {
    var template_path = BX.message('TEMPLATE_PATH_CITFACT_REGISTER_AJAX');
    var arParams = BX.message('arParams');
    //console.log(arParams);

    $('#fact_register_submit').click(function () {
        var form = $('form[name="fact_form_register"]');
        var data_send = form.serialize();
        var result_cont = $('.reg_result_cont');
        var errors_cont = $('.reg_errors_cont');
        if ( validate(form) ) {
            $.ajax({
                type: "POST",
                url: template_path + "/ajax.php",
                data: data_send,
                success: function (data) {
                    var json = JSON.parse(data);
                    if(json.result && json.result.captcha) {
                        var captchaurl = $("[data-capcha-image]").data("capcha-image") + json.result.captcha;
                        $("[data-capcha-hidden]").val(json.result.captcha);
                        $("[data-capcha-image]").attr("src", captchaurl);
                    }

                    result_cont.html('');
                    errors_cont.html('');
                    if (json.errors.length > 0) {
                        for (var key in json.errors) {
                            errors_cont.append('<p class="red">' + json.errors[key] + '</p>');
                        }
                        errors_cont.hide().fadeIn();
                    }
                    else {
                        for (var key in json.result) {
                            result_cont.append('<p class="">' + json.result[key] + '</p>');
                        }
						var email = GetQueryStringParams(data_send, 'EMAIL');
						if (validEmailTest(email)) {
							(window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {rrApi.setEmail(email);});
						}
                        window.location.reload();
                        //result_cont.hide().fadeIn();
                    }
                }
            });
        }
        else{
            // прокручиваем к первой ошибке
            var pos = form.find('input.error:first').offset();
            $('html,body').animate({scrollTop:pos.top-70},500);
        }
        return false;
    });


    function validate(form){
        flag = true;
        $('#label_PERSONAL_DATA').css('color', 'black');
        form.find('input.required').each(function(){
            var input = $(this);
            var val = input.val();
            var placeholder = input.attr('placeholder');
            if ( val=='' || val==placeholder
                && (val.length<3 || input.context.type == 'checkbox')
                // если телефон, то не меньше 6 символов
                || (input.hasClass('phone') && val.length<6)) {
                    $(this).addClass('error');
                    $(this).parent().find('.label-alert').show();
                    flag=false;
            }
            else {
                var re = /^[0-9- ]*$/;
                if (input.hasClass('phone') && !re.test(val)){
                    $(this).addClass('error');
                    flag=false;
                }
                else if (input.hasClass('email') && !validEmailTest(val)){
                    $(this).addClass('error');
                    flag=false;
                }
                else if(input.hasClass('datepicker-here') && val.includes('_')) {
                    $(this).addClass('error');
                    flag=false;
                }
                else if (input.context.type == 'checkbox' && input.context.checked == false){
                    $(this).addClass('error');
                    flag=false;
                    $('#label_PERSONAL_DATA').css('color', 'red');
                }
                else{
                    $(this).removeClass('error'); $(this).parent().find('.tip-error').hide(); $(this).parent().find('.tip-error-phone').hide();
                }
            }
        });
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
	
if (typeof validEmailTest != 'function')	
	function validEmailTest(mail){
		var re_email = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		if (!re_email.test(mail)) {
			return false;
		}else{
			return true;
		}
	}
	
}); // end document ready