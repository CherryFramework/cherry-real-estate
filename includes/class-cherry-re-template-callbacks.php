<?php
/**
 * Define callback functions for templater.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Callbacks for RE shortcodes templater.
 *
 * @since 1.0.0
 */
class Cherry_RE_Template_Callbacks {

	/**
	 * Shortcode attributes array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $atts = array();

	/**
	 * Specific property data.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public $property_data = array();

	/**
	 * Specific agent data.
	 *
	 * @since 1.0.0
	 * @var null
	 */
	public $agent_data = null;

	/**
	 * Constructor for the class.
	 *
	 * @since 1.0.0
	 * @param array $atts Input attributes array.
	 */
	public function __construct( $atts = array() ) {

		if ( ! empty( $atts ) ) {
			$this->atts = $atts;
		}

		// VIP actions - set and clear data on single property page.
		add_action( 'cherry_re_before_single_property_summary', array( $this, 'the_property_data' ), 0 );
		add_action( 'cherry_re_after_single_property_summary', array( $this, 'clear_data' ), 999999 );
	}

	/**
	 * Get agent photo.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_agent_photo( $args = array() ) {

		if ( isset( $this->atts['show_photo'] ) && false === $this->atts['show_photo'] ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'  => 'div',
			'class' => '',
			'size'  => ! empty( $this->atts['photo_size'] ) ? esc_attr( $this->atts['photo_size'] ) : 'thumbnail',
			'link'  => true,
		) );

		$agent_id = $this->get_agent_id();
		$photo    = Model_Agents::get_agent_photo_url( $agent_id, $args['size'] );

		if ( ! $photo ) {
			return;
		}

		$args['link'] = filter_var( $args['link'], FILTER_VALIDATE_BOOLEAN );

		if ( true === $args['link'] ) {
			$format = '<a href="%2$s"><img src="%1$s" alt=""></a>';
			$link   = $this->get_agent_permalink();
		} else {
			$format = '<img src="%1$s" alt="">';
			$link   = false;
		}

		return $this->macros_wrap( $args, sprintf( $format, esc_url( $photo ), esc_url( $link ) ) );
	}

	/**
	 * Get agent name.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_agent_name( $args = array() ) {

		if ( isset( $this->atts['show_name'] ) && false === $this->atts['show_name'] ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'  => 'div',
			'class' => '',
			'link'  => false,
		) );

		if ( empty( $this->agent_data->first_name ) && empty( $this->agent_data->last_name ) ) {
			$result = $this->agent_data->display_name;
		} else {
			$result = sprintf( '%s %s', $this->agent_data->first_name, $this->agent_data->last_name );
		}

		$args['link'] = filter_var( $args['link'], FILTER_VALIDATE_BOOLEAN );

		if ( true === $args['link'] ) {
			$result = '<a href="' . esc_url( $this->get_agent_permalink() ) . '">' . $result . '</a>';
		}

		return $this->macros_wrap( $args, $result );
	}

	/**
	 * Get agent decription.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_agent_description( $args = array() ) {

		if ( isset( $this->atts['show_desc'] ) && false === $this->atts['show_desc'] ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'   => 'div',
			'class'  => '',
			'length' => isset( $this->atts['desc_length'] ) ? intval( $this->atts['desc_length'] ) : -1,
		) );

		$description = $this->agent_data->description;

		if ( $args['length'] > 0 ) {
			$description = wp_trim_words( $description, $args['length'] );
		}

		return $this->macros_wrap( $args, $description );
	}

	/**
	 * Get agent contacts.
	 *
	 * @return string
	 */
	public function get_agent_contacts( $args = array() ) {

		if ( isset( $this->atts['show_contacts'] ) && false === $this->atts['show_contacts'] ) {
			return;
		}

		$agent_id = $this->get_agent_id();
		$contacts = Model_Agents::get_agent_contacts( $agent_id );

		if ( empty( $contacts ) ) {
			return;
		}

		$defaults = array(
			'icon'  => '',
			'label' => '',
			'value' => '',
		);

		$args = wp_parse_args( $args, array(
			'wrap'  => 'div',
			'class' => 'tm-agent-contacts',
		) );

		// Global item format.
		$item_format = apply_filters(
			'cherry_re_agent_contacts_item_format',
			'<div class="%5$s-item%4$s">%1$s<span class="%5$s-item-label">%2$s</span><span class="%5$s-item-value">%3$s</span></div>'
		);

		// Icon format.
		$icon_format = apply_filters(
			'cherry_re_agent_contact_icon_format',
			'<i class="%2$s-item-icon fa %1$s"></i>'
		);

		$result = '';

		foreach ( $contacts as $data ) {

			$class = esc_attr( $args['class'] );
			$data  = wp_parse_args( $data, $defaults );
			$icon  = sprintf( $icon_format, esc_attr( $data['icon'] ), $class );
			$label = esc_attr( $data['label'] );
			$value = esc_attr( $data['value'] );

			$label_class = '';

			if ( ! $label ) {
				$label_class = ' empty-label';
			}

			if ( ! $value ) {
				continue;
			}

			$result .= sprintf( $item_format, $icon, $label, $value, $label_class, $class );
		}

		return $this->macros_wrap( $args, $result );
	}

	/**
	 * Get agent socials list.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_agent_socials( $args = array() ) {

		if ( isset( $this->atts['show_socials'] ) && false === $this->atts['show_socials'] ) {
			return;
		}

		$agent_id = $this->get_agent_id();
		$socials  = Model_Agents::get_agent_socials( $agent_id );

		if ( empty( $socials ) ) {
			return;
		}

		$defaults = array(
			'icon'  => '',
			'label' => '',
			'value' => '',
		);

		$args = wp_parse_args( $args, array(
			'wrap'  => 'div',
			'class' => 'tm-agent-socials',
		) );

		// Global item format.
		$format = apply_filters(
			'cherry_re_agent_socials_item_format',
			'<div class="%5$s-item%4$s"><a href="%3$s" class="%5$s-item-link" rel="nofollow" target="_blank">%1$s<span class="%5$s-item-label">%2$s</span></a></div>'
		);

		// Icon format.
		$icon_format = apply_filters(
			'cherry_re_agent_social_icon_format',
			'<i class="%2$s-item-icon fa %1$s"></i>'
		);

		$result = '';

		foreach ( $socials as $data ) {

			$class = esc_attr( $args['class'] );
			$data  = wp_parse_args( $data, $defaults );
			$value = esc_url( $data['value'] );
			$icon  = sprintf( $icon_format, esc_attr( $data['icon'] ), $class );
			$label = esc_attr( $data['label'] );

			$label_class = '';

			if ( ! $label ) {
				$label_class = ' empty-label';
			}

			if ( ! $value ) {
				continue;
			}

			$result .= sprintf( $format, $icon, $label, $value, $label_class, $class );
		}

		return $this->macros_wrap( $args, $result );
	}

	/**
	 * Get agent read more button.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments.
	 * @return string
	 */
	public function get_agent_more( $args = array() ) {

		if ( isset( $this->atts['show_more_button'] ) && false === $this->atts['show_more_button'] ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'class' => '',
			'text'  => ! empty( $this->atts['more_button_text'] ) ? esc_attr( $this->atts['more_button_text'] ) : esc_html__( 'read more', 'cherry-real-estate' ),
		) );

		$link = $this->get_agent_permalink();

		return sprintf(
			'<a href="%s" class="%s">%s</a>',
			esc_url( $link ),
			esc_attr( $args['class'] ),
			esc_html( $args['text'] )
		);
	}

	/**
	 * Get agent ID.
	 *
	 * @since 1.0.0
	 * @return int
	 */
	public function get_agent_id() {

		if ( is_object( $this->agent_data ) && ! empty( $this->agent_data->ID ) ) {
			$agent_id = $this->agent_data->ID;
		} elseif ( get_query_var( 'author' ) ) {
			$agent_id = get_query_var( 'author' );
		} else {
			$agent_id = get_current_user_id();
		}

		return $agent_id;
	}

	/**
	 * Set a agent meta data.
	 *
	 * @since 1.0.0
	 */
	public function the_agent_meta( $agent_obj ) {
		$this->agent_data = $agent_obj;
	}

	/**
	 * Get the URL of the agent page.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_agent_permalink() {

		if ( ! $this->agent_data ) {
			return;
		}

		if ( ! isset( $this->agent_data->agent_permalink ) ) {
			$this->agent_data->agent_permalink = get_author_posts_url( $this->agent_data->ID );
		}

		return $this->agent_data->agent_permalink;
	}

	/**
	 * Set a property meta data.
	 *
	 * @since 1.0.0
	 */
	public function the_property_data() {
		global $post;

		$prefix              = cherry_real_estate()->get_meta_prefix();
		$this->property_data = get_post_meta( $post->ID, '', true );
	}

	/**
	 * Retrieve a property meta value.
	 *
	 * @since  1.0.0
	 * @param  string $key Meta key.
	 * @return bool|string
	 */
	public function get_the_property_data( $key ) {
		$key = cherry_real_estate()->get_meta_prefix() . $key;

		if ( ! isset( $this->property_data[ $key ] ) ) {
			return false;
		}

		return $this->property_data[ $key ][0];
	}

	/**
	 * Get property title.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_property_title( $args = array() ) {

		if ( isset( $this->atts['show_title'] ) && false === $this->atts['show_title'] ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'  => 'h5',
			'class' => '',
			'link'  => false,
		) );

		$result       = $this->post_title();
		$args['link'] = filter_var( $args['link'], FILTER_VALIDATE_BOOLEAN );

		if ( true === $args['link'] ) {
			$result = '<a href="' . esc_url( $this->property_link() ) . '">' . $result . '</a>';
		}

		return $this->macros_wrap( $args, $result );
	}

	/**
	 * Get property exerpt.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_property_excerpt( $args = array() ) {

		if ( isset( $this->atts['show_excerpt'] ) && false === $this->atts['show_excerpt'] ) {
			return;
		}

		global $post;

		$args = wp_parse_args( $args, array(
			'wrap'   => 'div',
			'class'  => '',
			'length' => isset( $this->atts['excerpt_length'] ) ? intval( $this->atts['excerpt_length'] ) : 15,
		) );

		$excerpt = has_excerpt( $post->ID ) ? apply_filters( 'the_excerpt', get_the_excerpt() ) : '';

		if ( '' == $excerpt ) :

			if ( -1 == $args['length'] ) {
				$excerpt = apply_filters( 'the_content', get_the_content() );

			} else {
				$content = get_the_content();
				$excerpt = strip_shortcodes( $content );
				$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
				$excerpt = wp_trim_words( $excerpt, $args['length'] );
			}

		endif;

		return $this->macros_wrap( $args, $excerpt );
	}

	/**
	 * Get property price.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_property_price( $args = array() ) {
		global $post;

		if ( isset( $this->atts['show_price'] ) && false === $this->atts['show_price'] ) {
			return;
		}

		$price = $this->get_the_property_data( 'price' );

		if ( false === $price ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'  => 'div',
			'class' => '',
		) );

		$decimals           = Model_Settings::get_decimal_numb();
		$decimal_separator  = Model_Settings::get_decimal_sep();
		$thousand_separator = Model_Settings::get_thousand_sep();
		$price_format       = Model_Settings::get_price_format();
		$symbol             = Model_Settings::get_currency_symbol();

		$price = floatval( $price );
		$price = apply_filters( 'cherry_re_formatted_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

		$amount = sprintf(
			$price_format,
			'<span class="' . esc_attr( $args['class'] . '-symbol' ) . '">' . $symbol . '</span>',
			'<span class="' . esc_attr( $args['class'] . '-value' ) . '">' . $price . '</span>'
		);

		return $this->macros_wrap( $args, $amount );
	}

	/**
	 * Get property location.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_property_location( $args = array() ) {

		if ( isset( $this->atts['show_location'] ) && false === $this->atts['show_location'] ) {
			return;
		}

		$location = $this->get_the_property_data( 'location' );

		if ( false === $location ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'  => '',
			'class' => '',
		) );

		return $this->macros_wrap( $args, esc_attr( $location ) );
	}

	/**
	 * Get property bedrooms.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_property_bedrooms( $args = array() ) {

		if ( isset( $this->atts['show_bedrooms'] ) && false === $this->atts['show_bedrooms'] ) {
			return;
		}

		$bedrooms = $this->get_the_property_data( 'bedrooms' );

		if ( false === $bedrooms ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'  => 'span',
			'class' => '',
		) );

		$bedrooms = (int) $bedrooms;

		return $this->macros_wrap( $args, $bedrooms );
	}

	/**
	 * Get property bathrooms.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_property_bathrooms( $args = array() ) {

		if ( isset( $this->atts['show_bathrooms'] ) && false === $this->atts['show_bathrooms'] ) {
			return;
		}

		$bathrooms = $this->get_the_property_data( 'bathrooms' );

		if ( false === $bathrooms ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'  => 'span',
			'class' => '',
		) );

		$bathrooms = (int) $bathrooms;

		return $this->macros_wrap( $args, $bathrooms );
	}

	/**
	 * Get property area.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_property_area( $args = array() ) {

		if ( isset( $this->atts['show_area'] ) && false === $this->atts['show_area'] ) {
			return;
		}

		$area = $this->get_the_property_data( 'area' );

		if ( false === $area ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'  => 'span',
			'class' => '',
		) );

		$area = (float) $area;
		$area = sprintf( '%01.2f&nbsp;%s', $area, esc_html( Model_Settings::get_area_unit_title() ) );

		return $this->macros_wrap( $args, $area );
	}

	/**
	 * Get property parking places.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_property_parking_places( $args = array() ) {

		if ( isset( $this->atts['show_parking_places'] ) && false === $this->atts['show_parking_places'] ) {
			return;
		}

		$parking_places = $this->get_the_property_data( 'parking_places' );

		if ( false === $parking_places ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'  => 'span',
			'class' => '',
		) );

		$parking_places = (int) $parking_places;

		return $this->macros_wrap( $args, $parking_places );
	}

	/**
	 * Get property status.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_property_status( $args = array() ) {

		if ( isset( $this->atts['show_status'] ) && false === $this->atts['show_status'] ) {
			return;
		}

		$status = $this->get_the_property_data( 'status' );

		if ( false === $status ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'  => 'span',
			'class' => '',
		) );

		$allowed = Model_Properties::get_allowed_property_statuses();

		if ( ! array_key_exists( $status, $allowed ) ) {
			$value = end( $allowed );
		} else {
			$value = $allowed[ $status ];
		}

		return $this->macros_wrap( $args, esc_attr( $value ) );
	}

	/**
	 * Get property read more button.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments.
	 * @return string
	 */
	public function get_property_more( $args = array() ) {

		if ( isset( $this->atts['show_more_button'] ) && false === $this->atts['show_more_button'] ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'class' => '',
			'text'  => ! empty( $this->atts['more_button_text'] ) ? esc_attr( $this->atts['more_button_text'] ) : esc_html__( 'read more', 'cherry-real-estate' ),
		) );

		return sprintf(
			'<a href="%s" class="%s">%s</a>',
			esc_url( $this->property_link() ),
			esc_attr( $args['class'] ),
			esc_html( $args['text'] )
		);
	}

	/**
	 * Get property image.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments array.
	 * @return string
	 */
	public function get_property_image( $args = array() ) {

		if ( isset( $this->atts['show_image'] ) && false === $this->atts['show_image'] ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'  => 'div',
			'class' => '',
			'size'  => ! empty( $this->atts['image_size'] ) ? esc_attr( $this->atts['image_size'] ) : 'thumbnail',
			'link'  => true,
		) );

		$image = $this->post_image( $args['size'] );

		if ( ! $image ) {
			return;
		}

		add_filter( 'cherry_re_property_item_classes', array( $this, 'add_thumb_class' ), 10, 2 );

		$args['link'] = filter_var( $args['link'], FILTER_VALIDATE_BOOLEAN );

		if ( true === $args['link'] ) {
			$format = '<a href="%2$s">%1$s</a>';
			$link   = $this->property_link();
		} else {
			$format = '%1$s';
			$link   = false;
		}

		return $this->macros_wrap( $args, sprintf( $format, $image, esc_url( $link ) ) );
	}

	/**
	 * Get property content.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_content( $args = array() ) {
		$content = apply_filters( 'the_content', get_the_content() );

		if ( ! $content ) {
			return;
		}

		$args = wp_parse_args( $args, array(
			'wrap'  => 'span',
			'class' => '',
		) );

		return $this->macros_wrap( $args, $content );
	}

	/**
	 * Get post image.
	 *
	 * @since  1.0.0
	 * @param  string $size Image size.
	 * @return string
	 */
	public function post_image( $size ) {
		global $post;

		$key = cherry_real_estate()->get_meta_prefix() . 'image';

		if ( ! isset( $this->property_data[ $key ] ) ) {

			if ( has_post_thumbnail( $post->ID ) ) {

				$this->property_data[ $key ] = get_the_post_thumbnail(
					intval( $post->ID ),
					$size,
					array( 'alt' => $this->post_title() )
				);

			} else {

				$gallery_ids = $this->post_gallery();

				if ( ! is_array( $gallery_ids ) || empty( $gallery_ids ) ) {
					return;
				}

				$image_src = wp_get_attachment_image_src(
					$gallery_ids[0],
					$size
				);

				if ( ! $image_src ) {
					return;
				}

				$this->property_data[ $key ] = sprintf(
					'<img src="%s" alt="%s">',
					esc_url( $image_src[0] ),
					esc_attr( $this->post_title() )
				);
			}
		}

		return $this->property_data[ $key ];
	}

	/**
	 * Get post gallery.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function post_gallery() {
		$gallery = $this->get_the_property_data( 'gallery' );

		if ( false === $gallery ) {
			return;
		}

		if ( is_string( $gallery ) ) {
			return explode( ',', $gallery );
		}

		return $gallery;
	}

	/**
	 * Get post title.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function post_title() {

		if ( ! isset( $this->property_data['title'] ) ) {
			$this->property_data['title'] = get_the_title();
		}

		return $this->property_data['title'];
	}

	/**
	 * Get post permalink.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function property_link() {

		if ( ! isset( $this->property_data['permalink'] ) ) {
			$this->property_data['permalink'] = get_permalink();
		}

		return $this->property_data['permalink'];
	}

	/**
	 * Wrap macros output into wrapper passed via arguments.
	 *
	 * @since  1.0.0
	 * @param  array  $args   Arguments array.
	 * @param  string $string Macros string to wrap.
	 * @return string
	 */
	public function macros_wrap( $args = array(), $string = '' ) {

		if ( '' === $string ) {
			return '';
		}

		if ( empty( $args['wrap'] ) ) {
			return $string;
		}

		$tag   = esc_attr( $args['wrap'] );
		$class = ! empty( $args['class'] ) ? esc_attr( $args['class'] ) : '';

		return sprintf( '<%1$s class="%2$s">%3$s</%1$s>', $tag, $class, $string );
	}

	/**
	 * Gets metadata by name.
	 *
	 * @since  1.0.0
	 * @param  string $meta Meta name to get.
	 * @return string
	 */
	public function get_meta( $meta ) {
		global $post;

		return get_post_meta( $post->ID, cherry_real_estate()->get_meta_prefix() . $meta, true );
	}

	/**
	 * Gets metadata by name and return HTML markup.
	 *
	 * @since  1.0.0
	 * @param  string $meta Meta name to get.
	 * @return string
	 */
	public function get_meta_html( $meta ) {
		$value = $this->get_meta( $meta );

		return ( ! empty( $value ) ) ? $this->meta_wrap( $value, $meta ) : '';
	}

	/**
	 * Clear data after loop iteration.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function clear_data() {
		$this->agent_data    = null;
		$this->property_data = array();
	}

	/**
	 * Added a specific CSS-class for image control.
	 *
	 * @since 1.0.0
	 * @param array $classes     An array of post classes.
	 * @param int   $property_id The property ID.
	 */
	public function add_thumb_class( $classes, $property_id ) {
		$classes[] = 'tm-property-has-thumb';

		return $classes;
	}
}
