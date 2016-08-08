/* global CherryREData */
(function( $ ) {
	'use strict';

	CherryJsCore.utilites.namespace( 'real_estate' );

	CherryJsCore.real_estate = {

		start: function() {
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
			self.submit_form( self );
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
						saved[ key ] = init( $( obj[ key ] ) );
					}

					saved.top.params.control = saved.thumbs;
					saved.thumbs.params.control = saved.top;

				} else {
					init( $gallery, params );
				}

			} );

			function init( $selector, args ) {
				var galleryId = $selector.data( 'id' );

				args = args || $selector.data( 'atts' );

				return new Swiper( '#' + galleryId, args );
			};
		},

		submit_form: function( self ) {
			var $form = $( '#tm-re-submitform' );

			if ( ! $.isFunction( jQuery.fn.validate ) || ! $form.length ) {
				return !1;
			}

			$form
				.submit( function( event ) {
					event.preventDefault();
				} )
				.validate({
					rules: {
						property_price: {
							number: true
						},
						property_area: {
							number: true
						},
						property_bedrooms: {
							digits: true
						},
						property_bathrooms: {
							digits: true
						},
						property_parking_places: {
							digits: true
						}
					},
					messages: {
						required: '*',
						property_price: "Enter price",
					},
					submitHandler: function( form ) {
						$( form ).init();
					}
				});

			function init() {
				var form       = $( this ),
					formData   = form.serializeArray(),
					nonce      = form.find( 'input[name="tm-re-submitform-nonce"]' ).val(),
					error      = form.find( '.tm-re-submit-form__error' ),
					success    = form.find( '.tm-re-submit-form__success' ),
					hidden     = 'tm-re-hidden',
					error_free = true;

				console.log(form);

				// if ( form.hasClass( 'processing' ) ) {
				// 	return !1;
				// }

				// form.addClass( 'processing' );
				// error.empty();

				// if ( ! error.hasClass( hidden ) ) {
				// 	error.addClass( hidden );
				// }

				// if ( ! success.hasClass( hidden ) ) {
				// 	success.addClass( hidden );
				// }

				$.ajax({
					url: CherryREData.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'submit_form',
						nonce: nonce,
						property: formData
					},
					error: function( jqXHR, textStatus, errorThrown ) {
						form.removeClass( 'processing' );
					}
				}).done( function( response ) {
					console.log( response );

					form.removeClass( 'processing' );

					if ( true === response.success ) {
						success.removeClass( hidden );
						return 1;
					}

					error.removeClass( hidden ).html( response.data.message );
					return !1;
				});
			}
		}
	};

	CherryJsCore.real_estate.start();

})( jQuery );