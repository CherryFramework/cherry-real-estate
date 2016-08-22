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
			self.uploadImages( self );
			self.previewLayouts( self );
			self.sort( self );
			self.location( self );
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

			var $error   = $form.find( '.tm-re-submission-form__error' ),
				$success = $form.find( '.tm-re-submission-form__success' ),
				hidden   = 'tm-re-hidden';

			$form.validate({
				debug: true, // disabled submit event
				messages: {
					property_title: CherryREData.messages,
					property_description: CherryREData.messages,
					property_price: CherryREData.messages,
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
				onkeyup: false,
				onfocusout: function( element ) {
					// native
					if ( ! this.checkable( element ) && ( element.name in this.submitted || ! this.optional( element ) ) ) {
						this.element( element );
					}

					if ( ! $error.hasClass( hidden ) ) {
						$error.addClass( hidden );
					}

					if ( ! $success.hasClass( hidden ) ) {
						$success.addClass( hidden );
					}
				},
				highlight: function( element, errorClass, validClass ) {
					$( element ).fadeOut( function() {
						$( element ).fadeIn().addClass( errorClass ).removeClass( validClass );
					} );
				},
				unhighlight: function( element, errorClass, validClass ) {
					$( element ).removeClass( errorClass ).addClass( validClass );
				},
				invalidHandler: function( event, validator ) {
					if ( ! $error.hasClass( hidden ) ) {
						$error.addClass( hidden );
					}

					if ( ! $success.hasClass( hidden ) ) {
						$success.addClass( hidden );
					}
				},
				submitHandler: function( form ) {
					ajaxSubmit( $( form ) );
				}
			});

			function ajaxSubmit( $form ) {
				var formData    = $form.serializeArray(),
					$source     = $form.find( '.tm-re-uploaded-ids' ),
					$preview    = $form.find( '.tm-re-uploaded-image' ),
					gallery_ids = $source.data( 'ids' ),
					nonce       = $form.find( 'input[name="tm-re-submissionform-nonce"]' ).val(),
					processing  = 'processing';

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
						property: formData,
						gallery: gallery_ids
					},
					error: function( jqXHR, textStatus, errorThrown ) {
						$form.removeClass( processing );
						$error.removeClass( hidden ).html( textStatus );
					}
				}).done( function( response ) {
					// console.log( response );

					$form.removeClass( processing );

					if ( true === response.success ) {
						$success.removeClass( hidden );
						$form[0].reset();
						$preview.remove();
						$source.data( 'ids', [] );
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
				onkeyup: false,
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
					error: function( jqXHR, textStatus, errorThrown ) {
						$form.removeClass( processing );
						$error.removeClass( hidden ).html( textStatus );
					}
				}).done( function( response ) {
					// console.log( response );

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
				onkeyup: false,
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
					error: function( jqXHR, textStatus, errorThrown ) {
						$form.removeClass( processing );
						$error.removeClass( hidden ).html( textStatus );
					}
				}).done( function( response ) {
					// console.log( response );

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
							var $forms = $( src ).find( 'form' ),
								hidden = 'tm-re-hidden';

							if ( $forms.length ) {
								$forms.each( function( i, form ) {
									$( form ).find( '.tm-re-messages span' ).addClass( hidden );
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
		},

		uploadImages: function( self ) {
			$( '.tm-re-image-upload' ).each( function() {
				var $this = $( this );

				if ( ! $.isFunction( jQuery.fn.fileupload ) || ! $this.length ) {
					return !1;
				}

				var $form           = $this.parents( 'form' ),
					$uploaded_files = $form.find( '.tm-re-uploaded-images' ),
					$submit_button  = $form.find( ':button[type="submit"]' ),
					$files_ids      = $form.find( '.tm-re-uploaded-ids' ),
					nonce           = $form.find( 'input[name="tm-re-submissionform-nonce"]' ).val(),
					name            = $this.attr( 'name' ),
					multiple        = $this.attr( 'multiple' ) ? 1 : 0,
					allowed_types   = $this.data( 'file_types' ),
					processing      = 'processing',
					hidden          = 'tm-re-hidden';

				if ( $form.hasClass( processing ) ) {
					return !1;
				}

				$this.fileupload({
					url: CherryREData.ajaxurl,
					type: 'post',
					dataType: 'json',
					singleFileUploads: true,
					dropZone: $this,
					autoUpload: false,
					previewMaxWidth: 150,
					previewMaxHeight: 150,
					previewCrop: true,
					formData: {
						nonce: nonce,
						name: name,
						action: 'upload_file',
						script: true
					},

				}).on( 'fileuploadadd', function( e, data ) {
					console.log( 'add' );

					var uploadErrors = [],
						html         = $.parseHTML( CherryREData.js_field_html_img );

					data.context = $( html );

					if ( allowed_types ) {
						var acceptFileTypes = new RegExp( "(\.|\/)(" + allowed_types + ")$", "i" );

						if ( data.files[0]['name'].length && ! acceptFileTypes.test( data.files[0]['name'] ) ) {
							uploadErrors.push( CherryREData.messages.invalid_file_type + ' ' + allowed_types );
						}
					}

					if ( uploadErrors.length > 0 ) {
						alert( uploadErrors.join( "\n" ) );

					} else {

						$.each( data.files, function( index, file ) {
							var $name = data.context.find( '.tm-re-uploaded-image__name' ),
								$btn  = data.context.find( '.tm-re-uploaded-image__btn' );

							$name.text( file.name );
							$btn.data( data );
							data.context.appendTo( $uploaded_files );
						});
					}
				})

				.on( 'fileuploadprocessstart', function( e ) {
					$submit_button.prop( 'disabled', true );
				})

				.on( 'fileuploadprocessalways', function( e, data ) {
					if ( ! $( data.context ).length ) {
						return !1;
					}

					// console.log(data.context);

					var index = data.index,
						file  = data.files[ index ],
						node  = $( data.context.children()[ index ] );

					if ( file.preview ) {
						node.prepend( file.preview );
					}

					if ( file.error ) {
						node
							.append( '<br>' )
							.append( $( '<span class="text-danger"/>' ).text( file.error ) );
					}

					if ( index + 1 === data.files.length ) {
						data.context.find( 'button' ).prop( 'disabled', !!data.files.error );
					}
				})

				.on( 'fileuploadprogressall', function( e, data ) {
					var progress = parseInt( data.loaded / data.total * 100, 10 );
					$( '.tm-re-uploaded-image__pregress').val( progress );
				})

				.on( 'fileuploaddone', function( e, data ) {
					console.log(data.context);

					var image_types = allowed_types.split( '|' ),
						response    = data.result;

					$form.removeClass( processing );

					if ( false === response.success ) {
						alert( response.data.message );
						return !1;
					}

					$.each( response.data.files, function( index, file ) {

						if ( file.error ) {

							alert( file.error );
							return -1;

						} else {
							var ids  = $files_ids.data( 'ids' );

							if ( -1 == $.inArray( file.extension, image_types ) ) {
								return -1;
							}

							ids.push( file.id );
							$files_ids.data( 'ids', ids );

							data.context.find( '.tm-re-uploaded-image__pregress' ).remove();
							data.context.find( '.tm-re-uploaded-image__btn' ).remove();

							$( data.context ).remove( 'progress' );
							$( data.context ).remove( 'button' );
						}
					});

					// $.each( data.result.files, function( index, file ) {

					// 	if ( file.url ) {
					// 		var link = $( '<a>' ).attr( 'target', '_blank' ).prop( 'href', file.url );

					// 		$( data.context.children()[ index ] ).wrap( link );

					// 	} else if ( file.error ) {
					// 		var error = $( '<span class="text-danger"/>' ).text( file.error );

					// 		$( data.context.children()[ index ] ).append( '<br>' ).append( error );
					// 	}
					// });
				});

				CherryJsCore.variable.$document.on( 'click', '.tm-re-uploaded-image__remove', remove );
				CherryJsCore.variable.$document.on( 'click', '.tm-re-uploaded-image__btn', upload );

				function remove( event ) {
					event.preventDefault();
					$( this ).closest( '.tm-re-uploaded-images__item' ).remove();
				};

				function upload( event ) {
					event.preventDefault();

					var $this     = $( this ),
						$item     = $this.closest( '.tm-re-uploaded-images__item' ),
						$progress = $item.find( 'progress' ),
						$remove   = $item.find( '.tm-re-uploaded-image__remove' ),
						data      = $this.data();

					$progress.removeClass( hidden );
					$form.addClass( processing );
					$remove.remove();

					// $this
					// 	.off( 'click' )
					// 	.text( 'Abort' )
					// 	.on( 'click', function() {
					// 		$this.remove();
					// 		data.abort();
					// 	});

					data.submit();

					// $this.remove();
					// $progress.addClass( hidden );
					// data.submit().always( function() {
					// 	console.log( 'submit fire' );
					// 	$this.remove();
					// });
				};

			});
		},

		previewLayouts: function( self ) {
			var $button = $( '.tm-re-switch-layout__btn' ),
				$items  = $( '#tm-re-property-items' );

			if ( ! ( $button.length && $items.length ) ) {
				return !1;
			}

			$button.on( 'click', init );

			function init( event ) {
				var $target     = $( event.target ),
					$form       = $target.parents( 'form' ),
					nonce       = $form.find( 'input[name="tm-re-switch-layout-nonce"]' ).val(),
					layout      = $target.val(),
					active      = 'tm-re-switch-layout__btn--active',
					listClass   = 'grid list',
					processing  = 'processing';

				if ( $target.hasClass( active ) ) {
					return !1;
				}

				if ( $form.hasClass( processing ) ) {
					return !1;
				}

				$form.addClass( processing );
				$items.addClass( processing );

				$.ajax({
					url: CherryREData.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'switch_layout',
						layout: layout,
						nonce: nonce
					},
					beforeSend: function() {
						if ( $button.hasClass( active ) ) {
							$button.removeClass( active );
						}

						$target.addClass( active );
						$items.toggleClass( listClass );
					},
					error: function() {
						$form.removeClass( processing );
						$items.removeClass( processing );
					}
				}).done( function( response ) {
					$form.removeClass( processing );
					$items.removeClass( processing );
				});
			}
		},

		sort: function( self ) {
			var $form = $( '#tm-re-property-sort' ),
				$sort = $form.find( 'select' );

			if ( ! $sort.length ) {
				return !1;
			}

			$sort.on( 'change', init );

			function init() {
				var search    = CherryJsCore.variable.$window[0].location.search,
					params    = self.getQueryParameters( search );

				params.properties_sort = $( this ).val();

				CherryJsCore.variable.$window[0].location.search = $.param( params );
			}
		},

		getQueryParameters: function( str ) {
			return (str || document.location.search).replace(/(^\?)/,'').split("&").map(function(n){return n = n.split("="),this[n[0]] = n[1],this}.bind({}))[0];
		},

		location: function( self ) {
			$( '.tm-re-map' ).each( function() {
				var data = $( this ).data( 'atts' ),
					$map;

				if ( 'object' != typeof data ) {
					return !1;
				}

				$map = $( '#' + data.id );

				if ( ! $.isFunction( jQuery.fn.RELocations ) || ! $map.length ) {
					return !1;
				}

				$map.RELocations( data );
			} );
		}

	};

	CherryJsCore.cherryRealEstate.start();

})( jQuery );