<?php
/**
 * The Template for displaying all single properties.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/re-agent.php.
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
	 * Fires before the single property.
	 *
	 * @since 1.0.0
	 */
	do_action( 'cherry_re_before_single_property' );

	// Start the Loop.
	while ( have_posts() ) : the_post();
		cherry_re_get_template_part( 'content', 'single-property' );
	endwhile; // end of the loop.

	/**
	 * Fires after the single property.
	 *
	 * @since 1.0.0
	 */
	do_action( 'cherry_re_after_single_property' );

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
