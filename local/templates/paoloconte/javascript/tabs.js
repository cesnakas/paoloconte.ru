$(document).ready(function() {
    var sort3 = document.getElementById('sort3'),
        sort5 = document.getElementById('sort5'),
        catalog = document.getElementsByClassName('catalog-box');

    if (!sort3 || !sort5 || !catalog) {
        return;
    }    
    function wide() {
        sort3.classList.add('active');
        sort5.classList.remove('active');
        catalog[0].classList.add('resized');
        Cookies.set('catalog_row', '3', { expires: 365 });

        $('.retailrocket-widget').trigger('catalogView:toggle', ['big']);

        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            "event": "threeInARow"
        });
    }
    function small() {
        sort5.classList.add('active');
        sort3.classList.remove('active');
        catalog[0].classList.remove('resized');
        Cookies.set('catalog_row', '5', { expires: 365 });

        $('.retailrocket-widget').trigger('catalogView:toggle', ['small']);

        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            "event": "fiveInARow"
        });
    }

    sort3.addEventListener('click', wide);
    sort5.addEventListener('click', small);

});