<?php
/**
 * The template for displaying Agent archive pages.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/single-propery.php.
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

$callbacks = cherry_re_templater()->setup_template_data();

/**
 * Fires before the agent archive listing.
 *
 * @since 1.0.0
 *
 * @hooked cherry_real_estate_agent_info - 5
 */
do_action( 'cherry_re_before_agent_archive', $callbacks );

$args = apply_filters( 'cherry_re_agent_archive_template_args', array(
	'number'          => get_query_var( 'posts_per_page', 10 ),
	'author'          => get_query_var( 'author' ),
	'css_id'          => Model_Agents::get_property_wrap_id(),
	'show_pagination' => true,
	'echo'            => false,
	'template'        => 'archive.tmpl',
) );

$data       = Cherry_RE_Property_Data::get_instance();
$properties = $data->the_property( $args );

/**
 * Fires when the agent archive listing are ready.
 *
 * @since 1.0.0
 *
 * @hooked cherry_real_estate_agent_map - 5
 * @hooked cherry_real_estate_output_agent_archive_wrapper - 10
 */
do_action( 'cherry_re_start_agent_archive', $callbacks );

// Output HTML-formatted properties.
echo $properties;

/**
 * Fires when agent archive listing are printed.
 *
 * @since 1.0.0
 *
 * @hooked cherry_real_estate_output_agent_archive_wrapper_end - 10
 */
do_action( 'cherry_re_end_agent_archive' );


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
