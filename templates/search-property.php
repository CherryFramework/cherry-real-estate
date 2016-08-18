<?php
/**
 * The Template for displaying search results for properties.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/search-propery.php.
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
	 * @hooked cherry_real_estate_search_map_result - 5
	 * @hooked cherry_real_estate_output_content_wrapper - 10 (outputs opening divs for the content)
	 */
	do_action( 'cherry_re_before_main_content' );
}

/**
 * cherry_re_before_search_loop hook.
 *
 * @since 1.0.0
 *
 * @hooked cherry_real_estate_switch_layout - 5
 * @hooked cherry_real_estate_property_sort - 10
 */
do_action( 'cherry_re_before_search_loop' );

$data   = Cherry_RE_Property_Data::get_instance();
$params = $data->prepare_search_args();
$args   = array(
	'css_id'    => 'tm-re-search-items',
	'css_class' => Model_Settings::get_search_layout(),
);
$args = wp_parse_args( $args, $params );

$data->the_property( $args );

/**
 * cherry_re_after_search_loop hook.
 *
 * @since 1.0.0
 */
do_action( 'cherry_re_after_search_loop' );

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
	 * cherry_re_search_sidebar hook.
	 *
	 * @since 1.0.0
	 *
	 * @hooked cherry_real_estate_get_search_sidebar - 10
	 */
	do_action( 'cherry_re_search_sidebar' );

	get_footer( 'property' );
}
