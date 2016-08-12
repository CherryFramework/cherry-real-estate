jQuery( function( $ ) {
	$( '.wp-job-manager-file-upload' ).each( function() {
		$( this ).fileupload({
			url: job_manager_ajax_file_upload.ajax_url,
			type: 'post',
			dataType: 'json',
			singleFileUploads: true,
			dropZone: $( this ),
			formData: {
				nonce: $( this ).closest( 'form' ).find( 'input[name="tm-re-submissionform-nonce"]' ).val(),
				name: $( this ).attr( 'name' ),
				action: 'baz',
				script: true
			},
			add: function ( e, data ) {
				var $file_field     = $( this ),
					$form           = $file_field.closest( 'form' ),
					$uploaded_files = $file_field.parent().find( '.job-manager-uploaded-files' ),
					uploadErrors    = [],
					allowed_types   = $( this ).data( 'file_types' );

				if ( allowed_types ) {
					var acceptFileTypes = new RegExp( "(\.|\/)(" + allowed_types + ")$", "i" );

					if ( data.originalFiles[0]['name'].length && ! acceptFileTypes.test( data.originalFiles[0]['name'] ) ) {
						uploadErrors.push( job_manager_ajax_file_upload.i18n_invalid_file_type + ' ' + allowed_types );
					}
				}

				// console.log(uploadErrors);

				if ( uploadErrors.length > 0 ) {
					alert( uploadErrors.join( "\n" ) );
				} else {
					$form.find(':button[type="submit"]').attr( 'disabled', 'disabled' );
					data.context = $('<progress value="" max="100"></progress>').appendTo( $uploaded_files );
					data.submit();
				}
			},
			progress: function ( e, data ) {
				var $file_field     = $( this ),
					$uploaded_files = $file_field.parent().find('.job-manager-uploaded-files'),
					progress        = parseInt( data.loaded / data.total * 100, 10 );

				data.context.val( progress );
			},
			fail: function ( e, data ) {
				var $file_field = $( this ),
					$form       = $file_field.closest( 'form' );

				if ( data.errorThrown ) {
					alert( data.errorThrown );
				}

				data.context.remove();

				$form.find(':button[type="submit"]').removeAttr( 'disabled' );
			},
			done: function ( e, data ) {
				var $file_field     = $( this ),
					$form           = $file_field.closest( 'form' ),
					$uploaded_files = $file_field.parent().find( '.job-manager-uploaded-files' ),
					multiple        = $file_field.attr( 'multiple' ) ? 1 : 0,
					image_types     = [ 'jpg', 'gif', 'png', 'jpeg', 'jpe' ];

				data.context.remove();

				console.log( data.result );

				$.each( data.result.files, function( index, file ) {
					if ( file.error ) {
						alert( file.error );
					} else {
						if ( $.inArray( file.extension, image_types ) >= 0 ) {
							var html = $.parseHTML( job_manager_ajax_file_upload.js_field_html_img );
							$( html ).find('.job-manager-uploaded-file-preview img').attr( 'src', file.url );
						} else {
							var html = $.parseHTML( job_manager_ajax_file_upload.js_field_html );
							$( html ).find('.job-manager-uploaded-file-name code').text( file.name );
						}

						$( html ).find('.input-text').val( file.url );
						$( html ).find('.input-text').attr( 'name', 'current_' + $file_field.attr( 'name' ) );

						if ( multiple ) {
							$uploaded_files.append( html );
						} else {
							$uploaded_files.html( html );
						}
					}
				});

				$form.find(':button[type="submit"]').removeAttr( 'disabled' );
			}
		});
	});
});
