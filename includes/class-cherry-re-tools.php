<?php
/**
 * Plugin tools.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */
class Cherry_RE_Tools {

	/**
	 * Returns social icons set
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_icons_set() {

		ob_start();

		include CHERRY_REAL_ESTATE_DIR . 'assets/js/icons.json';
		$json = ob_get_clean();

		$result = array();

		$icons = json_decode( $json, true );

		foreach ( $icons['icons'] as $icon ) {
			$result[] = $icon['id'];
		}

		return $result;
	}

	/**
	 * Get size information for all currently-registered image sizes.
	 *
	 * @since  1.0.0
	 * @global $_wp_additional_image_sizes
	 * @uses   get_intermediate_image_sizes()
	 * @return array $sizes Data for all currently-registered image sizes.
	 */
	public static function get_image_sizes() {
		global $_wp_additional_image_sizes;

		$sizes = array();

		foreach ( get_intermediate_image_sizes() as $_size ) {
			if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
				$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
				$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
				$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
				);
			}
		}

		return $sizes;
	}

	/**
	 * Get size information for a specific image size.
	 *
	 * @since  1.0.0
	 * @uses   get_image_sizes()
	 * @param  string $size The image size for which to retrieve data.
	 * @return bool|array $size Size data about an image size or false if the size doesn't exist.
	 */
	public static function get_image_size( $size ) {
		$sizes = self::get_image_sizes();

		if ( isset( $sizes[ $size ] ) ) {
			return $sizes[ $size ];
		}

		return false;
	}

	/**
	 * Retrieve a URI for Google Maps API.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_google_map_url() {
		$url     = '//maps.googleapis.com/maps/api/js';
		$api_key = Model_Settings::get_map_api_key();

		$query = apply_filters( 'cherry_re_google_map_url_query', array(
			'key' => $api_key,
		) );

		$url = add_query_arg( $query, $url );

		return apply_filters( 'cherry_re_google_map_url', $url, $query );
	}

	/**
	 * Display or retrieve a HTML-formatted select element.
	 *
	 * @since  1.0.0
	 * @param  array  $options Options.
	 * @param  array  $args    Arguments.
	 * @return string
	 */
	public static function select_form( $options, $args ) {

		if ( empty( $options ) ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'id'      => '',
			'name'    => '',
			'default' => '',
			'value'   => '',
			'echo'    => true,
		) );

		$html = '';

		if ( ! empty( $args['default'] ) ) {
			$html .= sprintf( '<option value="">%s</option>', esc_html( $args['default'] ) );
		}

		foreach ( $options as $key => $value ) {
			$selected = selected( $args['value'], $key, false );
			$html .= sprintf( '<option value="%1$s" %3$s>%2$s</option>', esc_attr( $key ), esc_html( $value ), $selected );
		}

		$id = $args['id'];

		if ( empty( $id ) ) {
			$id = $args['name'];
		}

		$html = sprintf(
			'<select id="%2$s" name="%1$s">%3$s</select>',
			esc_attr( $args['name'] ),
			esc_attr( $id ),
			$html
		);

		if ( true == $args['echo'] ) {
			echo $html;
		} else {
			return $html;
		}
	}
}
