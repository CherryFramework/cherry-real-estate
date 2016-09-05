<?php
/**
 * Core Functions.
 *
 * General core functions available on both the front-end and admin.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Functions
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Load a template part into a template (for templates like the `property-loop.php`).
 *
 * @since 1.0.0
 * @param string $slug The slug name for the generic template.
 * @param string $name The name of the specialised template.
 */
function cherry_re_get_template_part( $slug, $name = null ) {
	$name      = (string) $name;
	$templates = array();
	$template  = '';

	/**
	 * Look a specialised template in theme:
	 *
	 * - yourtheme/slug-name.php
	 * - yourtheme/real-estate/slug-name.php
	 */
	if ( '' !== $name ) {
		$templates[] = "{$slug}-{$name}.php";
		$templates[] = cherry_real_estate()->template_path() . "{$slug}-{$name}.php";

		$template = locate_template( $templates );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( CHERRY_REAL_ESTATE_DIR . "templates/{$slug}-{$name}.php" ) ) {
		$template = CHERRY_REAL_ESTATE_DIR . "templates/{$slug}-{$name}.php";
	}

	/**
	 * If template file doesn't exist, look a generic template in theme:
	 *
	 * - yourtheme/slug.php
	 * - yourtheme/real-estate/slug.php
	 */
	if ( ! $template ) {
		$templates[] = "{$slug}.php";
		$templates[] = cherry_real_estate()->template_path() . "{$slug}.php";

		$template = locate_template( $templates );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'cherry_re_get_template_part', $template, $slug, $name );

	// If template is found, include it.
	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Retrieve the path of the highest priority template file.
 *
 * @since  1.0.0
 * @param  string $template_name Template name.
 * @param  string $template_path Relative path to template's directory in theme.
 * @param  string $default_path  Absolute path to template's directory in plugin.
 * @return string
 */
function cherry_re_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	$templates = array();

	if ( ! $template_path ) {
		$template_path = cherry_real_estate()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = CHERRY_REAL_ESTATE_DIR . 'templates/';
	}

	/**
	 * Look in theme:
	 *
	 * - yourtheme/template_name.php
	 * - yourtheme/real-estate/template_name.php
	 */
	$templates[] = "{$template_name}.php";
	$templates[] = trailingslashit( $template_path ) . "{$template_name}.php";

	$template = locate_template( $templates );

	// Get default template.
	if ( ! $template ) {
		$template = $default_path . "{$template_name}.php";
	}

	// Return what we found.
	return apply_filters( 'cherry_re_locate_template', $template, $template_name, $template_path );
}

/**
 * Get other templates (e.g. `single-property/attributes.php`) passing attributes and including the file.
 *
 * @since 1.0.0
 * @param string $template_name Template name.
 * @param array  $passed_vars   Arguments.
 * @param string $template_path Relative path to template's directory in theme.
 * @param string $default_path  Absolute path to template's directory in plugin.
 */
function cherry_re_get_template( $template_name, $passed_vars = array(), $template_path = '', $default_path = '' ) {
	$located = cherry_re_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		return sprintf( '<code>%s</code> does not exist.', $located );
	}

	$passed_vars = apply_filters( 'cherry_re_get_template_passed_vars', $passed_vars, $template_name, $template_path );

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'cherry_re_get_template',
		$located, $template_name, $passed_vars, $template_path, $default_path );

	do_action( 'cherry_re_before_template_part', $template_name, $template_path, $located, $passed_vars );

	include( $located );

	do_action( 'cherry_re_after_template_part', $template_name, $template_path, $located, $passed_vars );
}

/**
 * Like `cherry_re_get_template` function, but returns the HTML instead of outputting.
 *
 * @since 1.0.0
 * @param string $template_name Template name.
 * @param array  $args          Arguments.
 * @param string $template_path Relative path to template's directory in theme.
 * @param string $default_path  Absolute path to template's directory in plugin.
 */
function cherry_re_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	ob_start();
	cherry_re_get_template( $template_name, $args, $template_path, $default_path );

	return ob_get_clean();
}

/**
 * Display a string with HTML5-data attributes.
 *
 * @since 1.0.0
 * @param array $atts   Data attributes.
 * @param bool  $single True if data attributes needs print as a single attribute. False - multiple attributes.
 */
function cherry_re_print_data_atts( $atts, $single = false ) {
	echo cherry_re_return_data_atts( $atts, $single );
}

/**
 * Retrieve a string with HTML5-data attributes.
 *
 * @since  1.0.0
 * @param  array $atts   Data attributes.
 * @param  bool  $single True if data attributes needs print as a single attribute. False - multiple attributes.
 * @return string
 */
function cherry_re_return_data_atts( $atts, $single = false ) {

	if ( ! is_array( $atts ) || empty( $atts ) ) {
		return;
	}

	$data = array();

	if ( false === $single ) {

		foreach ( $atts as $name => $value ) {

			if ( is_bool( $value ) ) {
				$value = ( true === $value ) ? 'true' : 'false';
			}

			if ( is_array( $value ) ) {
				$value = wp_json_encode( $value );
			}

			if ( empty( $value ) ) {
				continue;
			}

			$data[] = sprintf( "data-%s='%s'", esc_attr( $name ), esc_attr( $value ) );
		}
	} else {
		$data[] = sprintf( "data-atts='%s'", wp_json_encode( $atts ) );
	}

	return join( $data, ' ' );
}

/**
 * Is the query for an existing property taxonomy archive page?
 *
 * @since 1.0.0
 * @return bool
 */
function cherry_re_is_property_taxonomy() {
	$post_type = cherry_real_estate()->get_post_type_name();

	return is_tax( get_object_taxonomies( $post_type ) );
}

/**
 * Is the query for a property search?
 *
 * @since 1.0.0
 * @return bool
 */
function cherry_re_is_property_search() {

	if ( empty( $_GET ) ) {
		return false;
	}

	$query_post_type = get_query_var( 'post_type', false );

	if ( false === $query_post_type ) {
		return false;
	}

	$post_type = cherry_real_estate()->get_post_type_name();

	return ( is_search() && $query_post_type == $post_type );
}

/**
 * Is the query for an existing property archive page?
 *
 * @since 1.0.0
 * @return bool
 */
function cherry_re_is_property_listing() {
	$post_type    = cherry_real_estate()->get_post_type_name();
	$listing_page = Model_Settings::get_listing_page();

	return is_post_type_archive( $post_type ) || ( is_page( $listing_page ) && '' !== $listing_page );
}

/**
 * Is the query for an existing RE agent archive page?
 *
 * @since 1.0.0
 * @return bool
 */
function cherry_re_is_agent() {

	if ( ! is_author() ) {
		return false;
	}

	$author = get_queried_object();

	if ( ! ( $author instanceof WP_User ) ) {
		return false;
	}

	$roles = ! empty( $author->roles ) ? $author->roles : array();

	if ( in_array( 're_agent', $roles ) ) {
		return true;
	}

	return false;
}
