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
	 * cherry_re_before_main_content hook.
	 *
	 * @since 1.0.0
	 *
	 * @hooked cherry_real_estate_output_content_wrapper - 10 (outputs opening divs for the content)
	 */
	do_action( 'cherry_re_before_main_content' );
}

/**
 * cherry_re_archive_description hook.
 *
 * @since 1.0.0
 *
 * @hooked
 */
do_action( 'cherry_re_archive_description' );

$args = apply_filters( 'cherry_re_archive_template_args', array(
	'number'          => get_query_var( 'posts_per_page', 10 ),
	'show_pagination' => true,
	'tax_query'       => ! empty( $wp_query->tax_query->queries ) ? $wp_query->tax_query->queries : false,
	'template'        => 'archive.tmpl',
) );
$data = Cherry_RE_Property_Data::get_instance();

/**
 * cherry_re_before_property_loop hook.
 *
 * @since 1.0.0
 */
do_action( 'cherry_re_before_property_loop' );

$data->the_property( $args );

/**
 * cherry_re_after_property_loop hook.
 *
 * @since 1.0.0
 */
do_action( 'cherry_re_after_property_loop' );


if ( did_action( 'cherry_re_before_main_content' ) ) {

	/**
	 * cherry_re_after_main_content hook.
	 *
	 * @since 1.0.0
	 *
	 * @hooked cherry_real_estate_output_content_wrapper_end - 10 (outputs closing divs for the content)
	 */
	do_action( 'cherry_re_after_main_content' );

	/**
	 * cherry_re_sidebar hook.
	 *
	 * @since 1.0.0
	 *
	 * @hooked cherry_real_estate_get_sidebar - 10
	 */
	do_action( 'cherry_re_sidebar' );

	get_footer( 'property' );
}
