/* global CherryREGeocompleteData */
( function( doc, $ ) {
	$( doc ).ready( function() {

		var options = {
				map: '#cherry-re-location-place-map',
				details: '#cherry-re-location-place-details',
				detailsAttribute: 'data-geo',
				markerOptions: {
					draggable: true
				}
			},
			$el = $( '#cherry-re-geocode-input' ),
			latLng;

		if ( CherryREGeocompleteData.lat && CherryREGeocompleteData.lng ) {
			latLng = new google.maps.LatLng( CherryREGeocompleteData.lat, CherryREGeocompleteData.lng );

			options.mapOptions = { center: latLng };
			options.markerOptions.position = latLng;
		}

		$el.geocomplete( options ).on( 'geocode:dragged', function( event, latlng ) {
			$( 'input[data-geo=lat]' ).val( latlng.lat() );
			$( 'input[data-geo=lng]' ).val( latlng.lng() );
		} );

		$( '#cherry-re-geocode-trigger' ).click( function() {
			$el.trigger( 'geocode' );
		} );

	} );

})( document, jQuery );