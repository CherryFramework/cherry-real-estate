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
			self.submission_form( self );
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

		submission_form: function( self ) {
			var $form = $( '#tm-re-submissionform' );

			if ( ! $.isFunction( jQuery.fn.validate ) || ! $form.length ) {
				return !1;
			}

			$form.validate({
				debug: true, // disabled submit event
				messages: {
					property_title: CherryREData.messages,
					property_description: CherryREData.messages,
					property_price: CherryREData.messages,
					property_status: CherryREData.messages,
					property_type: CherryREData.messages,
					property_address: CherryREData.messages,
					property_bedrooms: CherryREData.messages,
					property_bathrooms: CherryREData.messages,
					property_area: CherryREData.messages,
					property_parking_places: CherryREData.messages,
					property_address: CherryREData.messages,
					agent_email: CherryREData.messages,
					agent_phone: CherryREData.messages,
				},
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
				errorClass: 'tm-re-submission-form__error',
				pendingClass: 'tm-re-submission-form__pending',
				validClass: 'tm-re-submission-form__valid',
				errorElement: 'span',
				highlight: function( element, errorClass ) {
					$( element ).fadeOut( function() {
						$( element ).fadeIn();
					} );
				},
				submitHandler: function( form ) {
					init( $( form ) );
				}
			});

			function init( $form ) {
				var formData   = $form.serializeArray(),
					nonce      = $form.find( 'input[name="tm-re-submissionform-nonce"]' ).val(),
					$error     = $form.find( '.tm-re-submission-form__error' ),
					$success   = $form.find( '.tm-re-submission-form__success' ),
					processing = 'processing',
					hidden     = 'tm-re-hidden';

				console.log(formData);

				if ( $form.hasClass( processing ) ) {
					return !1;
				}

				$form.addClass( processing );
				$error.empty();

				if ( ! $error.hasClass( hidden ) ) {
					$error.addClass( hidden );
				}

				if ( ! $success.hasClass( hidden ) ) {
					$success.addClass( hidden );
				}

				$.ajax({
					url: CherryREData.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'submission_form',
						nonce: nonce,
						property: formData
					},
					error: function() {
						$form.removeClass( processing );
					}
				}).done( function( response ) {
					console.log( response );

					$form.removeClass( processing );

					if ( true === response.success ) {
						$success.removeClass( hidden );
						return 1;
					}

					$error.removeClass( hidden ).html( response.data.message );
					return !1;
				});
			}
		}
	};

	CherryJsCore.real_estate.start();

})( jQuery );