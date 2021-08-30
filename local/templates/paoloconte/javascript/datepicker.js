$(document).ready(function () {

    const $dates = $(".dateField");
    $dates.each(function () {
        const input = $(this);
        input.datepicker({
            autoClose: true
        });

    })
    const inputs = document.querySelectorAll(".dateField");
    for (var i = 0; i  < inputs.length; i++) {
        IMask(
            inputs[i],
            {
                mask: Date,
                min: new Date(1890, 0, 1),
                max: new Date(),
                lazy: false
            });

    }
});


