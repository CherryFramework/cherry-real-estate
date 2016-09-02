<?php
/**
 * Plugin Template Hooks.
 *
 * Action/filter hooks used for plugin functions/templates.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Content Wrappers.
 */
add_action( 'cherry_re_before_main_content', 'cherry_real_estate_output_content_wrapper', 10 );
add_action( 'cherry_re_after_main_content',  'cherry_real_estate_output_content_wrapper_end', 10 );

/**
 * Sidebar.
 */
add_action( 'cherry_re_property_sidebar', 'cherry_real_estate_get_property_sidebar', 10 );
add_action( 'cherry_re_search_sidebar', 'cherry_real_estate_get_search_sidebar', 10 );

/**
 * Single Property.
 */
add_action( 'cherry_re_before_single_property_summary', 'cherry_real_estate_template_single_title', 10 );
add_action( 'cherry_re_before_single_property_summary', 'cherry_real_estate_show_property_gallery', 15 );
add_action( 'cherry_re_single_property_summary',        'cherry_real_estate_template_single_price', 5 );
add_action( 'cherry_re_single_property_summary',        'cherry_real_estate_property_description', 10 );
add_action( 'cherry_re_single_property_summary',        'cherry_real_estate_property_attributes', 15 );
add_action( 'cherry_re_after_single_property_summary',  'cherry_real_estate_property_map', 5 );
add_action( 'cherry_re_after_single_property_summary',  'cherry_real_estate_property_agent', 10 );

/**
 * Agent Archive.
 */
add_action( 'cherry_re_before_agent_archive', 'cherry_real_estate_agent_info', 5 );
add_action( 'cherry_re_start_agent_archive',  'cherry_real_estate_agent_map', 5 );
add_action( 'cherry_re_start_agent_archive',  'cherry_real_estate_output_agent_archive_wrapper', 10 );
add_action( 'cherry_re_end_agent_archive',    'cherry_real_estate_output_agent_archive_wrapper_end', 10 );


/**
 * Property Archive.
 */
add_action( 'cherry_re_before_property_loop', 'cherry_real_estate_listing_controls', 10 );


/**
 * Search Property Result.
 */
add_action( 'cherry_re_before_main_content', 'cherry_real_estate_search_map_result', 5 );
add_action( 'cherry_re_output_search_result_map', 'cherry_real_estate_search_map_result', 10 );
add_action( 'cherry_re_before_search_loop', 'cherry_real_estate_listing_controls', 10 );

/**
 * Submission Form.
 */
add_action( 'cherry_re_before_submission_form', 'cherry_real_estate_auth_message_link', 10 );
add_action( 'cherry_re_before_submission_form_btn', 'cherry_real_estate_auth_message_link', 10 );
