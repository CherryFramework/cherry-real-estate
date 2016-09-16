<?php
/**
 * Porperties listing widget for Real Estate.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Widgets
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

if ( ! class_exists( 'Cherry_RE_Properties_Widget' ) ) {

	/**
	 * Class for Properties widget.
	 *
	 * @since 1.0.0
	 */
	class Cherry_RE_Properties_Widget extends Cherry_Abstract_Widget {

		/**
		 * Constructor method.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->widget_cssclass    = 'widget-tm-re-property';
			$this->widget_description = esc_html__( 'Display a search form with map.', 'cherry-real-estate' );
			$this->widget_id          = 'tm_re_properties_widget';
			$this->widget_name        = esc_html__( 'Cherry RE Properites', 'cherry-real-estate' );

			$post_type     = cherry_real_estate()->get_post_type_name();
			$agents        = cherry_re_agent_data()->get_agents();
			$agents_select = is_array( $agents ) ? wp_list_pluck( $agents, 'display_name', 'ID' ) : array();
			$image_sizes   = get_intermediate_image_sizes();

			$this->settings = array(
				'title'  => array(
					'type'  => 'text',
					'value' => '',
					'label' => esc_html__( 'Title:', 'cherry-real-estate' ),
				),
				'number' => array(
					'type'      => 'stepper',
					'value'     => 5,
					'max_value' => 9999,
					'min_value' => 1,
					'label'     => esc_html__( 'Posts Number:', 'cherry-real-estate' ),
				),
				'source' => array(
					'type'    => 'radio',
					'value'   => 'all',
					'options' => array(
						'all' => array(
							'label' => esc_html__( 'All', 'cherry-real-esate' ),
						),
						$post_type . '_type' => array(
							'label' => esc_html__( 'Types', 'cherry-real-esate' ),
							'slave' => 'types_relation',
						),
						$post_type . '_tag' => array(
							'label' => esc_html__( 'Tags', 'cherry-real-esate' ),
							'slave' => 'tags_relation',
						),
						$post_type . '_feature' => array(
							'label' => esc_html__( 'Features', 'cherry-real-esate' ),
							'slave' => 'features_relation',
						),
						'ids' => array(
							'label' => esc_html__( 'IDs', 'cherry-real-esate' ),
							'slave' => 'ids_relation',
						),
					),
					'label' => esc_html__( 'Source:', 'cherry-real-esate' ),
				),
				$post_type . '_type' => array(
					'type'             => 'select',
					'value'            => '',
					'options_callback' => array( 'Model_Properties', 'get_property_types', array( 'id' ) ),
					'options'          => false,
					'label'            => esc_html__( 'Select types:', 'cherry-real-esate' ),
					'multiple'         => true,
					'placeholder'      => esc_html__( 'Select types:', 'cherry-real-esate' ),
					'master'           => 'types_relation',
				),
				$post_type . '_tag' => array(
					'type'             => 'select',
					'value'            => '',
					'options_callback' => array( 'Model_Properties', 'get_property_tags', array( 'id' ) ),
					'options'          => false,
					'label'            => esc_html__( 'Select tags:', 'cherry-real-esate' ),
					'multiple'         => true,
					'placeholder'      => esc_html__( 'Select tags:', 'cherry-real-esate' ),
					'master'           => 'tags_relation',
				),
				$post_type . '_feature' => array(
					'type'             => 'select',
					'value'            => '',
					'options_callback' => array( 'Model_Properties', 'get_property_features', array( 'id' ) ),
					'options'          => false,
					'label'            => esc_html__( 'Select features:', 'cherry-real-esate' ),
					'multiple'         => true,
					'placeholder'      => esc_html__( 'Select features:', 'cherry-real-esate' ),
					'master'           => 'features_relation',
				),
				'ids' => array(
					'type'        => 'text',
					'value'       => '',
					'label'       => esc_html__( 'Type posts ID (for a custom sorting):', 'cherry-real-esate' ),
					'placeholder' => esc_html__( 'Type posts ID:', 'cherry-real-esate' ),
					'master'      => 'ids_relation',
				),
				'agent' => array(
					'type'    => 'select',
					'options' => array( '' => esc_attr__( 'All', 'cherry-real-estate' ) ) + $agents_select,
					'label'   => esc_html__( 'Agent:', 'cherry-real-estate' ),
				),
				'sort' => array(
					'type'    => 'select',
					'options' => array(
						'date' => esc_attr__( 'Date', 'cherry-real-estate' ),
						'price' => esc_attr__( 'Price', 'cherry-real-estate' ),
					),
					'value' => 'date',
					'label' => esc_html__( 'Order by:', 'cherry-real-estate' ),
				),
				'order' => array(
					'type'    => 'select',
					'options' => array(
						'asc'  => esc_attr__( 'ASC', 'cherry-real-estate' ),
						'desc' => esc_attr__( 'DESC', 'cherry-real-estate' ),
					),
					'value' => 'desc',
					'label' => esc_html__( 'Order:', 'cherry-real-estate' ),
				),
				'show_title' => array(
					'type'  => 'switcher',
					'value' => 'true',
					'style' => ( wp_is_mobile() ) ? 'normal' : 'small',
					'label' => esc_html__( 'Show Title:', 'cherry-real-estate' ),
					'toggle' => array(
						'true_toggle'  => esc_html__( 'On', 'cherry-real-estate' ),
						'false_toggle' => esc_html__( 'Off', 'cherry-real-estate' ),
					),
				),
				'show_image' => array(
					'type'   => 'switcher',
					'value'  => 'true',
					'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
					'label'  => esc_html__( 'Show Image:', 'cherry-real-estate' ),
					'toggle' => array(
						'true_toggle'  => esc_html__( 'On', 'cherry-real-estate' ),
						'false_toggle' => esc_html__( 'Off', 'cherry-real-estate' ),
						'true_slave'   => 'image_attr',
						'false_slave'  => '',
					),
				),
				'image_size' => array(
					'type'    => 'select',
					'options' => array_combine( $image_sizes, $image_sizes ),
					'value'   => 'thumbnail',
					'label'   => esc_html__( 'Image Size:', 'cherry-real-estate' ),
					'master'  => 'image_attr',
				),
				'show_excerpt' => array(
					'type'   => 'switcher',
					'value'  => 'true',
					'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
					'label'  => esc_html__( 'Show Excerpt:', 'cherry-real-estate' ),
					'toggle' => array(
						'true_toggle'  => esc_html__( 'On', 'cherry-real-estate' ),
						'false_toggle' => esc_html__( 'Off', 'cherry-real-estate' ),
						'true_slave'   => 'excerpt_attr',
						'false_slave'  => '',
					),
				),
				'excerpt_length' => array(
					'type'      => 'stepper',
					'value'     => 15,
					'max_value' => 9999,
					'min_value' => 1,
					'label'     => esc_html__( 'Excerpt Length (in words):', 'cherry-real-estate' ),
					'master'    => 'excerpt_attr',
				),
				'show_more_button' => array(
					'type'   => 'switcher',
					'value'  => 'true',
					'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
					'label'  => esc_html__( 'Show More Button:', 'cherry-real-estate' ),
					'toggle' => array(
						'true_toggle'  => esc_html__( 'On', 'cherry-real-estate' ),
						'false_toggle' => esc_html__( 'Off', 'cherry-real-estate' ),
						'true_slave'   => 'more_btn_attr',
						'false_slave'  => '',
					),
				),
				'more_button_text' => array(
					'type'   => 'text',
					'value'  => esc_html__( 'read more', 'cherry-real-estate' ),
					'label'  => esc_html__( 'More Button Text:', 'cherry-real-estate' ),
					'master' => 'more_btn_attr',
				),
				'show_status' => array(
					'type'   => 'switcher',
					'value'  => 'false',
					'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
					'label'  => esc_html__( 'Show Status:', 'cherry-real-estate' ),
					'toggle' => array(
						'true_toggle'  => esc_html__( 'On', 'cherry-real-estate' ),
						'false_toggle' => esc_html__( 'Off', 'cherry-real-estate' ),
					),
				),
				'show_area' => array(
					'type'   => 'switcher',
					'value'  => 'false',
					'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
					'label'  => esc_html__( 'Show Area:', 'cherry-real-estate' ),
					'toggle' => array(
						'true_toggle'  => esc_html__( 'On', 'cherry-real-estate' ),
						'false_toggle' => esc_html__( 'Off', 'cherry-real-estate' ),
					),
				),
				'show_bedrooms' => array(
					'type'   => 'switcher',
					'value'  => 'false',
					'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
					'label'  => esc_html__( 'Show Bedrooms:', 'cherry-real-estate' ),
					'toggle' => array(
						'true_toggle'  => esc_html__( 'On', 'cherry-real-estate' ),
						'false_toggle' => esc_html__( 'Off', 'cherry-real-estate' ),
					),
				),
				'show_bathrooms' => array(
					'type'   => 'switcher',
					'value'  => 'false',
					'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
					'label'  => esc_html__( 'Show Bathrooms:', 'cherry-real-estate' ),
					'toggle' => array(
						'true_toggle'  => esc_html__( 'On', 'cherry-real-estate' ),
						'false_toggle' => esc_html__( 'Off', 'cherry-real-estate' ),
					),
				),
				'show_price' => array(
					'type'   => 'switcher',
					'value'  => 'false',
					'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
					'label'  => esc_html__( 'Show Price:', 'cherry-real-estate' ),
					'toggle' => array(
						'true_toggle'  => esc_html__( 'On', 'cherry-real-estate' ),
						'false_toggle' => esc_html__( 'Off', 'cherry-real-estate' ),
					),
				),
				'show_location' => array(
					'type'   => 'switcher',
					'value'  => 'false',
					'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
					'label'  => esc_html__( 'Show Location:', 'cherry-real-estate' ),
					'toggle' => array(
						'true_toggle'  => esc_html__( 'On', 'cherry-real-estate' ),
						'false_toggle' => esc_html__( 'Off', 'cherry-real-estate' ),
					),
				),
				'template' => array(
					'type'    => 'select',
					'options' => $this->get_templates_list(),
					'value'   => 'default.tmpl',
					'label'   => esc_html__( 'Template:', 'cherry-real-estate' ),
				),
			);

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

			ob_start();

			$this->setup_widget_data( $args, $instance );
			$this->widget_start( $args, $instance );

			// Prepare agrs.
			$number           = ! empty( $instance['number'] ) ? absint( $instance['number'] ) : $this->settings['number']['value'];
			$source           = ! empty( $instance['source'] ) ? sanitize_text_field( $instance['source'] ) : '';
			$terms            = ! empty( $instance[ $source ] ) ? $instance[ $source ] : '';
			$terms            = is_array( $terms ) ? array_map( 'intval', $terms ) : sanitize_text_field( $terms );
			$ids              = ! empty( $instance['ids'] ) && ( 'ids' === $source ) ? sanitize_text_field( $instance['ids'] ) : '';
			$agent            = ! empty( $instance['agent'] ) ? sanitize_text_field( $instance['agent'] ) : '';
			$orderby          = ! empty( $instance['sort'] ) ? sanitize_text_field( $instance['sort'] ) : $this->settings['sort']['value'];
			$order            = ! empty( $instance['order'] ) ? sanitize_text_field( $instance['order'] ) : $this->settings['order']['value'];
			$image_size       = ! empty( $instance['image_size'] ) ? sanitize_text_field( $instance['image_size'] ) : $this->settings['image_size']['value'];
			$excerpt_length   = ! empty( $instance['excerpt_length'] ) ? sanitize_text_field( $instance['excerpt_length'] ) : $this->settings['excerpt_length']['value'];
			$more_button_text = ! empty( $instance['more_button_text'] ) ? sanitize_text_field( $instance['more_button_text'] ) : $this->settings['more_button_text']['value'];
			$template         = ! empty( $instance['template'] ) ? sanitize_text_field( $instance['template'] ) : $this->settings['template']['value'];

			// Visibility options.
			$title_visibility     = ! empty( $instance['show_title'] ) ? sanitize_text_field( $instance['show_title'] ) : $this->settings['show_title']['value'];
			$image_visibility     = ! empty( $instance['show_image'] ) ? sanitize_text_field( $instance['show_image'] ) : $this->settings['show_image']['value'];
			$excerpt_visibility   = ! empty( $instance['show_excerpt'] ) ? sanitize_text_field( $instance['show_excerpt'] ) : $this->settings['show_excerpt']['value'];
			$btn_visibility       = ! empty( $instance['show_more_button'] ) ? sanitize_text_field( $instance['show_more_button'] ) : $this->settings['show_more_button']['value'];

			$status_visibility    = ! empty( $instance['show_status'] ) ? sanitize_text_field( $instance['show_status'] ) : $this->settings['show_status']['value'];
			$area_visibility      = ! empty( $instance['show_area'] ) ? sanitize_text_field( $instance['show_area'] ) : $this->settings['show_area']['value'];
			$bedrooms_visibility  = ! empty( $instance['show_bedrooms'] ) ? sanitize_text_field( $instance['show_bedrooms'] ) : $this->settings['show_bedrooms']['value'];
			$bathrooms_visibility = ! empty( $instance['show_bathrooms'] ) ? sanitize_text_field( $instance['show_bathrooms'] ) : $this->settings['show_bathrooms']['value'];
			$price_visibility     = ! empty( $instance['show_price'] ) ? sanitize_text_field( $instance['show_price'] ) : $this->settings['show_price']['value'];
			$location_visibility  = ! empty( $instance['show_location'] ) ? sanitize_text_field( $instance['show_location'] ) : $this->settings['show_location']['value'];

			// Sanitize boolean value.
			$title_visibility     = filter_var( $title_visibility, FILTER_VALIDATE_BOOLEAN );
			$image_visibility     = filter_var( $image_visibility, FILTER_VALIDATE_BOOLEAN );
			$excerpt_visibility   = filter_var( $excerpt_visibility, FILTER_VALIDATE_BOOLEAN );
			$btn_visibility       = filter_var( $btn_visibility, FILTER_VALIDATE_BOOLEAN );

			$status_visibility    = filter_var( $status_visibility, FILTER_VALIDATE_BOOLEAN );
			$area_visibility      = filter_var( $area_visibility, FILTER_VALIDATE_BOOLEAN );
			$bedrooms_visibility  = filter_var( $bedrooms_visibility, FILTER_VALIDATE_BOOLEAN );
			$bathrooms_visibility = filter_var( $bathrooms_visibility, FILTER_VALIDATE_BOOLEAN );
			$price_visibility     = filter_var( $price_visibility, FILTER_VALIDATE_BOOLEAN );
			$location_visibility  = filter_var( $location_visibility, FILTER_VALIDATE_BOOLEAN );

			$data       = Cherry_RE_Property_Data::get_instance();
			$query_args = array(
				'number'           => $number,
				'author'           => $agent,
				'order'            => $order,
				'show_title'       => $title_visibility,
				'show_image'       => $image_visibility,
				'image_size'       => $image_size,
				'show_excerpt'     => $excerpt_visibility,
				'excerpt_length'   => $excerpt_length,
				'show_more_button' => $btn_visibility,
				'more_button_text' => $more_button_text,
				'show_status'      => $status_visibility,
				'show_area'        => $area_visibility,
				'show_bedrooms'    => $bedrooms_visibility,
				'show_bathrooms'   => $bathrooms_visibility,
				'show_price'       => $price_visibility,
				'show_location'    => $location_visibility,
				'template'         => $template,
				'css_class'        => 'tm_property--widget list',
			);

			if ( 'ids' === $source ) {
				$query_args['ids'] = $ids;
			} else {
				$query_args['taxonomy'] = $source;
				$query_args['terms']    = $terms;

				if ( 'price' === $orderby  ) {
					$prefix                 = cherry_real_estate()->get_meta_prefix();
					$query_args['orderby']  = 'meta_value_num';
					$query_args['meta_key'] = $prefix . 'price';
				} else {
					$query_args['orderby'] = 'date';
				}
			}

			$query_args = apply_filters( 'cherry_re_properties_widget_args', $query_args, $args, $instance );

			$data->the_property( $query_args );
			$this->widget_end( $args );
			$this->reset_widget_data();

			echo $this->cache_widget( $args, ob_get_clean() );
		}

		/**
		 * Returns available templates list.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_templates_list() {
			return apply_filters( 'cherry_re_properties_widget_templates_list', array(
				'default.tmpl' => 'default.tmpl',
			) );
		}
	}

	add_action( 'widgets_init', 'cherry_re_register_properties_widget' );

	/**
	 * Register a `Cherry RE Properites` widget.
	 *
	 * @since 1.0.0
	 */
	function cherry_re_register_properties_widget() {
		register_widget( 'Cherry_RE_Properties_Widget' );
	}
}
