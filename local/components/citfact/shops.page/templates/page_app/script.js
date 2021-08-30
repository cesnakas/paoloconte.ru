var googleMaps = [];
$(document).ready(function() {
    iniShopPageGoogleMap();
});

//with markers in get function (for dynamic update)
function iniShopPageGoogleMap() {

    var mapid ="shop-page-map";
    var map=document.getElementById(mapid);
    var shops = BX.message('SHOPS_TO_MAP');
    var city_current = BX.message('CITY_CURRENT');

    var infowindow = null;
    var gmarkers = [];

    if(map){
        var latlngbounds = new google.maps.LatLngBounds();

        function setMarkers(map, locations) {

            // Add markers to the map

            // Marker sizes are expressed as a Size of X,Y
            // where the origin of the image (0,0) is located
            // in the top left of the image.

            // Origins, anchor positions and coordinates of the marker
            // increase in the X direction to the right and in
            // the Y direction down.
            var defaultIcon = {
                url: '/local/templates/paoloconte/images/icons/map-marker.png',
                // This marker is 20 pixels wide by 32 pixels tall.
                size: new google.maps.Size(41, 60),
                // The origin for this image is 0,0.
                origin: new google.maps.Point(0,0),
                // The anchor for this image is the base of the flagpole at 0,32.
                anchor: new google.maps.Point(20, 60)
            };

            var activeIcon = new google.maps.MarkerImage(
                '/local/templates/paoloconte/images/icons/map-marker.png',
                // This marker is 20 pixels wide by 32 pixels tall.
                new google.maps.Size(41, 60),
                // The origin for this image is 0,0.
                new google.maps.Point(0,0),
                // The anchor for this image is the base of the flagpole at 0,32.
                new google.maps.Point(20, 60));

            // Shapes define the clickable region of the icon.
            // The type defines an HTML <area> element 'poly' which
            // traces out a polygon as a series of X,Y points. The final
            // coordinate closes the poly by connecting to the first
            // coordinate.

            var shape = {
                coord: [1, 1, 1, 38, 31, 38, 38 , 1],
                type: 'poly'
            };

            for (var city_id in locations){
                for (var i = 0; i < locations[city_id].length; i++) {
                    var shop = locations[city_id][i];
                    if (shop.PROPERTY_COORDS_VALUE !== '') {
                        var text = '';
                        if (shop.PROPERTY_ADDRESS_VALUE !== false){
                            text += shop.PROPERTY_ADDRESS_VALUE + '<br/>';
                        }
                        if (shop.PROPERTY_PHONE_VALUE !== false){
                            text += shop.PROPERTY_PHONE_VALUE + '<br/>';
                        }
                        if (shop.PROPERTY_GRAPHICK_VALUE !== false){
                            text += shop.PROPERTY_GRAPHICK_VALUE.TEXT + '<br/>';
                        }

                        var coords = shop.PROPERTY_COORDS_VALUE.split(',');
                        var LatLng = new google.maps.LatLng(coords[0], coords[1]);
                        var marker = new google.maps.Marker({
                            position: LatLng,
                            map: map,
                            animation: google.maps.Animation.DROP,
                            icon: defaultIcon,
                            shape: shape,
                            title: '<a href="/shops/'+shop.PROPERTY_CITY_CODE+'/'+shop.CODE+'/">' + shop.NAME + '</a>',
                            id: shop.ID,
                            text: text
                        });

                        /*if (city_current.length > 0 && shop.PROPERTY_CITY_CODE == city_current[0].CODE){
                            latlngbounds.extend(marker.position);
                        }*/
                        latlngbounds.extend(marker.position);

                        //click on marker
                        google.maps.event.addListener(marker, "click", function () {
                            for (var j=0; j<gmarkers.length; j++) {
                                gmarkers[j].setIcon(defaultIcon);
                            }
                            this.setIcon(activeIcon);

                            //set center on marker
                            map.panTo(this.position);

                            //set zoom
                            //map.setZoom(13);

                            infowindow.setContent(
                                "<div class='marker-info'><div class='marker-title bold'>"
                                +this.title
                                +"</div><div class='marker-text'>"
                                +this.text
                                +"</div></div>"
                            );
                            infowindow.open(map, this);
                        });
                        gmarkers.push(marker);
                    }
                }
            }
        }//end setMarkers()

        // Create an array of styles.
        var styles = [];

        // Create a new StyledMapType object, passing it the array of styles,
        // as well as the name to be displayed on the map type control.
        var styledMap = new google.maps.StyledMapType(styles,
            {name: "Styled Map"});


        // Create a map object, and include the MapTypeId to add
        // to the map type control.
        var mapOptions = {
            zoom: 11,
            minZoom:3,
            center: new google.maps.LatLng(55.6468, 37.581),
            mapTypeControlOptions: {
                mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
            },
            scrollwheel: false
        };
        var map = new google.maps.Map(document.getElementById(mapid),
            mapOptions);

        // Добавляем маркеры
        setMarkers(map, shops);

        // Если смотрим страницу конкретного города, то центрируем карту на этом городе
        if (BX.message('CITY_CURRENT').length > 0){
            var city_current = BX.message('CITY_CURRENT');
            var center = new google.maps.LatLng(city_current[0].PROPERTY_LAT_VALUE, city_current[0].PROPERTY_LONG_VALUE);
            map.setCenter(center);
            //map.fitBounds(latlngbounds);
        }
        else{
            map.setCenter( latlngbounds.getCenter());
            map.fitBounds(latlngbounds);
        }

        infowindow = new google.maps.InfoWindow({
            content: "loading..."
        });

        //map.mapTypes.set('map_style', styledMap);
        //map.setMapTypeId('map_style');

        googleMaps.push(map);

        if (BX.message('CITY_CURRENT').length > 0){
                var menuHeight = $(".header-add").height();
                $('html, body').animate({
                    scrollTop: $(".map-link-list").offset().top - menuHeight
                }, 500);
        }
    }

    return false;
};






