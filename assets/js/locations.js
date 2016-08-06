(function( $ ) {

	$.fn.RELocations = function( options ) {
		var $this = $( this ),
			infowindow = null,
			data = {
				zoom : 5,
				infowindow : null,
				address : null
			};

		$.extend( data, options );
		prepareControlOptions();
		initMap();

		/**
		 * Initialize map.
		 */
		function initMap() {

			if ( null === data.address && ! data.hasOwnProperty( 'sourceselector' ) ) {
				return !1;
			}

			var $selector = $( data.sourceselector ),
				map, geocoder, bounds;

			if ( null === data.address && ! $selector.length ) {
				return !1;
			}

			map = new google.maps.Map( $this[0], data );
			geocoder = new google.maps.Geocoder();
			bounds = new google.maps.LatLngBounds();

			map.addListener( 'click', function( e ) {
				mapPanTo( e.latLng, map );
			});

			if ( null !== data.infowindow ) {
				infowindow = new google.maps.InfoWindow( data.infowindow );
			}

			if ( null === data.address ) {

				$selector.each( function() {
					geocodeAddress( map, geocoder, bounds, $(this) );
				} );

			} else {

				for ( key in data.address ) {
					geocodeAddress( map, geocoder, bounds, data.address[ key ] );
				}
			}
		};

		/**
		 * Callback function on click-event.
		 *
		 * @param  object LatLng
		 * @param  ojject map
		 * @return void
		 */
		function mapPanTo( LatLng, map ) {
			map.panTo( LatLng );
		};

		/**
		 * Prepare options for Map Controls in javascript-format.
		 */
		function prepareControlOptions() {

			if ( data.hasOwnProperty( 'mapTypeControlOptions' ) ) {
				var mapTypeControlOptions = {
					style: google.maps.MapTypeControlStyle[ data.mapTypeControlOptions.style ],
					position: google.maps.ControlPosition[ data.mapTypeControlOptions.position ]
				};

				data.mapTypeControlOptions = mapTypeControlOptions;
			}

			if ( data.hasOwnProperty( 'zoomControlOptions' ) ) {
				var zoomControlOptions = {
					position: google.maps.ControlPosition[ data.zoomControlOptions.position ]
				};

				data.zoomControlOptions = zoomControlOptions;
			}

			if ( data.hasOwnProperty( 'streetViewControlOptions' ) ) {
				var streetViewControlOptions = {
					position: google.maps.ControlPosition[ data.streetViewControlOptions.position ]
				};

				data.streetViewControlOptions = streetViewControlOptions;
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
						marker,
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

					if ( null !== data.infowindow ) {
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
		var data = $( this ).data( 'atts' ),
			$map;

		if ( 'object' != typeof data ) {
			return !1;
		}

		if ( ! data.hasOwnProperty( 'id' ) ) {
			return !1;
		}

		$map = $( '#' + data.id );

		if ( ! $.isFunction( jQuery.fn.RELocations ) || ! $map.length ) {
			return !1;
		}

		$map.RELocations( data );
	} );

})( jQuery );