/* global CherryREData */
(function( $ ) {
	'use strict';

	CherryJsCore.utilites.namespace( 'cherryRealEstate' );

	CherryJsCore.cherryRealEstate = {

		start: function() {
			var self = this;

			if ( CherryJsCore.status.is_ready ) {
				self.documentReady( self );
			} else {
				CherryJsCore.variable.$document.on( 'ready', self.documentReady( self ) );
			}
		},

		documentReady: function( self ) {
			self.gallery( self );
			self.submissionForm( self );
			self.loginForm( self );
			self.popup( self );
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

		submissionForm: function( self ) {
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
						$( element ).fadeIn().addClass( 'error' );
					} );
				},
				submitHandler: function( form ) {
					ajaxSubmit( $( form ) );
				}
			});

			function ajaxSubmit( $form ) {
				var formData   = $form.serializeArray(),
					nonce      = $form.find( 'input[name="tm-re-submissionform-nonce"]' ).val(),
					$error     = $form.find( '.tm-re-submission-form__error' ),
					$success   = $form.find( '.tm-re-submission-form__success' ),
					processing = 'processing',
					hidden     = 'tm-re-hidden';

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
		},

		loginForm: function( self ) {
			CherryJsCore.variable.$document.on( 'click', '.tm-re-login-form__btn', init );

			function init( event ) {
				event.preventDefault();

				var $this       = $( this ),
					$form       = $this.parents( 'form' ),
					$error      = $form.find( '.tm-re-login-form__error' ),
					$success    = $form.find( '.tm-re-login-form__success' ),
					login_input = $form.find( 'input[name="tm-re-user-login"]' ),
					pass_input  = $form.find( 'input[name="tm-re-user-pass"]' ),
					login       = login_input.val(),
					pass        = pass_input.val(),
					nonce       = $form.find( 'input[name="tm-re-loginform-nonce"]' ).val(),
					processing  = 'processing',
					hidden      = 'tm-re-hidden';

				if ( $form.hasClass( processing ) ) {
					return !1;
				}

				if ( '' == login ) {
					login_input.addClass( 'error' );
					return !1;
				} else {
					login_input.removeClass( 'error' );
				}

				if ( '' == pass ) {
					pass_input.addClass( 'error' );
					return !1;
				} else {
					pass_input.removeClass( 'error' );
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
						action: 'login_form',
						nonce: nonce,
						access: {
							login : login,
							pass: pass
						}
					},
					error: function() {
						$form.removeClass( processing );
					}
				}).done( function( response ) {
					console.log( response );

					$form.removeClass( processing );

					if ( true === response.success ) {
						$success.removeClass( hidden );

						if ( $.isFunction( jQuery.fn.magnificPopup ) ) {
							$.magnificPopup.close();
						}

						return 1;
					}

					$error.removeClass( hidden ).html( response.data.message );
					return !1;
				});

			};
		},

		popup: function( self ) {
			var $link = $( '.tm-re-popup' );

			if ( ! $.isFunction( jQuery.fn.magnificPopup ) || ! $link.length ) {
				return !1;
			}

			$link.magnificPopup({
				type: 'inline',
				preloader: false,
				focus: '#tm-re-user-login',

				// When elemened is focused, some mobile browsers in some cases zoom in
				// It looks not nice, so we disable it:
				callbacks: {
					beforeOpen: function() {
						if ( $( window ).width() < 700 ) {
							this.st.focus = false;
						} else {
							this.st.focus = '#tm-re-user-login';
						}
					}
				}
			});
		}

	};

	CherryJsCore.cherryRealEstate.start();

})( jQuery );