var googleMaps = [];
$(document).ready(function() {
    iniGoogleMap();
});

//with markers in get function (for dynamic update)
function iniGoogleMap() {

    var mapid ="global-map";
    var map=document.getElementById(mapid);

    var shops = [

        ['Название салона 1', 55.75514899488118, 37.618985921144485, 1, 'Кутузовский проспект 2'],
        ['Название салона 2', 55.755887,37.81735, 2, 'Кутузовский проспект 22'],
        ['Название салона 3', 55.955887,37.61735, 3, 'Кутузовский проспект 222']

    ];

    var infowindow = null;
    var gmarkers = [];

    if(map){

    //console.log(shops);

    var latlngbounds = new google.maps.LatLngBounds();

    function setMarkers(map, locations) {

        // Add markers to the map

        // Marker sizes are expressed as a Size of X,Y
        // where the origin of the image (0,0) is located
        // in the top left of the image.

        // Origins, anchor positions and coordinates of the marker
        // increase in the X direction to the right and in
        // the Y direction down.
        var defaultIcon = new google.maps.MarkerImage('/local/templates/paoloconte/images/icons/map-marker.png',
            // This marker is 20 pixels wide by 32 pixels tall.
            new google.maps.Size(41, 60),
            // The origin for this image is 0,0.
            new google.maps.Point(0,0),
            // The anchor for this image is the base of the flagpole at 0,32.
            new google.maps.Point(41, 60));

        var activeIcon = new google.maps.MarkerImage('/local/templates/paoloconte/images/icons/map-marker.png',
            // This marker is 20 pixels wide by 32 pixels tall.
            new google.maps.Size(41, 60),
            // The origin for this image is 0,0.
            new google.maps.Point(0,0),
            // The anchor for this image is the base of the flagpole at 0,32.
            new google.maps.Point(41, 60));

        // Shapes define the clickable region of the icon.
        // The type defines an HTML <area> element 'poly' which
        // traces out a polygon as a series of X,Y points. The final
        // coordinate closes the poly by connecting to the first
        // coordinate.

        var shape = {
            coord: [1, 1, 1, 38, 31, 38, 38 , 1],
            type: 'poly'
        };

        for (var i = 0; i < locations.length; i++) {
            var shops = locations[i];
            var LatLng = new google.maps.LatLng(shops[1], shops[2]);
            var marker = new google.maps.Marker({
                lat: shops[1],
                lng: shops[2],
                position: LatLng,
                map: map,
                animation: google.maps.Animation.DROP,
                icon: defaultIcon,
                shape: shape,
                title: shops[0],
                id: shops[3],
                text: shops[4]
            });


            latlngbounds.extend(marker.position);

            var contentString = "Some content";

            //click on marker
            google.maps.event.addListener(marker, "click", function () {

                for (var i=0; i<gmarkers.length; i++) {
                    gmarkers[i].setIcon(defaultIcon);
                }
                this.setIcon(activeIcon);

                //set center on marker
                map.panTo(new google.maps.LatLng(this.lat,this.lng));

                //set zoom
                map.setZoom(13);

                infowindow.setContent("<div class='marker-info'><div class='marker-title bold'>"+this.title+"</div><div class='marker-text'>"+this.text+"</div></div>");
                infowindow.open(map, this);
            });
            gmarkers.push(marker);
        }

    }

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

        //Associate the styled map with the MapTypeId and set it to display.


        setMarkers(map, shops);


        $(document).on("click", ".scroll-box .btn", function (e) {
            e.preventDefault();
            var elemid = $(this).data('markerid');

            for (var i=0; i<gmarkers.length; i++) {
                if ((gmarkers[i].id)==elemid){
                    google.maps.event.trigger(gmarkers[i], 'click');
                    return false;
                }


            }

        });



        /*
                $(document).find(".content-box").on("click", ".aside-box", function (e) {
                    e.preventDefault();

                    google.maps.event.trigger(gmarkers[1], 'click');
                });
        */

        //get map position for visible all markers
        map.setCenter( latlngbounds.getCenter(), map.fitBounds(latlngbounds));

        infowindow = new google.maps.InfoWindow({
            content: "loading..."

        });

        map.mapTypes.set('map_style', styledMap);
        map.setMapTypeId('map_style');

        googleMaps.push(map);
    }


};






