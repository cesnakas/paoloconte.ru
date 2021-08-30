// fast_order_mobile
$(document).ready(function () {

    if (typeof BX.message['arParams_fast_order'] == "undefined") {
        return;
    }

    var template_path = BX.message('TEMPLATE_PATH_fast_order');
    var component_path = BX.message('COMPONENT_PATH_fast_order');
    var arParams = BX.message('arParams_fast_order');

    $('#fast_order_submit').click(function () {
        var form = $(this).parents('form');
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

        return flag;
    }

}); // end document ready




// product_review
$(document).ready(function () {

    if (typeof BX.message['arParams_product_review'] == "undefined") {
        return;
    }

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

    function clearForm(form){
        form.find('input.required, textarea.required').each(function(){
            $(this).val('');
            $(this).removeClass('error').parent().removeClass('error_mes');
            $(this).parent().find('.tip-error').hide();
            $(this).parent().find('.tip-error-phone').hide();
        });
    }


    var template_path = BX.message('TEMPLATE_PATH_product_review');
    var component_path = BX.message('COMPONENT_PATH_product_review');
    var arParams = BX.message('arParams_product_review');

    $('#product_review_submit').click(function () {
        var form = $(this).parents('form');
        var data = {formdata: form.serialize(), params: arParams, sessid: $('input#sessid').val()};
        var result_cont = form.find('.success_cont');
        var errors_cont = form.find('.errors_cont');
        var mesinput = form.find('.message_type');
        $('.show_length').hide();
        if ( validate_ajax_form(form) ) {
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
        }
        return false;
    });


    function validate_ajax_form(form){
        flag = true;
        form.find('input.required, textarea.required').each(function(){
            var input = $(this);
            var val = input.val();
            var placeholder = input.attr('placeholder');

            if (
                (input.hasClass('message_type') && val.length < 100) || // если сообщение, то не меньше 100 символов
                val=='' || val==placeholder || val.length<3 // если телефон, то не меньше 6 символов
                || (input.hasClass('phone') && val.length<6)
            ) {
                $(this).addClass('error').parent().addClass('error_mes');
                $(this).parent().find('.label-alert').show();
                flag=false;
            }
            else {
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

        return flag;
    }

}); // end document ready


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




// subscribe_price
$(document).ready(function () {

    if (typeof BX.message['arParams_subscribe_price'] == "undefined") {
        return;
    }

    var template_path = BX.message('TEMPLATE_PATH_subscribe_price');
    var component_path = BX.message('COMPONENT_PATH_subscribe_price');
    var arParams_subscribe_price = BX.message('arParams_subscribe_price');

    $('#subscribe_price_submit').click(function () {
        var form = $(this).parents('form');
        form.find('input[name="TOVAR_ID"]').val( $('.btn-tobasket').data('product-id') );
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
            if (form.find('input[name="TOVAR_ID"]').val() == ''){
                $('#chooseSizeModalForSubscribe').modal('show');
                $('#side-low-price').modal('hide');
            }
            // прокручиваем к первой ошибке
            //var pos = form.find('input.error:first').offset();
            //$('html,body').animate({scrollTop:pos.top-70},500);
        }
        return false;
    });


    function validate_ajax_form(form){
        flag = true;
        form.find('input.required, textarea.required').each(function(){
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

        return flag;
    }

}); // end document ready