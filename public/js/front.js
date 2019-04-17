/***FRONT PAGE FORMS***/

jQuery(document).ready(function($){
    $('.advanced-search-button').click(function(e){
        e.preventDefault();
        $('.more-options').toggleClass('open');
        $(this).toggleClass('turn-arrow');
    });

    var $container = $('.building-listing-sort-container');

    $container.imagesLoaded(function(){
        $container.isotope({
            // options
            itemSelector: '.grid-item',

            masonry: {
                // use element for option
                columnWidth: '.grid-sizer',
            },
            getSortData: {
                name: 'h3',
                sfAvail: '.available-space parseInt'
            },
            onLayout: function () {
                $win.trigger("scroll");
            }
        });

        var $win = $(window),
            $imgs = $("img.lazy");

        $container.on('layoutComplete', function(){
            $win.trigger("scroll");
        });

        $imgs.lazyload({
            effect: "fadeIn",
            failure_limit: Math.max($imgs.length - 1, 0)
        });
    });

    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    var myEfficientFn = debounce(function() {
        $container.isotope();
    }, 300);

    window.addEventListener('scroll', myEfficientFn);



    // $('.sort-btn').click(function(e){
    //   e.preventDefault();
    //     var sortValue = $(this).attr('data-sort-value');

    //     $container.isotope({ sortBy: sortValue});

    // });
    $('.reset-btn').click(function(e){
        e.preventDefault();
        $container.isotope({ sortBy : 'original-order' });

    });

    $('.ascending-btn').click(function(e){
        e.preventDefault();

        $container.isotope({ sortBy: 'name', sortAscending: true });
    });

    $('.descending-btn').click(function(e){
        e.preventDefault();

        $container.isotope({ sortBy: 'name', sortAscending: false });
    });

    $('.available-space-asc').click(function(e){
        e.preventDefault();

        $container.isotope({ sortBy: 'sfAvail', sortAscending: true });
    });

    $('.available-space-desc').click(function(e){
        e.preventDefault();

        $container.isotope({ sortBy: 'sfAvail', sortAscending: false });
    });

    $('.grid').click(function(e){
        e.preventDefault();
        $('.building-listing-sort-container').removeClass('list-view');
        $('.building-listing-sort-container').addClass('grid-view');
        $container.isotope('layout');
    });
    $('.list').click(function(e){
        e.preventDefault();
        $('.building-listing-sort-container').removeClass('grid-view');
        $('.building-listing-sort-container').addClass('list-view');
        $container.isotope('layout');
    });

    //For state select boxes
    var allOptions = $('#city option');
    $('#state').change(function () {
        $('#city option').remove();
        var classN = $('#state option:selected').prop('class');
        var opts = allOptions.filter('.' + classN);
        $.each(opts, function (i, j) {
            $(j).appendTo('#city');
        });

        $('#city').append($('<option>', {
            value:"",
            text:'Any City'
        }));

        $('#city').val("");
    });

});


/*
 *  new_map
 *
 *  This function will render a Google Map onto the selected jQuery element
 *
 *  @type	function
 *  @date	8/11/2013
 *  @since	4.3.0
 *
 *  @param	$el (jQuery element)
 *  @return	n/a
 */

function new_map( $el ) {

    // var
    var $markers = $el.find('.marker');


    // vars
    var args = {
        zoom		: 14,
        center		: new google.maps.LatLng(38.01, -95.9656344),
        mapTypeId	: google.maps.MapTypeId.ROADMAP
    };


    // create map
    var map = new google.maps.Map( $el[0], args);


    // add a markers reference
    map.markers = [];


    // add markers
    $markers.each(function(){

        add_marker( jQuery(this), map );

    });


    // center map
    center_map( map );


    // return
    return map;

}

var infowindow = new google.maps.InfoWindow({
    content     : ''
});

/*
 *  add_marker
 *
 *  This function will add a marker to the selected Google Map
 *
 *  @type	function
 *  @date	8/11/2013
 *  @since	4.3.0
 *
 *  @param	$marker (jQuery element)
 *  @param	map (Google Map object)
 *  @return	n/a
 */

function add_marker( $marker, map ) {

    // var
    var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );

    // create marker
    var marker = new google.maps.Marker({
        position	: latlng,
        map			: map,
        icon        : '/wp-content/plugins/vtsread/public/images/pin1smla.png'
    });

    // add to array
    map.markers.push( marker );

    // if marker contains HTML, add it to an infoWindow
    if( $marker.html() )
    {
        // show info window when marker is clicked & close other markers
        google.maps.event.addListener(marker, 'click', function() {
            //swap content of that singular infowindow
            infowindow.setContent($marker.html());
            infowindow.open(map, marker);
        });

        // close info window when map is clicked
        google.maps.event.addListener(map, 'click', function(event) {
            if (infowindow) {
                infowindow.close(); }
        });

    }

}

/*
 *  center_map
 *
 *  This function will center the map, showing all markers attached to this map
 *
 *  @type	function
 *  @date	8/11/2013
 *  @since	4.3.0
 *
 *  @param	map (Google Map object)
 *  @return	n/a
 */

function center_map( map ) {

    // vars
    var bounds = new google.maps.LatLngBounds();

    // loop through all markers and create bounds
    jQuery.each( map.markers, function( i, marker ){

        var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );

        bounds.extend( latlng );

    });

    // only 1 marker?
    if( map.markers.length === 1 )
    {
        // set center of map
        map.setCenter( bounds.getCenter() );
        map.setZoom( 14 );
    }
    //NO markers?
    else if (map.markers.length === 0)
    {
        map.setCenter(38.01, -95.9656344);
        map.setZoom( 4 );
    }
    else
    {
        // fit to bounds
        map.fitBounds( bounds );
    }

}

/*
 *  document ready
 *
 *  This function will render each map when the document is ready (page has loaded)
 *
 *  @type	function
 *  @date	8/11/2013
 *  @since	5.0.0
 *
 *  @param	n/a
 *  @return	n/a
 */
// global var
var map = null;

jQuery(document).ready(function($){

    $('.building-contact-map').each(function(){
        // create map
        map = new_map( $(this) );

        map.set('styles', [{
            zoomControl: false
        }, {
            "featureType": "all",
            "stylers": [
                {
                    "saturation": 0
                },
                {
                    "hue": "#e7ecf0"
                }
            ]
        },
            {
                "featureType": "road",
                "stylers": [
                    {
                        "saturation": -70
                    }
                ]
            },
            {
                "featureType": "transit",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "poi",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "featureType": "water",
                "stylers": [
                    {
                        "visibility": "simplified"
                    },
                    {
                        "saturation": -60
                    }
                ]
            }]);
    });

});