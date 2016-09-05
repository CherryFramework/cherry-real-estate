<?php
/**
 * Search widget for Real Estate.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Widgets
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

if ( ! class_exists( 'Cherry_RE_Search_Widget' ) ) {

	/**
	 * Class for Search widget.
	 *
	 * @since 1.0.0
	 */
	class Cherry_RE_Search_Widget extends Cherry_Abstract_Widget {

		/**
		 * Constructor method.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->widget_cssclass    = 'widget-tm-re-search';
			$this->widget_description = esc_html__( 'Display a search form with map.', 'cherry-real-estate' );
			$this->widget_id          = 'tm_re_search_form';
			$this->widget_name        = esc_html__( 'Cherry RE Search', 'cherry-real-estate' );
			$this->settings           = array(
				'title'  => array(
					'type'  => 'text',
					'value' => '',
					'label' => esc_html__( 'Title:', 'cherry-real-estate' ),
				),
				'map_visibility' => array(
					'type'   => 'switcher',
					'value'  => 'true',
					'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
					'label'  => esc_html__( 'Show map', 'cherry-real-estate' ),
					'toggle' => array(
						'true_toggle'  => esc_html__( 'Yes', 'cherry-real-estate' ),
						'false_toggle' => esc_html__( 'No', 'cherry-real-estate' ),
						'true_slave'   => 'map_relation',
						'false_slave'  => '',
					),
				),
				'marker_number' => array(
					'type'      => 'stepper',
					'value'     => 10,
					'max_value' => 9999,
					'min_value' => 1,
					'label'     => esc_html__( 'Visible markers on map', 'cherry-real-estate' ),
					'master'    => 'map_relation',
				),
				'form_visibility' => array(
					'type'   => 'switcher',
					'value'  => 'true',
					'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
					'label'  => esc_html__( 'Show form', 'cherry-real-estate' ),
					'toggle' => array(
						'true_toggle'  => esc_html__( 'Yes', 'cherry-real-estate' ),
						'false_toggle' => esc_html__( 'No', 'cherry-real-estate' ),
					),
				),
			);

			add_action( 'wp_ajax_switch_layout', array( $this, 'switch_layout_callback' ) );
			add_action( 'wp_ajax_nopriv_switch_layout', array( $this, 'switch_layout_callback' ) );

			parent::__construct();
		}

		/**
		 * Widget function.
		 *
		 * @see   WP_Widget
		 * @since 1.0.0
		 * @param array $args     Display arguments.
		 * @param array $instance Settings for the current Text widget instance.
		 */
		public function widget( $args, $instance ) {

			if ( $this->get_cached_widget( $args ) ) {
				return;
			}

			$this->setup_widget_data( $args, $instance );

			$form_visibility = ! empty( $instance['form_visibility'] ) ? $instance['form_visibility'] : $this->settings['form_visibility']['value'];
			$form_visibility = filter_var( $form_visibility, FILTER_VALIDATE_BOOLEAN );

			$map_visibility = ! empty( $instance['map_visibility'] ) ? $instance['map_visibility'] : $this->settings['map_visibility']['value'];
			$map_visibility = filter_var( $map_visibility, FILTER_VALIDATE_BOOLEAN );

			if ( ! $form_visibility && ! $map_visibility ) {
				$this->reset_widget_data();
				return;
			}

			ob_start();

			echo $args['before_widget'];

			$title  = $this->widget_start( $args, $instance );
			$number = ! empty( $instance['marker_number'] ) ? $instance['marker_number'] : $this->settings['marker_number']['value'];

			$data     = Cherry_RE_Property_Data::get_instance();
			$defaults = $data->get_search_defaults();
			$get_args = array_map( 'esc_attr', $_GET );
			$values   = wp_parse_args( $get_args, $defaults );

			if ( $form_visibility ) {

				// Search form template.
				cherry_re_get_template( 'widgets/search/form', array(
					'title'  => $title,
					'values' => $values,
				) );

			}

			if ( $map_visibility ) {

				// Map template.
				cherry_re_get_template( 'widgets/search/map', array(
					'number' => $number,
				) );
			}

			$this->widget_end( $args );
			$this->reset_widget_data();

			echo $this->cache_widget( $args, ob_get_clean() );
		}

		/**
		 * Output the html at the start of a widget
		 *
		 * @since  1.0.0
		 * @param  array $args     Widget arguments.
		 * @param  array $instance Widget instance.
		 * @return string
		 */
		public function widget_start( $args, $instance ) {

			$title = apply_filters(
				'widget_title',
				empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base
			);

			if ( $title ) {
				return $args['before_title'] . $title . $args['after_title'];
			}
		}

		/**
		 * Switching layout.
		 *
		 * @return void
		 */
		public function switch_layout_callback() {

			// Check a nonce.
			$security = check_ajax_referer( '_tm-re-switch-layout', 'nonce', false );

			if ( false === $security ) {
				wp_send_json_error( array(
					'message' => esc_html__( 'Internal error. Please, try again later.', 'cherry-real-estate' ),
				) );
			}

			$saved_layout  = Model_Settings::get_listing_layout();
			$passed_layout = ! empty( $_POST['layout'] ) ? esc_attr( $_POST['layout'] ) : false;

			if ( ! in_array( $passed_layout, array( 'grid', 'list' ) ) ) {
				$passed_layout = 'grid';
			}

			if ( $saved_layout !== $passed_layout ) {
				$listing_options = get_option( 'cherry-re-options-listing', array() );
				$new_options     = array_merge( $listing_options, array( 'layout' => $passed_layout ) );
				update_option( 'cherry-re-options-listing', $new_options );
			}

			wp_send_json_success();
		}
	}

	add_action( 'widgets_init', 'cherry_re_register_search_widget' );

	/**
	 * Register a `Cherry RE Search` widget.
	 *
	 * @since 1.0.0
	 */
	function cherry_re_register_search_widget() {
		register_widget( 'Cherry_RE_Search_Widget' );
	}
}
