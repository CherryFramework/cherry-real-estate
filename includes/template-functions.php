<?php
/**
 * Functions for the templating system.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/* Global --------------------------------------------------------------*/

if ( ! function_exists( 'cherry_real_estate_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_output_content_wrapper() {
		cherry_re_get_template( 'global/wrapper-start' );
	}
}

if ( ! function_exists( 'cherry_real_estate_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_output_content_wrapper_end() {
		cherry_re_get_template( 'global/wrapper-end' );
	}
}

if ( ! function_exists( 'cherry_real_estate_get_property_sidebar' ) ) {

	/**
	 * Get the sidebar template.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_get_property_sidebar() {
		cherry_re_get_template( 'global/sidebars/property' );
	}
}

if ( ! function_exists( 'cherry_real_estate_get_search_sidebar' ) ) {

	/**
	 * Get the sidebar template for search page.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_get_search_sidebar() {
		cherry_re_get_template( 'global/sidebars/search' );
	}
}


/* Single Property --------------------------------------------------------------*/

if ( ! function_exists( 'cherry_real_estate_template_single_title' ) ) {

	/**
	 * Output the property title.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_template_single_title( $callbacks ) {
		cherry_re_get_template( 'single-property/title' );
	}
}

if ( ! function_exists( 'cherry_real_estate_show_property_gallery' ) ) {

	/**
	 * Output the property thumbnails.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_show_property_gallery( $callbacks ) {
		cherry_re_get_template( 'single-property/gallery', array( 'callbacks' => $callbacks ) );
	}
}

if ( ! function_exists( 'cherry_real_estate_template_single_price' ) ) {

	/**
	 * Output the product price.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_template_single_price( $callbacks ) {
		cherry_re_get_template( 'single-property/price', array( 'callbacks' => $callbacks ) );
	}
}

if ( ! function_exists( 'cherry_real_estate_property_description' ) ) {

	/**
	 * Output the description content.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_property_description( $callbacks ) {
		cherry_re_get_template( 'single-property/description' );
	}
}

if ( ! function_exists( 'cherry_real_estate_property_attributes' ) ) {

	/**
	 * Output the property attributes.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_property_attributes( $callbacks ) {
		cherry_re_get_template( 'single-property/attributes', array( 'callbacks' => $callbacks ) );
	}
}

if ( ! function_exists( 'cherry_real_estate_property_map' ) ) {

	/**
	 * Output the property map.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_property_map( $callbacks ) {
		cherry_re_get_template( 'single-property/map', array( 'callbacks' => $callbacks ) );
	}
}

if ( ! function_exists( 'cherry_real_estate_property_agent' ) ) {

	/**
	 * Output the property map.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_property_agent( $callbacks ) {
		cherry_re_get_template( 'single-property/agent', array( 'callbacks' => $callbacks ) );
	}
}


/* Agent Archive --------------------------------------------------------------*/

if ( ! function_exists( 'cherry_real_estate_agent_info' ) ) {

	/**
	 * Output the agent info.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_agent_info( $agent_callbacks ) {
		cherry_re_get_template( 'single-agent/info', array( 'callbacks' => $agent_callbacks ) );
	}
}

if ( ! function_exists( 'cherry_real_estate_agent_map' ) ) {

	/**
	 * Output the agent map.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_agent_map( $callbacks ) {
		cherry_re_get_template( 'single-agent/map' );
	}
}

if ( ! function_exists( 'cherry_real_estate_output_agent_archive_wrapper' ) ) {

	/**
	 * Output the start of the wrapper.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_output_agent_archive_wrapper() {
		echo '<div class="tm-property-items">';
	}
}

if ( ! function_exists( 'cherry_real_estate_output_agent_archive_wrapper_end' ) ) {

	/**
	 * Output the end of the wrapper.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_output_agent_archive_wrapper_end() {
		echo '</div><!-- .tm-property-items -->';
	}
}


/* Search Result --------------------------------------------------------------*/

if ( ! function_exists( 'cherry_real_estate_search_map_result' ) ) {

	/**
	 * Output the search map result.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_search_map_result() {
		if ( cherry_re_is_property_search() ) {
			cherry_re_get_template( 'search/result' );
		}
	}
}

if ( ! function_exists( 'cherry_real_estate_listing_controls' ) ) {

	/**
	 * Output the listing controls.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_listing_controls() {
		cherry_re_get_template( 'misc/listing-controls', array(
			'layout_control' => cherry_re_get_template_html( 'misc/switch-layout' ),
			'sort_control'   => cherry_re_get_template_html( 'misc/sort', array(
				'options' => Model_Submit_Form::get_sort_options(),
				'value'   => ! empty( $_GET['properties_sort'] ) ? esc_attr( $_GET['properties_sort'] ) : '',
			) ),
		) );
	}
}


/* Submission Form --------------------------------------------------------------*/

if ( ! function_exists( 'cherry_real_estate_auth_message_link' ) ) {

	/**
	 * Output the message with login/register links.
	 *
	 * @since 1.0.0
	 */
	function cherry_real_estate_auth_message_link() {
		cherry_re_get_template( 'auth/message-link', array(
			'class' => 'tm-re-popup',
			'href'  => Model_Submit_Form::get_popup_id(),
		) );
	}
}
