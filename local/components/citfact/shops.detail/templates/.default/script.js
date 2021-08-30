ymaps.ready(init);

function init() {
    var shop = BX.message('SHOP_TO_MAP');
    var coordinates = [shop.COORDS.split(',')[0], shop.COORDS.split(',')[1]];
    var myMap = new ymaps.Map('shop-detail-map', {
        center: [coordinates[0], coordinates[1]],
        zoom: 9,
        controls: ['zoomControl', 'searchControl', 'typeSelector', 'fullscreenControl']
    }, {
        searchControlProvider: 'yandex#search'
    });

    myMap.geoObjects.add(new ymaps.Placemark([coordinates[0], coordinates[1]], {
            balloonContent: "<a href=\"/shops/"+shop.CITY_CODE+'/'+shop.CODE+'/">' + "<strong>" + shop.NAME + "</strong><br></a>" +
                shop.ADDRESS +
                "<br>" + shop.PHONE + "<br>" +
                (shop.GRAPHICK === undefined ? "" : shop.GRAPHICK)
        },
        {
            iconImageHref: '/local/templates/paoloconte/images/icons/map-marker.png',
            iconImageSize: [41, 60],
            //Очень важное свойство! Если не установить, иконка не выведется
            iconLayout: 'default#image',
            iconImageOffset: [-20, -60]
        }));
    myMap.behaviors.disable('scrollZoom');
}

