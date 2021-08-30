$(document).ready(function () {
    var template_path = BX.message('TEMPLATE_PATH_CITFACT_REGISTER_AJAX');
    var arParams = BX.message('arParams');
    //console.log(arParams);

    $('#fact_register_submit').click(function () {
        var form = $('form[name="fact_form_register"]');
        var data = form.serialize();
        var result_cont = $('.result_cont');
        var errors_cont = $('.errors_cont');
        if ( validate(form) ) {
            $.ajax({
                type: "POST",
                url: template_path + "/ajax.php",
                data: data,
                success: function (data) {
                    var json = JSON.parse(data);
                    console.log(json);
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
        form.find('input.required').each(function(){
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