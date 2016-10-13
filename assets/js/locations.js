/* global google */
(function( $ ) {

	/**
	 * 1. options.address - (array|string) LatLng coordiantes or formatted address
	 * 2. options.sourceselector - (string) selector name for parent element in DOM
	 */
	$.fn.RELocations = function( options ) {
		var $this = $( this ),
			data  = {
				zoom: 5,
				infowindow: null,
				address: null,
				sourceselector: null
			},
			$source = [],
			infowindow;

		$.extend( data, options );

		if ( null === data.address && null === data.sourceselector ) {
			return ! 1;
		}

		initMap();

		/**
		 * Initialize map.
		 */
		function initMap() {
			var loading = 'tm-re-map-loading',
				map, geocoder, bounds, position;

			prepareMapOptions();

			// Changed a control's position.
			setControlOptions();

			map      = new google.maps.Map( $this[0], data );
			geocoder = new google.maps.Geocoder();
			bounds   = new google.maps.LatLngBounds();

			// When map are loaded.
			google.maps.event.addListenerOnce( map, 'idle', function() {
				var markersNumb = $source.length;

				// Automatically center the map fitting all markers on the screen.
				map.fitBounds( bounds );

				// Sets the zoom level of the map with one marker (e.g. single property page).
				if ( ! markersNumb || 1 === markersNumb ) {
					map.setZoom( data.zoom );
				}

				// Remove loader.
				if ( $this.hasClass( loading ) ) {
					$this.removeClass( loading );
				}
			});

			if ( null === data.address ) {

				$source.each( function( i, el ) {
					var address = $( el ).data( 'property-address' ),
						latlng  = $( el ).data( 'property-latlng' ),
						html    = $( el )[0].outerHTML;

					if ( undefined === latlng ) {
						geocodeAddress( map, geocoder, bounds, address, html );
					} else {
						position = getLatLng( latlng );
						setMarker( map, bounds, position, html );
					}
				} );

				return 1;
			}

			if ( 'object' === typeof data.address ) {
				position = getLatLng( data.address );
				setMarker( map, bounds, position );

			} else {
				geocodeAddress( map, geocoder, bounds, data.address );
			}

			return 1;
		}

		/**
		 * Geocoding.
		 *
		 * @param  object resultsMap
		 * @param  object geocoder
		 * @param  object bounds
		 * @param  string location
		 * @param  string html
		 */
		function geocodeAddress( resultsMap, geocoder, bounds, location, html ) {
			html = html || '';

			geocoder.geocode({
				'address': location
			}, function( results, status ) {
				var position;

				if ( status === google.maps.GeocoderStatus.OK ) {
					position = results[0].geometry.location;
					setMarker( resultsMap, bounds, position, html );
				}
			});
		}

		/**
		 * Place a marker on the map.
		 *
		 * @param object map
		 * @param object bounds
		 * @param object position
		 * @param string html
		 */
		function setMarker( map, bounds, position, html ) {
			var animationType = data.hasOwnProperty( 'animation' ) ? data.animation : ''
				marker;

			bounds.extend( position );

			marker = new google.maps.Marker({
				map: map,
				draggable: false,
				animation: google.maps.Animation[ animationType ],
				position: position,
				icon: data.hasOwnProperty( 'icon' ) ? data.icon : '',
				html: html
			});

			if ( null === data.infowindow ) {
				return;
			}

			google.maps.event.addListener( marker, 'click', function() {
				infowindow.setContent( html );
				infowindow.open( map, this );
			});

			google.maps.event.addListener( infowindow, 'domready', function() {
				$( '.gm-style-iw' ).parent().addClass( 'tm-re-iw' );
			});
		}

		function prepareMapOptions() {

			// Sanitize & parse styles.
			if ( data.hasOwnProperty( 'styles' ) && ( 'string' === typeof data.styles ) ) {
				data.styles = $.parseJSON( data.styles );
			}

			if ( data.hasOwnProperty( 'sourceselector' ) ) {
				$source = $( '#' + data.sourceselector ).children();
			}

			if ( null !== data.infowindow ) {
				infowindow = new google.maps.InfoWindow( data.infowindow );
			}
		}

		/**
		 * Prepare options for Map Controls in javascript-format.
		 */
		function setControlOptions() {
			var mapTypeControlOptions, zoomControlOptions, streetViewControlOptions;

			if ( data.hasOwnProperty( 'mapTypeControlOptions' ) ) {
				mapTypeControlOptions = {
					style: google.maps.MapTypeControlStyle[ data.mapTypeControlOptions.style ],
					position: google.maps.ControlPosition[ data.mapTypeControlOptions.position ]
				};

				data.mapTypeControlOptions = mapTypeControlOptions;
			}

			if ( data.hasOwnProperty( 'zoomControlOptions' ) ) {
				zoomControlOptions = {
					position: google.maps.ControlPosition[ data.zoomControlOptions.position ]
				};

				data.zoomControlOptions = zoomControlOptions;
			}

			if ( data.hasOwnProperty( 'streetViewControlOptions' ) ) {
				streetViewControlOptions = {
					position: google.maps.ControlPosition[ data.streetViewControlOptions.position ]
				};

				data.streetViewControlOptions = streetViewControlOptions;
			}
		}

		/**
		 * Retrieve object with lat/lng coordinates.
		 *
		 * @param  array  latlng
		 * @return object
		 */
		function getLatLng( latlng ) {
			return {
				lat: parseFloat( latlng[0] ),
				lng: parseFloat( latlng[1] )
			};
		}
	};

})( jQuery );
