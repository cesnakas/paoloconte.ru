$(function() {
    $('#loyalty_card_form').submit(function() {
        var barcode = $('[name=barcode]', $(this)).val();
        barcode = barcode.replace(/ /g, "");
        if (!/^\d{13}$/.test(barcode.trim())) {
            $("#loyalty_card_error").text(incorrect_barcode_text);
            $("#loyalty_card_error").show();
            $('#loyalty_card_barcode_input_wrapper > input').addClass("error");
            return false;
        } else {
            $("#loyalty_card_error").text("");
            $("#loyalty_card_error").hide();
            $('#loyalty_card_barcode_input_wrapper > input').removeClass("error");
        }
    });

    if ($('#loyalty_card_error').length && $('#loyalty_card_error').html().trim().length) {
        $('#loyalty_card_error').show();
        $('#loyalty_card_barcode_input_wrapper > input').addClass("error");
    }
    $('#loyalty_card_barcode_input_wrapper > input').mask('9 999999 999999');

    $('.loyalty_card_submit_button_wrapper').click(function(){
        $('#loyalty_card_form').submit();
    });
});