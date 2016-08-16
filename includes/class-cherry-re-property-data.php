<?php
/**
 * Property public data class.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Class for RE property data.
 *
 * @since 1.0.0
 */
class Cherry_RE_Property_Data {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	private static $instance = null;

	/**
	 * The array of arguments for query.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $query_args = array();

	/**
	 * Holder for the main query object.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	private $wp_query = null;

	/**
	 * Defaults param for search.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $search_defaults = array(
		's'                 => '',
		'property_status'   => '',
		'property_type'     => '',
		'property_location' => '',
		'min_price'         => '',
		'max_price'         => '',
		'min_bedrooms'      => '',
		'max_bedrooms'      => '',
		'min_bathrooms'     => '',
		'max_bathrooms'     => '',
		'min_area'          => '',
		'max_area'          => '',
		'min_parking_place' => '',
		'max_parking_place' => '',
		'orderby'           => 'date',
		'order'             => 'desc',
	);

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {}

	/**
	 * Display or return HTML-formatted properties.
	 *
	 * @since  1.0.0
	 * @param  array  $args Arguments.
	 * @return string
	 */
	public function the_property( $args = array() ) {

		/**
		 * Filter the array of default arguments.
		 *
		 * @since 1.0.0
		 * @param array $defaults Default arguments.
		 * @param array $args     The 'the_property' function argument.
		 */
		$defaults = apply_filters( 'cherry_re_property_default_args', array(
			'number'          => 5,
			'orderby'         => 'date',
			'order'           => 'desc',
			'ids'             => 0,
			'echo'            => true,
			'show_pagination' => false,
			'template'        => 'default.tmpl',
			'wrap_class'      => 'tm-property__wrap',
			'item_class'      => 'tm-property__item',
			'color_scheme'    => '',
			'css_id'          => '',
			'css_class'       => '',
		), $args );

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the array of arguments.
		 *
		 * @since 1.0.0
		 * @param array Arguments.
		 */
		$args = apply_filters( 'cherry_re_the_property_args', $args );

		/**
		 * Filter before the Properties.
		 *
		 * @since 1.0.0
		 * @param array $array The array of arguments.
		 */
		$inner = apply_filters( 'cherry_re_property_before', '', $args );

		// Strange query.
		if ( 0 === $args['number'] ) {
			return;
		}

		// The Query.
		$query = $this->get_properties( $args );

		if ( false === $query ) {
			wp_reset_postdata();
			return;
		}

		global $wp_query;

		$temp_query     = $wp_query;
		$wp_query       = null;
		$wp_query       = $query;
		$this->wp_query = null;
		$this->wp_query = $query;

		// Prepare CSS-id.
		$css_id = ! empty( $args['css_id'] ) ? esc_attr( $args['css_id'] ) : '';
		$css_id = ! empty( $css_id ) ? sprintf( ' id="%s"', $css_id ) : '';

		// Prepare CSS-class.
		$css_classes = array();

		if ( ! empty( $args['wrap_class'] ) ) {
			$css_classes[] = $args['wrap_class'];
		}

		if ( ! empty( $args['template'] ) ) {
			$css_classes[] = cherry_re_templater()->get_template_class( $args['template'] );
		}

		if ( ! in_array( $args['color_scheme'], array( 'regular', 'invert' ) ) ) {
			$args['color_scheme'] = 'regular';
		}

		$css_classes[] = $args['color_scheme'];

		if ( ! empty( $args['css_class'] ) ) {
			$css_classes[] = $args['css_class'];
		}

		$css_classes = array_map( 'esc_attr', $css_classes );
		$css_classes = apply_filters( 'cherry_re_properties_wrapper_classes', $css_classes, $args );

		$inner    .= $this->get_properties_loop( $query, $args );
		$post_ids = wp_list_pluck( $query->posts, 'ID' );

		$inner          = apply_filters( 'cherry_re_properties_loop_after', $inner, $args );
		$wrapper_format = apply_filters( 'cherry_re_properties_wrapper_format', '<div%s class="%s" data-property-ids="%s">%s</div>', $args );

		$output = sprintf(
			$wrapper_format,
			$css_id,
			join( ' ', array_unique( $css_classes ) ),
			wp_json_encode( $post_ids ),
			$inner
		);

		// Pagination (if we need).
		if ( true == $args['show_pagination'] ) {
			$output .= get_the_posts_pagination( apply_filters( 'cherry_re_properties_pagination_args', array(), $args ) );
		}

		$wp_query = null;
		$wp_query = $temp_query;

		wp_reset_postdata();

		/**
		 * Filters HTML-formatted properties before display or return.
		 *
		 * @since 1.0.0
		 * @param string $output The HTML-formatted properties.
		 * @param array  $query  List of WP_Post objects.
		 * @param array  $args   The array of arguments.
		 */
		$output = apply_filters( 'cherry_re_properties_html', $output, $query, $args );

		if ( true != $args['echo'] ) {
			return $output;
		}

		// If "echo" is set to true.
		echo $output;
	}

	/**
	 * Get properties.
	 *
	 * @since  1.0.0
	 * @param  array      $args Arguments to be passed to the query.
	 * @return array|bool       Array if true, boolean if false.
	 */
	public function get_properties( $args = array() ) {
		$defaults = array(
			'number'   => 5,
			'orderby'  => 'date',
			'order'    => 'desc',
			'ids'      => 0,
			'state'    => 'active',
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the array of arguments.
		 *
		 * @since 1.0.0
		 * @param array Arguments to be passed to the query.
		 */
		$args = apply_filters( 'cherry_re_get_properties_args', $args );

		// The Query Arguments.
		$post_type = cherry_real_estate()->get_post_type_name();
		$this->query_args['post_type']        = $post_type;
		$this->query_args['posts_per_page']   = $args['number'];
		$this->query_args['orderby']          = $args['orderby'];
		$this->query_args['order']            = $args['order'];
		$this->query_args['suppress_filters'] = false;

		if ( ! empty( $args['author'] ) ) {
			$this->query_args['author'] = $args['author'];
		}

		// Tax Query.
		if ( ! empty( $args['tax_query'] ) ) {
			$this->query_args['tax_query'] = $args['tax_query'];

		} elseif ( ! empty( $args['taxonomy'] ) && ! empty( $args['terms'] ) ) {

			// Term string to array.
			$terms = explode( ',', $args['terms'] );

			// Taxonomy operator.
			$tax_operator = ! empty( $args['tax_operator'] ) ? $args['tax_operator'] : 'IN';

			// Validate operator.
			if ( ! in_array( $tax_operator, array( 'IN', 'NOT IN', 'AND' ) ) ) {
				$tax_operator = 'IN';
			}

			$tax_args = array(
				'tax_query' => array(
					array(
						'taxonomy' => $args['taxonomy'],
						'field'    => ( is_numeric( $terms[0] ) ) ? 'id' : 'slug',
						'terms'    => $terms,
						'operator' => $tax_operator,
					),
				),
			);

			$this->query_args = array_merge( $this->query_args, $tax_args );
		}

		// State param.
		if ( ! array_key_exists( $args['state'], Model_Properties::get_allowed_property_states() ) ) {
			$args['state'] = 'active';
		}

		$state_query = array(
			array(
				'key'     => cherry_real_estate()->get_meta_prefix() . 'state',
				'value'   => $args['state'],
				'compare' => '=',
			),
		);

		// Meta Query.
		$this->query_args['meta_query'] = $state_query;

		if ( ! empty( $args['meta_query'] ) ) {
			$this->query_args['meta_query'] = array_merge( $this->query_args['meta_query'], $args['meta_query'] );
		}

		// Pagination.
		if ( isset( $args['show_pagination'] ) && ( true == $args['show_pagination'] ) ) :

			if ( get_query_var( 'paged' ) ) {
				$this->query_args['paged'] = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
				$this->query_args['paged'] = get_query_var( 'page' );
			} else {
				$this->query_args['paged'] = 1;
			}

		endif;

		$ids = str_replace( ' ', ',', $args['ids'] );
		$ids = explode( ',', $ids );

		if ( 0 < intval( $args['ids'] ) && 0 < count( $ids ) ) :

			$ids = array_map( 'intval', $ids );

			if ( 1 == count( $ids ) && is_numeric( $ids[0] ) && ( 0 < intval( $ids[0] ) ) ) {
				$this->query_args['p'] = intval( $args['ids'] );
			} else {
				$this->query_args['post__in'] = $ids;
			}

		endif;

		// Whitelist checks.
		if ( ! in_array( $this->query_args['orderby'], array( 'none', 'ID', 'author', 'title', 'date', 'modified', 'parent', 'rand', 'menu_order', 'meta_value', 'meta_value_num' ) ) ) {
			$this->query_args['orderby'] = 'date';
		}

		if ( ! in_array( strtolower( $this->query_args['order'] ), array( 'asc', 'desc' ) ) ) {
			$this->query_args['order'] = 'desc';
		}

		if ( ! empty( $args['s'] ) ) {
			$this->query_args['s'] = esc_attr( $args['s'] );
		}

		/**
		 * Filters the query.
		 *
		 * @since 1.0.0
		 * @param array The array of query arguments.
		 * @param array The array of arguments to be passed to the query.
		 */
		$this->query_args = apply_filters( 'cherry_re_get_properties_query_args', $this->query_args, $args );

		// echo "<pre>";
		// var_dump($this->query_args);
		// echo "</pre>";

		// The Query.
		$query = new WP_Query( $this->query_args );

		if ( ! $query->have_posts() ) {
			return false;
		}

		return $query;
	}

	/**
	 * Get properties item.
	 *
	 * @since  1.0.0
	 * @param  array $query WP_query object.
	 * @param  array $args  The array of arguments.
	 * @return string
	 */
	public function get_properties_loop( $query, $args ) {
		global $post;

		// Item template.
		$template = cherry_re_templater()->get_template_by_name(
			$args['template'],
			'property_list'
		);

		/**
		 * Filters template for property item.
		 *
		 * @since 1.0.0
		 * @param string $template.
		 * @param array  $args.
		 */
		$template = apply_filters( 'cherry_re_property_item_template', $template, $args );

		$count  = 1;
		$output = '';

		$callbacks = cherry_re_templater()->setup_template_data( $args );

		while ( $query->have_posts() ) :

			$query->the_post();
			$callbacks->the_property_data();

			$tpl = $template;
			$tpl = cherry_re_templater()->parse_template( $tpl );

			$property_ID    = $post->ID;
			$item_classes   = array( $args['item_class'], 'item-' . $count, 'clearfix' );
			$item_classes[] = ( $count % 2 ) ? 'odd' : 'even';
			$item_classes   = array_filter( $item_classes );
			$item_classes   = array_map( 'esc_attr', $item_classes );

			$meta_prefix = cherry_real_estate()->get_meta_prefix();
			$data_atts   = apply_filters( 'cherry_re_property_item_data_atts', array(
				'property-id'      => esc_attr( $property_ID ),
				'property-address' => esc_attr( get_post_meta( $property_ID, $meta_prefix . 'location', true ) ),
			), $property_ID );

			$output .= '<div class="' . join( ' ', $item_classes ) . '" ' . cherry_re_return_data_atts( $data_atts ) . '><div class="tm-property__inner">';

				/**
				 * Filters property item.
				 *
				 * @since 1.0.0
				 * @param string $tpl.
				 */
				$tpl = apply_filters( 'cherry_re_get_property_loop_item', $tpl );

				$output .= $tpl;

			$output .= '</div></div>';

			$callbacks->clear_data();

			$count++;

		endwhile;

		return $output;
	}

	/**
	 * Retrieve a current object of WP_Query.
	 *
	 * @since 1.0.0
	 * @return object|null
	 */
	public function get_wp_query() {
		return $this->wp_query;
	}

	/**
	 * Retrieve a defaults search params.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_search_defaults() {
		return apply_filters( 'cherry_re_get_search_defaults', $this->search_defaults );
	}

	/**
	 * Prepare search arguments.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function prepare_search_args() {
		$post_type = cherry_real_estate()->get_post_type_name();
		$prefix    = cherry_real_estate()->get_meta_prefix();
		$atts      = array_merge( $this->get_search_defaults(), $_GET );

		$args = apply_filters( 'cherry_re_search_defaults', array(
			'number'          => get_query_var( 'posts_per_page', 10 ),
			'show_pagination' => true,
			'template'        => 'archive.tmpl',
		) );

		if ( ! is_array( $atts ) ) {
			return $args;
		}

		if ( ! empty( $atts['s'] ) ) {
			$args['s'] = $atts['s'];
		}

		if ( ! empty( $atts['property_status'] ) ) {
			$args['meta_query'][] = array(
				'key'     =>  $prefix . 'status',
				'value'   => (string) $atts['property_status'],
				'compare' => '=',
			);
		}

		if ( ! empty( $atts['property_type'] ) ) {
			$args['taxonomy'] = $post_type . '_type';
			$args['terms']    = (string) $atts['property_type'];
		}

		if ( ! empty( $atts['property_location'] ) ) {
			$args['meta_query'][] = array(
				'key'     =>  $prefix . 'location',
				'value'   => (string) $atts['property_location'],
				'compare' => 'LIKE',
			);
		}

		if ( ! empty( $atts['min_price'] ) ) {
			$args['meta_query'][] = array(
				'key'     => $prefix . 'price',
				'value'   => (int) $atts['min_price'],
				'type'    => 'numeric',
				'compare' => '>=',
			);
		}

		if ( ! empty( $atts['max_price'] ) ) {
			$args['meta_query'][] = array(
				'key'     => $prefix . 'price',
				'value'   => (int) $atts['max_price'],
				'type'    => 'numeric',
				'compare' => '<=',
			);
		}

		if ( ! empty( $atts['min_bedrooms'] ) ) {
			$args['meta_query'][] = array(
				'key'     => $prefix . 'bedrooms',
				'value'   => (int) $atts['min_bedrooms'],
				'type'    => 'numeric',
				'compare' => '>=',
			);
		}

		if ( ! empty( $atts['max_bedrooms'] ) ) {
			$args['meta_query'][] = array(
				'key'     => $prefix . 'bedrooms',
				'value'   => (int) $atts['max_bedrooms'],
				'type'    => 'numeric',
				'compare' => '<=',
			);
		}

		if ( ! empty( $atts['min_bathrooms'] ) ) {
			$args['meta_query'][] = array(
				'key'     => $prefix . 'bathrooms',
				'value'   => (int) $atts['min_bathrooms'],
				'type'    => 'numeric',
				'compare' => '>=',
			);
		}

		if ( ! empty( $atts['max_bathrooms'] ) ) {
			$args['meta_query'][] = array(
				'key'     => $prefix . 'bathrooms',
				'value'   => (int) $atts['max_bathrooms'],
				'type'    => 'numeric',
				'compare' => ' <=',
			);
		}

		if ( ! empty( $atts['min_area'] ) ) {
			$args['meta_query'][] = array(
				'key'     => $prefix . 'area',
				'value'   => (int) $atts['min_area'],
				'type'    => 'numeric',
				'compare' => '>=',
			);
		}

		if ( ! empty( $atts['max_area'] ) ) {
			$args['meta_query'][] = array(
				'key'     => $prefix . 'area',
				'value'   => (int) $atts['max_area'],
				'type'    => 'numeric',
				'compare' => '<=',
			);
		}

		if ( ! empty( $atts['min_parking_place'] ) ) {
			$args['meta_query'][] = array(
				'key'     => $prefix . 'parking_place',
				'value'   => (int) $atts['min_parking_place'],
				'type'    => 'numeric',
				'compare' => '>=',
			);
		}

		if ( ! empty( $atts['max_parking_place'] ) ) {
			$args['meta_query'][] = array(
				'key'     => $prefix . 'parking_place',
				'value'   => (int) $atts['max_parking_place'],
				'type'    => 'numeric',
				'compare' => '<=',
			);
		}

		if ( 'price' == $atts['orderby'] ) {
			$args['orderby']  = 'meta_value_num';
			$args['meta_key'] = $prefix . 'price';
		} else {
			$args['orderby'] = 'date';
		}

		if ( ! empty( $atts['order'] ) ) {
			$args['order'] = $atts['order'];
		} else {
			$args['order'] = 'desc';
		}

		return apply_filters( 'cherry_re_prepare_search_args', $args, $atts );
	}

	public function get_property_data_from_query( $query, $key ) {

		if ( ! is_a( $query, 'WP_Query' ) ) {
			return;
		}

		if ( ! $query->have_posts() ) {
			return;
		}

		$data         = array();
		$callbacks    = cherry_re_templater()->setup_template_data();
		$replace_data = cherry_re_templater()->get_replace_data();

		if ( empty( $replace_data[ $key ]  ) ) {
			return;
		}

		if ( ! is_callable( $replace_data[ $key ] ) ) {
			return;
		}

		// Start inner loop.
		while ( $query->have_posts() ) {
			$query->the_post();
			$callbacks->the_property_data();

			$address = call_user_func( $replace_data[ $key ] );

			if ( empty( $address ) ) {
				continue;
			}

			$data[] = $address;

			$callbacks->clear_data();
		}

		wp_reset_postdata();

		return $data;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}
