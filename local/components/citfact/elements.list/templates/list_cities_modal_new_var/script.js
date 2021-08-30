$(document).on("click", "a", function() {
    if ($(this).data('marker') == '1')
    {
        document.cookie = "city-id=" + encodeURIComponent($(this).data('city-id'));
        location.reload();
    }
});
