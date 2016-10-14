/* global CherryREGeocompleteData, google */
( function( doc, $ ) {
	$( doc ).ready( function() {
		var options = {
			map: '#cherry-re-location-place-map',
			location: [-33.867, 151.195],
			details: '#cherry-re-location-place-details',
			detailsAttribute: 'data-geo',
			mapOptions: {
				zoom: 14,
			},
			markerOptions: {
				draggable: true
			},
			maxZoom: 14
		},
		$el   = $( '#cherry-re-geocode-input' ),
		$info = $( '#cherry-re-geocode-info' ),
		map, latLng, styles, styledMapType;

		if ( CherryREGeocompleteData.lat && CherryREGeocompleteData.lng ) {
			latLng = new google.maps.LatLng(
				parseFloat( CherryREGeocompleteData.lat ),
				parseFloat( CherryREGeocompleteData.lng )
			);

			options.location               = false;
			options.mapOptions.center      = latLng;
			options.markerOptions.position = latLng;

		} else if ( CherryREGeocompleteData.address ) {
			options.location = CherryREGeocompleteData.address;
		}

		$el.geocomplete( options )
			.on( 'geocode:dragged', function( event, latlng ) {
				$( 'input[data-geo=lat]' ).val( latlng.lat() );
				$( 'input[data-geo=lng]' ).val( latlng.lng() );
				$info.val( latlng.lat() + ', ' + latlng.lng() );
			} )
			.on( 'geocode:idle', function( event, latlng ) {
				$info.val( latlng.lat() + ', ' + latlng.lng() );
			} );

		// Get map object.
		map = $el.geocomplete( 'map' );

		// Set custom control - search input
		map.controls[google.maps.ControlPosition.TOP_CENTER].push( $el[0] );
		map.controls[google.maps.ControlPosition.TOP_RIGHT].push( $info[0] );

		// Map styles.
		if ( CherryREGeocompleteData.hasOwnProperty( 'styles' ) && ( 'string' === typeof CherryREGeocompleteData.styles ) ) {
			styles = $.parseJSON( CherryREGeocompleteData.styles );
			styledMapType = new google.maps.StyledMapType( styles, { name: "Styled Map" } );

			if ( styledMapType instanceof google.maps.StyledMapType ) {
				map.mapTypes.set( 'map_style', styledMapType );
				map.setMapTypeId( 'map_style' );
			}
		}

		// console.log(map.getZoom());
	} );

})( document, jQuery );
