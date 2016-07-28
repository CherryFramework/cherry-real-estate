/* global CherryRETinyMCE */
(function( $ ) {
	var prefix = CherryRETinyMCE.prefix,
		button = CherryRETinyMCE.button,
		shortcodes = JSON.parse( CherryRETinyMCE.shortcodes );

	tinymce.PluginManager.add( button, function( editor ) {
		var menu = [];

		$.each( shortcodes, function( key, value ) {
			var item = {};

			item['text']    = value['title'];
			item['onclick'] = function() {

				editor.windowManager.open({
					title: value['title'],
					icon: value['icon'],
					body: value['body'],
					minWidth: 600,
					onsubmit: function( e ) {
						var attr = e.data,
							shortcode,
							prop;

						shortcode = '[' + prefix + key;

						for ( prop in attr ) {
							if ( attr[ prop ] ) {
								shortcode += ' ' + prop + '="' + attr[ prop ] + '"';
							}
						}

						shortcode += ']';

						editor.selection.setContent( shortcode );
					}
				});
			};

			menu.push( item );
		});

		editor.addButton( button, {
			title: CherryRETinyMCE.title,
			type: 'menubutton',
			icon: 'icon dashicons-before dashicons-building',
			menu: menu
		} );
	});

}( jQuery ));