<?php
/**
 * The Template for displaying property archives.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/archive-propery.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

if ( ! did_action( 'get_header' ) ) {
	get_header( 'property' );

	/**
	 * Fires before the content divs are opened.
	 *
	 * @since 1.0.0
	 *
	 * @hooked cherry_real_estate_output_content_wrapper - 10 (outputs opening divs for the content)
	 */
	do_action( 'cherry_re_before_main_content' );
}

/**
 * Fires before the archive description printed.
 *
 * @since 1.0.0
 */
do_action( 'cherry_re_archive_description' );

$data   = Cherry_RE_Property_Data::get_instance();
$params = $data->prepare_search_args();
$args   = apply_filters( 'cherry_re_archive_template_args', array(
	'number'          => Model_Settings::get_listing_per_page(),
	'show_pagination' => true,
	'tax_query'       => ! empty( $wp_query->tax_query->queries ) ? $wp_query->tax_query->queries : false,
	'template'        => 'archive.tmpl',
	'css_id'          => 'tm-re-property-items',
	'css_class'       => Model_Settings::get_listing_layout(),
) );
$args = wp_parse_args( $args, $params );

/**
 * Fires before the archive listing.
 *
 * @since 1.0.0
 */
do_action( 'cherry_re_before_property_loop' );

$data->the_property( $args );

/**
 * Fires after the archive listing.
 *
 * @since 1.0.0
 */
do_action( 'cherry_re_after_property_loop' );

if ( did_action( 'cherry_re_before_main_content' ) ) {

	/**
	 * Fires after the content divs are closed.
	 *
	 * @since 1.0.0
	 *
	 * @hooked cherry_real_estate_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action( 'cherry_re_after_main_content' );

	/**
	 * Fires before the sidebar template file is loaded.
	 *
	 * @since 1.0.0
	 *
	 * @hooked cherry_real_estate_get_sidebar - 10
	 */
	do_action( 'cherry_re_property_sidebar' );

	get_footer( 'property' );
}
