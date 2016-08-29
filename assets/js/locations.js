(function( $ ) {

	$.fn.RELocations = function( options ) {
		var $this = $( this ),
			data  = {
				zoom : 5,
				infowindow : null,
				address : null
			},
			locations = {},
			positions = {};

		$.extend( data, options );
		initMap();

		/**
		 * Initialize map.
		 */
		function initMap() {
			var $source = [],
				loading = 'tm-re-map-loading',
				markersNumb, map, geocoder, bounds;

			// Sanitize & parse styles.
			if ( data.hasOwnProperty( 'styles' ) && ( 'string' == typeof data.styles ) ) {
				data.styles = $.parseJSON( data.styles );
			}

			if ( data.hasOwnProperty( 'sourceselector' ) ) {
				$source = $( '#' + data.sourceselector ).children();
			}

			markersNumb = $source.length;

			if ( null === data.address && ! markersNumb ) {
				return !1;
			}

			// Changed a control's position.
			setControlOptions();

			map      = new google.maps.Map( $this[0], data );
			geocoder = new google.maps.Geocoder();
			bounds   = new google.maps.LatLngBounds();

			// When map are loaded.
			google.maps.event.addListenerOnce( map, 'idle', function() {

				// Automatically center the map fitting all markers on the screen.
				map.fitBounds( bounds );

				if ( ! markersNumb || 1 === markersNumb ) {
					map.setZoom( data.zoom );
				}

				// Remove loader.
				if ( $this.hasClass( loading ) ) {
					$this.removeClass( loading );
				}
			});

			// map.addListener( 'click', function( e ) {
			// 	mapPanTo( e.latLng, map );
			// });

			if ( null !== data.infowindow ) {
				infowindow = new google.maps.InfoWindow( data.infowindow );
			}

			if ( null === data.address ) {

				$source.each( function( i, el ) {
					geocodeAddress( map, geocoder, bounds, $( el ) );
				} );

			} else {

				for ( key in data.address ) {
					geocodeAddress( map, geocoder, bounds, data.address[ key ] );
				}
			}
		};

		/**
		 * Geocoding.
		 *
		 * @param  object resultsMap
		 * @param  object geocoder
		 * @param  object bounds
		 * @param  string _data
		 * @return void
		 */
		function geocodeAddress( resultsMap, geocoder, bounds, _data ) {
			var location = _data,
				html     = '';

			if ( 'object' == typeof _data ) {
				location = _data.data( 'property-address' );
				html     = _data[0].outerHTML;
			}

			if ( undefined === location ) {
				return !1;
			}

			location = String( location );

			geocoder.geocode({
				'address': location
			}, function( results, status ) {

				if ( status === google.maps.GeocoderStatus.OK ) {
					var position      = results[0].geometry.location,
						key           = results[0].place_id,
						animationType = data.hasOwnProperty( 'animation' ) ? data.animation : '',
						marker;

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
						google.maps.event.addListener( marker, 'click', function() {
							infowindow.setContent( this.html );
							infowindow.open( resultsMap, this );
						});

						google.maps.event.addListener( infowindow, 'domready', function() {
							var className = _data.attr( 'class' ).split( ' ' ).join( '.' );

							$( '.' + className )
								.closest( '.gm-style-iw' )
								.parent()
								.addClass( 'tm-re-iw' );
						});
					}

					// Automatically center the map fitting all markers on the screen.
					// resultsMap.fitBounds( bounds );
					// resultsMap.setZoom( data.zoom );
				}
			});
		};

		/**
		 * Callback-function on `click` event.
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
		function setControlOptions() {

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
	}

})( jQuery );