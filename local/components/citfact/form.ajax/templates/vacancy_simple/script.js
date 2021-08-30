$(document).ready(function () {
    var template_path = BX.message('TEMPLATE_PATH');
    var component_path = BX.message('COMPONENT_PATH');
    var arParams_vacancy_simple = BX.message('arParams_vacancy_simple');

    $('#vacancy_simple_submit').click(function () {
        var form = $(this).parents('form');
        var data = {formdata: form.serialize(), params: arParams_vacancy_simple, sessid: $('input#sessid').val()};
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
        $('#label_PERSONAL_DATA').css('color', 'black');
        form.find('input.required, textarea.required').each(function(){
            var input = $(this);
            var val = input.val();
            var placeholder = input.attr('placeholder');
            if (val=='' || val==placeholder && (val.length<3 || input.context.type == 'checkbox')
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
                } else if (input.context.type == 'checkbox' && input.context.checked == false){
                    $(this).addClass('error');
                    flag=false;
                    $('#label_PERSONAL_DATA').css('color', 'red');
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
        return flag;
    }


    // FileUpload
        var url = '/include/ajax_fileupload/fileupload.php';
        $('#fileupload_rezume').fileupload({
            url: url,
            dataType: 'json',
            maxFileSize: 10000000,
            //acceptFileTypes: '/^image\/(gif|jpeg|png)$/',
            //previewSourceFileTypes: '/^image\/(gif|jpeg|png)$/',
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    $('.upload_rezume_block .filename').html(file.name);
                    $('input[name="FILE_REZUME"]').val(file.name);
                    //$('<p/>').text(file.name).appendTo('#files');
                });
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            },
            fail: function (e, data) {
                //console.log(data);
                //console.log(data.errorThrown);
                //console.log(data.textStatus);
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');

}); // end document ready