function formFastOrder (input) {
    if (!input)
        return false;

    var form = input.parents('form');
    var formID = form.attr('id');

    if (!formID || !form)
        return false;

    var wrap_form = $('#'+BX.message('WRAP_FORM_'+formID));

    var template_path = BX.message('TEMPLATE_PATH_'+formID);
    var component_path = BX.message('COMPONENT_PATH_'+formID);
    var arParams = BX.message('PARAMS_'+formID);

    var formdata = form.serializeArray();
    var formdataObj = {};
    $(formdata ).each(function(index, obj){
        formdataObj[obj.name] = obj.value;
    });

    var data = {data: JSON.stringify(formdataObj), params: JSON.stringify(arParams)};
    var result_cont = wrap_form.find('.success_cont');
    var errors_cont = wrap_form.find('.errors_cont');
    if ( validateFormFastOrder(form) ) {
        $.ajax({
            type: "POST",
            url: component_path + "/ajax.php",
            data: data,
            success: function (data) {
                var json = JSON.parse(data);
                result_cont.html('');
                errors_cont.html('');
                if (json.ERROR_TEXT.length > 0) {
                    errors_cont.append('<p class="">' + json.ERROR_TEXT + '</p>');
                    errors_cont.hide().fadeIn();
                }
                else {
                    if (json.RESULT.SUCCESS) {
                        if (json.RESULT.REDIRECT.length > 0) {
                            window.location = json.RESULT.REDIRECT;
                        } else {
                            var mes = BX.message('SUCCESS_ORDER_MESSAGE_'+formID);
                            mes = mes.replace("#ORDER_ID#", json.RESULT.ORDER_ID);
                            result_cont.append('<p class="">' + mes + '</p>');
                            // setTimeout(function() {
                            //     location.reload();
                            // }, 5000);
                        }
                    }
                    result_cont.hide().fadeIn();
                    form[0].reset();
                    //window.location.reload();
                }
            }
        });
    } else{
        // прокручиваем к первой ошибке
        //var pos = form.find('input.error:first').offset();
        //$('html,body').animate({scrollTop:pos.top-70},500);
    }
    return false;
}



function validateFormFastOrder (form){
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

