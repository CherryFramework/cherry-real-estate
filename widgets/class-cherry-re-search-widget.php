<?php
/**
 * Search widget for Real Estate.
 *
 * @package __Tm
 */

class Cherry_RE_Search_Widget extends Cherry_Abstract_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget-tm-re-search';
		$this->widget_description = esc_html__( 'Display a search form with map.', '__tm' );
		$this->widget_id          = 'tm_re_search_form';
		$this->widget_name        = esc_html__( 'Cherry RE Search', '__tm' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'value' => '',
				'label' => esc_html__( 'Title:', '__tm' ),
			),
			'map_visibility' => array(
				'type'   => 'switcher',
				'value'  => 'true',
				'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
				'label'  => esc_html__( 'Show map', '__tm' ),
				'toggle' => array(
					'true_toggle'  => esc_html__( 'Yes', '__tm' ),
					'false_toggle' => esc_html__( 'No', '__tm' ),
					'true_slave'   => 'map_relation',
					'false_slave'  => '',
				),
			),
			'marker_number' => array(
				'type'      => 'stepper',
				'value'     => 10,
				'max_value' => 9999,
				'min_value' => 1,
				'label'     => esc_html__( 'Visible markers on map', '__tm' ),
				'master'    => 'map_relation',
			),
		);

		parent::__construct();
	}

	/**
	 * Widget function.
	 *
	 * @see   WP_Widget
	 * @since 1.0.0
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		$this->setup_widget_data( $args, $instance );
		echo $args['before_widget'];

		$title          = $this->widget_start( $args, $instance );
		$map_visibility = ! empty( $instance['map_visibility'] ) ? $instance['map_visibility'] : $this->settings['map_visibility']['value'];
		$map_visibility = filter_var( $map_visibility, FILTER_VALIDATE_BOOLEAN );
		$map_visibility = $map_visibility && ! cherry_re_is_property_search();
		$number         = ! empty( $instance['marker_number'] ) ? $instance['marker_number'] : $this->settings['marker_number']['value'];

		$data     = Cherry_RE_Property_Data::get_instance();
		$defaults = $data->get_search_defaults();
		$values   = wp_parse_args( $defaults, $_GET );

		// Search form template.
		cherry_re_get_template( 'widgets/search/form', array(
			'title'  => $title,
			'values' => $values,
		) );

		if ( true === apply_filters( 'cherry_re_map_visibility', $map_visibility ) ) {

			// Map template.
			cherry_re_get_template( 'widgets/search/map', array(
				'args' => compact( 'number' ),
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
	 * @return void
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
}
