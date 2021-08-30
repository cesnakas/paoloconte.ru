ymaps.ready(init);

function init() {
    var city_current = BX.message('CITY_CURRENT')[0];

    if (city_current) {
        buildCityYMap(city_current.ID);
    }
    else {
        buildMainYMap();
    }
}

function buildCityYMap(city_id) {
    var shops = BX.message('SHOPS_TO_MAP')[city_id];
    var coordinates = [shops[0].PROPERTY_COORDS_VALUE.split(',')[0], shops[1].PROPERTY_COORDS_VALUE.split(',')[1]];
    var map = new ymaps.Map('vacancy-page-map', {
        center: [coordinates[0], coordinates[1]],
        zoom: 10,
        controls: ['searchControl', 'typeSelector', 'fullscreenControl']
    }, {
        searchControlProvider: 'yandex#search'
    });

    tagging(map, shops);
}

function buildMainYMap() {
    var shops = BX.message('SHOPS_TO_MAP');
    var map = new ymaps.Map('vacancy-page-map', {
        center: [57.397311, 56.650224],
        zoom: 3,
        controls: ['zoomControl', 'searchControl', 'typeSelector', 'fullscreenControl']
    }, {
        searchControlProvider: 'yandex#search'
    });

    for (var shopObject in shops)
        tagging(map, shops[shopObject]);
}

function tagging(map, shops) {
    shops.forEach(function (element) {
        map.geoObjects.add(new ymaps.Placemark([element.PROPERTY_COORDS_VALUE.split(',')[0],
                element.PROPERTY_COORDS_VALUE.split(',')[1]], {
                balloonContent: "<a href=\"/shops/" + element.PROPERTY_CITY_CODE+'/'+ element.CODE+'/">' + "<strong>" + element.NAME + "</strong><br></a>" +
                element.PROPERTY_ADDRESS_VALUE +
                "<br>" + element.PROPERTY_PHONE_VALUE + "<br>" +
                (element.PROPERTY_GRAPHICK_VALUE.TEXT === undefined ? "" : element.PROPERTY_GRAPHICK_VALUE.TEXT) +
                "<br>"+"<a href=\"/shops/" + element.PROPERTY_CITY_CODE+'/'+ element.CODE+'/">' + "Подробнее</a>"
            },
            {
                iconImageHref: '/local/templates/paoloconte/images/icons/map-marker.png',
                iconImageSize: [41, 60],
                //Очень важное свойство! Если не установить, иконка не выведется
                iconLayout: 'default#image',
                iconImageOffset: [-20, -60]
            }));
    })

    map.behaviors.disable('scrollZoom');
}