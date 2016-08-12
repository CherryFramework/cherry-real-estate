/* global CherryREData */
(function( $ ) {
	'use strict';

	CherryJsCore.utilites.namespace( 'cherryRealEstate' );

	CherryJsCore.cherryRealEstate = {

		magnificPopup: null,

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
			self.popup( self );
			self.submissionForm( self );
			self.loginForm( self );
			self.registerForm( self );
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
					property_address: CherryREData.messages
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
				errorElement: 'span',
				highlight: function( element, errorClass, validClass ) {
					$( element ).fadeOut( function() {
						$( element ).fadeIn().addClass( errorClass ).removeClass( validClass );
					} );
				},
				unhighlight: function( element, errorClass, validClass ) {
					$( element ).removeClass( errorClass ).addClass( validClass );
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
						// gallery: $( '#property_gallery' ).val()
					},
					error: function( jqXHR, textStatus, errorThrown ) {
						$form.removeClass( processing );
						$error.removeClass( hidden ).html( textStatus );
					}
				}).done( function( response ) {
					console.log( response );

					$form.removeClass( processing );

					if ( true === response.success ) {
						$success.removeClass( hidden );
						$form[0].reset();
						return 1;
					}

					$error.removeClass( hidden ).html( response.data.message );
					return !1;
				});
			}
		},

		loginForm: function( self ) {
			var $form = $( '#tm-re-loginform' );

			if ( ! $.isFunction( jQuery.fn.validate ) || ! $form.length ) {
				return !1;
			}

			$form.validate({
				debug: true, // disabled submit event
				messages: {
					user_login: CherryREData.messages,
					user_pass: CherryREData.messages
				},
				errorElement: 'span',
				highlight: function( element, errorClass, validClass ) {
					$( element ).fadeOut( function() {
						$( element ).fadeIn().addClass( errorClass ).removeClass( validClass );
					} );
				},
				unhighlight: function( element, errorClass, validClass ) {
					$( element ).removeClass( errorClass ).addClass( validClass );
				},
				submitHandler: function( form ) {
					ajaxLogin( $( form ) );
				}
			});

			function ajaxLogin( $form ) {
				var $error     = $form.find( '.tm-re-login-form__error' ),
					$success   = $form.find( '.tm-re-login-form__success' ),
					login      = $form.find( 'input[name="user_login"]' ).val(),
					pass       = $form.find( 'input[name="user_pass"]' ).val(),
					nonce      = $form.find( 'input[name="tm-re-loginform-nonce"]' ).val(),
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
						CherryJsCore.variable.$document[0].location.reload( true );
						return 1;
					}

					$error.removeClass( hidden ).html( response.data.message );
					return !1;
				});

			};
		},

		registerForm: function( self ) {
			var $form = $( '#tm-re-registerform' );

			if ( ! $.isFunction( jQuery.fn.validate ) || ! $form.length ) {
				return !1;
			}

			$form.validate({
				debug: true, // disabled submit event
				messages: {
					user_login: CherryREData.messages,
					user_email: CherryREData.messages
				},
				errorElement: 'span',
				highlight: function( element, errorClass, validClass ) {
					$( element ).fadeOut( function() {
						$( element ).fadeIn().addClass( errorClass ).removeClass( validClass );
					} );
				},
				unhighlight: function( element, errorClass, validClass ) {
					$( element ).removeClass( errorClass ).addClass( validClass );
				},
				submitHandler: function( form ) {
					ajaxRegister( $( form ) );
				}
			});

			function ajaxRegister( $form ) {
				var $error      = $form.find( '.tm-re-register-form__error' ),
					$success    = $form.find( '.tm-re-register-form__success' ),
					login       = $form.find( 'input[name="user_login"]' ).val(),
					email       = $form.find( 'input[name="user_email"]' ).val(),
					nonce       = $form.find( 'input[name="tm-re-registerform-nonce"]' ).val(),
					processing  = 'processing',
					hidden      = 'tm-re-hidden';

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
						action: 'register_form',
						nonce: nonce,
						access: {
							login: login,
							email: email
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

						if ( $.isFunction( jQuery.fn.magnificPopup ) && null !== self.magnificPopup ) {
							self.magnificPopup.close();
						}

						return 1;
					}

					$error.removeClass( hidden ).html( response.data.message );
					return !1;
				});

			};
		},

		popup: function( self ) {
			var link = '.tm-re-popup',
				src  = '#' + CherryREData.popupid;

			if ( ! $( src ).length ) {
				return !1;
			}

			if ( ! $.isFunction( jQuery.fn.magnificPopup ) || ! $( link ).length ) {
				return !1;
			}

			self.magnificPopup = $.magnificPopup.instance;

			CherryJsCore.variable.$document.on( 'click', link, init );

			function init( event ) {
				event.preventDefault();

				var tab    = $( this ).data( 'tab' ),
					effect = $( src ).data( 'anim-effect' );

				self.magnificPopup.open({
					items: {
						src: src
					},
					type: 'inline',
					preloader: false,
					removalDelay: 500,
					mainClass: effect,
					callbacks: {
						beforeOpen: function() {
							var $forms = $( src ).find( 'form' );

							if ( $forms.length ) {
								$forms.each( function( i, form ) {
									form.reset();
								})
							}

							self.tabs( self, tab );
						}
					}
				});
			}
		},

		tabs: function( self, activeTab ) {
			var $target = $( '#' + CherryREData.popupid );

			if ( ! $.isFunction( jQuery.fn.tabs ) || ! $target.length ) {
				return !1;
			}

			$target.tabs({
				collapsible: false,
				active: activeTab
			});
		}

	};

	CherryJsCore.cherryRealEstate.start();

})( jQuery );