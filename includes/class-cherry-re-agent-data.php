<?php
/**
 * Agent public data class.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Class for RE agent data.
 *
 * @since 1.0.0
 */
class Cherry_RE_Agent_Data {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Holder for the agent object.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private $agent = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {}

	/**
	 * Display or return HTML-formatted agents.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments.
	 * @return string
	 */
	public function the_agents( $args = array() ) {
		/**
		 * Filter the array of default arguments.
		 *
		 * @since 1.0.0
		 * @param array $defaults Default arguments.
		 * @param array $args     The 'the_agents' function argument.
		 */
		$defaults = apply_filters( 'cherry_re_agent_list_defaults', array(
			// 'pager'        => false,
			// 'paged'        => 1,
			'number'          => 10,
			'orderby'         => 'display_name',
			'order'           => 'desc',
			'id'              => 0,
			'echo'            => true,
			'show_pagination' => true,
			'template'        => 'default.tmpl',
			'wrap_class'      => 'tm-agents-list',
			'item_class'      => 'tm-agent-list__item',
			'color_scheme'    => '',
			'css_class'       => '',
		), $args );

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the array of arguments.
		 *
		 * @since 1.0.0
		 * @param array Arguments.
		 */
		$args = apply_filters( 'cherry_re_the_agents_args', $args );

		/**
		 * Fires before the agent listing.
		 *
		 * @since 1.0.0
		 * @param array $array The array of arguments.
		 */
		do_action( 'cherry_re_before_agents', $args );

		// Strange query.
		if ( 0 === $args['number'] ) {
			return;
		}

		// The Query.
		$agents = $this->get_agents( $args );

		if ( false === $agents ) {
			return;
		}

		$output = '';

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
		$css_classes = apply_filters( 'cherry_re_agents_wrapper_classes', $css_classes, $args );

		$inner          = $this->get_agents_loop( $agents, $args );
		$wrapper_format = apply_filters( 'cherry_re_agents_wrapper_format', '<div class="%s">%s</div>', $args );
		$output         = sprintf( $wrapper_format, join( ' ', array_unique( $css_classes ) ), $inner );

		// Pagination (if we need).
		if ( true == $args['show_pagination'] ) {

			$all_agents = get_users( array(
				'role' => 're_agent',
			) );

			$total_agents = count( $all_agents );
			$total_query  = count( $agents );
			$total_pages  = ceil( $total_agents / $args['number'] );

			$output .= $this->get_pagination( array(
				'total_agents' => $total_agents,
				'total_query'  => $total_query,
				'total_pages'  => $total_pages,
			) );
		}

		/**
		 * Filters HTML-formatted agents before display or return.
		 *
		 * @since 1.0.0
		 * @param string $output The HTML-formatted agents.
		 * @param array  $query  List of WP_Post objects.
		 * @param array  $args   The array of arguments.
		 */
		$output = apply_filters( 'cherry_re_agents_html', $output, $agents, $args );

		if ( true != $args['echo'] ) {
			return $output;
		}

		// If "echo" is set to true.
		echo $output;

		/**
		 * Fires after the agents listing.
		 *
		 * This hook fires only when "echo" is set to true.
		 *
		 * @since 1.0.0
		 * @param array $array The array of arguments.
		 */
		do_action( 'cherry_re_agents_after', $args );
	}

	/**
	 * Get agenst.
	 *
	 * @since  1.0.0
	 * @param  array|string $args Arguments to be passed to the query.
	 * @return array|bool         Array if true, boolean if false.
	 */
	public function get_agents( $args = array() ) {
		$defaults = array(
			'number'  => 10,
			'role'    => 're_agent',
			'orderby' => 'display_name',
			'order'   => 'asc',
			'id'      => '',
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the array of arguments.
		 *
		 * @since 1.0.0
		 * @param array Arguments to be passed to the query.
		 */
		$args = apply_filters( 'cherry_get_agents_args', $args );

		$args['show_pagination'] = isset( $args['show_pagination'] ) ? filter_var( $args['show_pagination'], FILTER_VALIDATE_BOOLEAN ) : false;

		if ( true === $args['show_pagination'] ) {

			if ( get_query_var( 'paged' ) ) {
				$args['paged'] = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
				$args['paged'] = get_query_var( 'page' );
			} else {
				$args['paged'] = 1;
			}
		}

		$ids = explode( ',', $args['id'] );

		if ( 0 < intval( $args['id'] ) && 0 < count( $ids ) ) {
			$ids             = array_map( 'intval', $ids );
			$args['include'] = $ids;
		}

		$orderby_whitelist = array(
			'id',
			'login',
			'nicename',
			'email',
			'url',
			'registered',
			'display_name',
			'post_count',
			'include',
			'meta_value',
		);

		// Whitelist checks.
		if ( ! in_array( $args['orderby'], $orderby_whitelist ) ) {
			$args['orderby'] = 'display_name';
		}

		if ( ! in_array( strtolower( $args['order'] ), array( 'asc', 'desc' ) ) ) {
			$args['order'] = 'asc';
		}

		/**
		 * Filters the query.
		 *
		 * @since 1.0.0
		 * @param array The array of query arguments.
		 */
		$args = apply_filters( 'cherry_re_get_agents_query_args', $args );

		$agents = get_users( $args );

		if ( empty( $agents ) ) {
			return false;
		}

		return $agents;
	}

	/**
	 * Get agents pagination.
	 *
	 * @see `get_the_posts_pagination` function.
	 * @since  1.0.0
	 * @param  array $args Pagination arguments.
	 * @return string
	 */
	public function get_pagination( $args ) {
		$args = apply_filters( 'cherry_re_get_agent_pagination_args', $args );

		if ( $args['total_agents'] <= $args['total_query'] ) {
			return;
		}

		$pagination = '';

		$args = wp_parse_args( $args, array(
			'total'              => $args['total_pages'],
			'mid_size'           => 1,
			'prev_text'          => esc_html_x( 'Previous', 'previous post', 'cherry-real-estate' ),
			'next_text'          => esc_html_x( 'Next', 'next post', 'cherry-real-estate' ),
			'screen_reader_text' => esc_html__( 'Posts navigation', 'cherry-real-estate' ),
		) );

		// Make sure we get a string back. Plain is the next best thing.
		if ( isset( $args['type'] ) && 'array' == $args['type'] ) {
			$args['type'] = 'plain';
		}

		$links = paginate_links( $args );

		if ( $links ) {
			$pagination = _navigation_markup( $links, 'pagination', $args['screen_reader_text'] );
		}

		return $pagination;
	}

	/**
	 * Get agents items.
	 *
	 * @since  1.0.0
	 * @param  array $agents WP_query object.
	 * @param  array $args   The array of arguments.
	 * @return string
	 */
	public function get_agents_loop( $agents, $args ) {

		// Item template.
		$template = cherry_re_templater()->get_template_by_name(
			$args['template'],
			'agent_list'
		);

		/**
		 * Filters template for agent item.
		 *
		 * @since 1.0.0
		 * @param string $template
		 * @param array  $args
		 */
		$template = apply_filters( 'cherry_re_agent_item_template', $template, $args );

		$count  = 1;
		$output = '';

		$callbacks = cherry_re_templater()->setup_template_data( $args );

		foreach ( $agents as $agent ) {
			$this->setup_agentdata( $agent );
			$callbacks->the_agent_meta( $agent );

			$tpl = $template;
			$tpl = cherry_re_templater()->parse_template( $tpl );

			$item_classes   = array( 'tm-agent', $args['item_class'], 'item-' . $count, 'clearfix' );
			$item_classes[] = ( $count % 2 ) ? 'odd' : 'even';
			$item_classes   = array_filter( $item_classes );
			$item_classes   = array_map( 'esc_attr', $item_classes );

			$output .= '<div class="' . join( ' ', $item_classes ) . '">';

				/**
				 * Filters agent items.
				 *
				 * @since 1.0.0
				 * @param string $tpl
				 */
				$tpl = apply_filters( 'cherry_get_agents_loop', $tpl );

				$output .= $tpl;

			$output .= '</div><!--/.tm-agent-->';

			$callbacks->clear_data();

			$count++;
		}

		$this->reset_agentdata();

		return $output;
	}

	/**
	 * Setup agent data.
	 *
	 * @since 1.0.0
	 * @param object $agent Current agent instance.
	 */
	public function setup_agentdata( $agent ) {
		$this->agent = $agent;
	}

	/**
	 * Retrieve current agent data.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public function get_current_agent() {
		return $this->agent;
	}

	/**
	 * Clear agent data.
	 *
	 * @since 1.0.0
	 */
	public function reset_agentdata() {
		$this->agent = null;
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

/**
 * Returns instance of Agent data class.
 *
 * @since  1.0.0
 * @return Cherry_Real_Estate
 */
function cherry_re_agent_data() {
	return Cherry_RE_Agent_Data::get_instance();
}
