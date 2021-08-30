$(function () {
    var $letters = $("#cityModalInReserve .word-box a");
    $letters.on("click", function () {
        var letter = $(this).data( "letter" );
        $letters.removeClass("active");
        $(this).addClass("active");
        $('.city-list-wrap .city-list').removeClass('active');
        $('.city-list-wrap .city-list[data-letter="'+letter+'"]').addClass('active');
    });
});