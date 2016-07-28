(function( $ ) {
	'use strict';

	CherryJsCore.utilites.namespace( 'real_estate' );

	CherryJsCore.real_estate = {

		init: function() {
			var self = this;

			// Document ready event.
			if ( CherryJsCore.status.is_ready ) {
				self.document_ready( self );
			} else {
				CherryJsCore.variable.$document.on( 'ready', self.document_ready( self ) );
			}
		},

		document_ready: function( self ) {
			var self = self;

			self.gallery( self );
		},

		gallery: function( self ) {
			$( '.tm-property-gallery-js' ).each( function() {
				var $gallery = $( this ),
					params = $gallery.data( 'atts' );

				if ( ! $.isFunction( jQuery.fn.swiper ) || ! $gallery.length ) {
					return !1;
				}

				if ( params.hasOwnProperty( 'group' ) ) {
					var obj = params.group,
						key,
						saved = {};

					for ( key in obj ) {
						saved[ key ] = self.galleryInit( $( obj[ key ], false ) );
					}

					saved.top.params.control = saved.thumbs;
					saved.thumbs.params.control = saved.top;

				} else {
					self.galleryInit( $gallery, params );
				}

			} );
		},

		galleryInit: function( $selector, args ) {
			var galleryId = $selector.data( 'id' );

			args = ( false === args ) ? $selector.data( 'atts' ) : args;

			return new Swiper( '#' + galleryId, args );
		}
	};

	CherryJsCore.real_estate.init();

})( jQuery );