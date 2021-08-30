window.yaCounterName = 'yaCounter209275'; // название переменной

window.yaCounterInited = false;
jQuery(document).on( window.yaCounterName.toLowerCase() + 'inited', function () {
    window.yaCounterInited = true;
    window.yaCounterObject = window[window.yaCounterName]; // объект счетчика
    window.yaCounterObject.reachGoal('MOBILE_FORM_VIEW_ORDER');  // просмотр страницы
});


$(document).ready(function () {
    var MobileOrderIsChangeFormYandex = false;
    var MobileOrderIsChangeFormGA = false;
    var MobileOrderIsPhoneCompletedYandex  = false;
    var MobileOrderIsPhoneCompletedGA = false;

    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        "event": "mobileOrderView"
    });

    var jqSelectInputs = $('#fast_full_order_form input');
    jqSelectInputs.on('change', setChangeOrder);
    jqSelectInputs.on('keypress', setChangeOrder);
    function setChangeOrder() {
        var input = $(this);
        var type = input.attr('type');

        if (!MobileOrderIsChangeFormYandex && window.yaCounterInited) {
            MobileOrderIsChangeFormYandex = true;
            window.yaCounterObject.reachGoal('MOBILE_FORM_ORDER_START_CHANGE');
        }

        if (!MobileOrderIsChangeFormGA) {
            MobileOrderIsChangeFormGA = true;
            window.dataLayer.push({
                "event": "mobileOrderChange"
            });
        }

        if (
            type == 'tel'
            && input.inputmask("isComplete")
        ) {
            if (!MobileOrderIsPhoneCompletedYandex && window.yaCounterInited) {
                MobileOrderIsPhoneCompletedYandex = true;
                window.yaCounterObject.reachGoal('MOBILE_FORM_ORDER_PHONE_DONE');
            }
            if (!MobileOrderIsPhoneCompletedGA) {
                MobileOrderIsPhoneCompletedGA = true;
                window.dataLayer.push({
                    "event": "mobileOrderPhoneDone"
                });
            }
        }
    }


    if (typeof BX.message['arParams_fast_full_order'] == "undefined") {
        return;
    }
    var template_path = BX.message('TEMPLATE_PATH_fast_full_order');
    var component_path = BX.message('COMPONENT_PATH_fast_full_order');
    var arParams = BX.message('arParams_fast_full_order');

    $('#fast_full_order_submit').click(function () {
        var wait = BX.showWait('fast_full_order_form');
        var disabled = $('.fast_full_order_form_disabled');
        disabled.show();
        disabled.width($('#fast_full_order_form').width());
        disabled.height($('#fast_full_order_form').height());

        var form = $(this).parents('form');

        var couponValue = !!BX('coupon') ? BX('coupon').value : "";
        var couponField = form.find('[name="COUPON"]');
        couponField.val(couponValue);

        var data = {formdata: form.serialize(), params: arParams, sessid: $('input#sessid').val()};
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

                    if (typeof(json.REDIRECT) != 'undefined' && json.REDIRECT.length > 0) {
                        if (window.yaCounterInited) {
                            window.yaCounterObject.reachGoal('MOBILE_FORM_ORDER_SUCCESS');
                        }
                        window.dataLayer.push({
                            "event": "mobileOrderSuccess"
                        });
                        window.location = json.REDIRECT;
                    } else {
                        if (json.errors.length > 0) {
                            for (var key in json.errors) {
                                errors_cont.append('<p class="">' + json.errors[key] + '</p>');
                            }
                            errors_cont.hide().fadeIn();
                        } else {
                            for (var key in json.result) {
                                result_cont.append('<p class="">' + json.result[key] + '</p>');
                            }
                            result_cont.hide().fadeIn();
                            form[0].reset();
                            //window.location.reload();
                        }
                    }
                    disabled.hide();
                    BX.closeWait(wait);
                }
            });
        } else{
            BX.closeWait(wait);
            disabled.hide();
            // прокручиваем к первой ошибке
            //var pos = form.find('input.error:first').offset();
            //$('html,body').animate({scrollTop:pos.top-70},500);
        }
        return false;
    });


    function validate_ajax_form(form){
        var flag = true;
        form.find('input.required, textarea.required').each(function(){
            var input = $(this);
            var val = input.val();
            var type = input.attr('type');
            var placeholder = input.attr('placeholder');

            if (type == 'checkbox') {
                if (input.is(':checked')) {
                    input.removeClass('error');
                    input.parent().removeClass('error');
                } else {
                    input.addClass('error');
                    input.parent().addClass('error');
                    flag=false;
                }
            } else if (
                val=='' || val==placeholder || val.length<3
                // если телефон, то не меньше 6 символов
                || (input.hasClass('phone') && val.length<6))
            {
                $(this).addClass('error');
                $(this).parent().find('.label-alert').show();
                flag=false;
            } else {
                var re = /^[0-9- ]*$/;
                var re_email = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if (input.hasClass('phone') && !re.test(val)) {
                    $(this).addClass('error');
                    flag=false;
                } else if (input.hasClass('email') && !re_email.test(val)) {
                    $(this).addClass('error');
                    flag=false;
                } else{
                    $(this).removeClass('error');
                    $(this).parent().find('.tip-error').hide();
                    $(this).parent().find('.tip-error-phone').hide();
                }
            }
        });


        return flag;
    }

}); // end document ready