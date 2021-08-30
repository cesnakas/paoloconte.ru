$(document).ready(function () {
    $('#fact_form_authorize input[type="text"], #fact_form_authorize input[type="password"]').keyup(function (e) {
        if (e.which == 13){
            $(this).parents('form').find('.fact_authorize_submit').click();
        }
    });
});

if (typeof FactAjaxAuth != 'function')
    function FactAjaxAuth(link) {
        //$('a.fact_authorize_submit').click(function () {
        var form_id = link.data('form-id');

        var template_path = BX.message('TEMPLATE_PATH_' + form_id);
        var arParams = BX.message('arParams_' + form_id);

        var form = $('form[name="fact_form_authorize"][data-form-id="' + form_id + '"]');
        var data = form.serialize();

        var result_cont = $('.auth_result_cont');
        var errors_cont = $('.auth_errors_cont');

        if (FactAjaxAuthValidate(form)) {
            $.ajax({
                type: "POST",
                url: template_path + "/ajax.php",
                data: data,
                success: function (data) {
                    var json = JSON.parse(data);
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
        else {
            // прокручиваем к первой ошибке
            //var pos = form.find('input.error:first').offset();
            //$('html,body').animate({scrollTop:pos.top-70},500);
        }
        return false;
        //});
    }


if (typeof FactAjaxAuthValidate != 'function')
function FactAjaxAuthValidate(form){
    flag = true;
    form.find('input.required').each(function(){
        //console.log($(this).find('input').val());
        var input = $(this);
        var val = input.val();
        var placeholder = input.attr('placeholder');

        if (val=='' || val==placeholder || val.length<3){
            $(this).addClass('error');
            $(this).parent().find('.label-alert').show();
            flag=false;
        }
        else {
            $(this).removeClass('error'); $(this).parent().find('.tip-error').hide(); $(this).parent().find('.tip-error-phone').hide();
        }
    });

    return flag;
}
