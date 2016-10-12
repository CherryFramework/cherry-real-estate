/* global CherryREGeocompleteData */
( function( doc, $ ) {
	$( doc ).ready( function() {
		var options = {
			map: '#cherry-re-location-place-map',
			location: [-33.867, 151.195],
			details: '#cherry-re-location-place-details',
			detailsAttribute: 'data-geo',
			markerOptions: {
				draggable: true
			}
		},
		$el = $( '#cherry-re-geocode-input' ),
		map, latLng;

		if ( CherryREGeocompleteData.lat && CherryREGeocompleteData.lng ) {
			latLng = new google.maps.LatLng(
				parseFloat( CherryREGeocompleteData.lat ),
				parseFloat( CherryREGeocompleteData.lng )
			);

			options.location = null;
			options.mapOptions = { center: latLng };
			options.markerOptions.position = latLng;
		}

		$el.geocomplete( options )
			.on( 'geocode:dragged', function( event, latlng ) {
				$( 'input[data-geo=lat]' ).val( latlng.lat() );
				$( 'input[data-geo=lng]' ).val( latlng.lng() );
			} );

		map = $el.geocomplete( 'map' );
		map.controls[google.maps.ControlPosition.TOP_CENTER].push( $el[0] );
	} );

})( document, jQuery );