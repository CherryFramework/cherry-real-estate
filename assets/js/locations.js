(function( $ ) {

	$.fn.RELocations = function() {
		var $this = $( this ),
			data = $this.data(),
			address = null,
			dataInfoWindow = false,
			infowindow = null;

		if ( data.hasOwnProperty( 'address' ) ) {
			address = data.address;
		}

		if ( ! data.hasOwnProperty( 'zoom' ) ) {
			data.zoom = 15;
		}

		if ( data.hasOwnProperty( 'infowindow' ) ) {
			dataInfoWindow = data.infowindow;
		}

		initMap();

		/**
		 * Initialize map.
		 */
		function initMap() {

			if ( null === address && ! data.hasOwnProperty( 'sourceselector' ) ) {
				return null;
			}

			var $selector = $( data.sourceselector ),
				map, geocoder, bounds;

			if ( null === address && ! $selector.length ) {
				return !1;
			}

			map = new google.maps.Map( $this[0], data );
			geocoder = new google.maps.Geocoder();
			bounds = new google.maps.LatLngBounds();

			if ( false !== dataInfoWindow ) {
				infowindow = new google.maps.InfoWindow( dataInfoWindow );
			}

			if ( null === address ) {

				$selector.each( function() {
					geocodeAddress( map, geocoder, bounds, $(this) );
				} );

			} else {

				for ( key in address ) {
					geocodeAddress( map, geocoder, bounds, address[ key ] );
				}
			}
		};

		/**
		 * Geocoding.
		 *
		 * @param  object resultsMap
		 * @param  object geocoder
		 * @param  object bounds
		 * @param  string address
		 * @return void
		 */
		function geocodeAddress( resultsMap, geocoder, bounds, _data ) {
			var location = _data,
				html = '';

			if ( 'object' === typeof _data ) {
				location = _data.data( 'property-address' );
				html     = _data.html();
			}

			geocoder.geocode({
				'address': location
			}, function( results, status ) {

				if ( status === google.maps.GeocoderStatus.OK ) {
					var position = results[0].geometry.location,
						marker
						animationType = data.hasOwnProperty( 'animation' ) ? data.animation : '';

					bounds.extend( position );

					marker = new google.maps.Marker({
						map: resultsMap,
						draggable: false,
						animation: google.maps.Animation[ animationType ],
						position: position,
						icon: data.hasOwnProperty( 'icon' ) ? data.icon : '',
						html: html
					});

					if ( false !== dataInfoWindow ) {
						google.maps.event.addListener( marker, 'click', function () {
							infowindow.setContent( this.html );
							infowindow.open( resultsMap, this );
						});
					}

					// Automatically center the map fitting all markers on the screen.
					resultsMap.fitBounds( bounds );
					// resultsMap.setZoom( zoom );
				}
			});
		};
	}

	// Let's Go.
	$( '.tm-re-map' ).each( function() {
		var mapId = $( this ).data( 'id' ),
			$map = $( '#' + mapId );

		if ( ! $.isFunction( jQuery.fn.RELocations ) || ! $map.length ) {
			return !1;
		}

		$map.RELocations();
	} );

})( jQuery );