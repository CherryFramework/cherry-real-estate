<?php
/**
 * Handles the location meta box.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Admin
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Meta box class.
 *
 * @since  1.1.0
 * @access public
 */
class Cherry_RE_Meta_Box_Location {

	/**
	 * Sets up the appropriate actions.
	 *
	 * @since 1.1.0
	 */
	protected function __construct() {
		add_action( 'load-post.php',     array( $this, 'load' ) );
		add_action( 'load-post-new.php', array( $this, 'load' ) );
	}

	/**
	 * Fires on the page load hook to add actions specifically for the post and
	 * new post screens.
	 *
	 * @since 1.1.0
	 */
	public function load() {
		$current_screen = get_current_screen();
		$post_type_name = cherry_real_estate()->get_post_type_name();

		if ( empty( $current_screen->post_type ) || $post_type_name !== $current_screen->post_type ) {
			return;
		}

		// Add custom meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
	}

	/**
	 * Adds the meta box.
	 *
	 * @since 1.1.0
	 */
	public function add_meta_boxes( $post_type ) {

		// Add our custom meta box.
		add_meta_box(
			'cherry-re-location',
			esc_html__( 'Location', 'cherry-real-estate' ),
			array( $this, 'meta_box' ),
			$post_type,
			'normal',
			'high'
		);
	}

	/**
	 * Outputs the meta box HTML.
	 *
	 * @since 1.1.0
	 * @param WP_Post $post Post object.
	 * @param object  $box  Contains the callback arguments along with other data on the current meta box
	 */
	public function meta_box( $post, $box ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( plugin_basename( __FILE__ ), 'cherry_re_location_meta_nonce' );

		if ( false === apply_filters( 'cherry_re_deenqueue_location_metabox', false ) ) {
			$this->enqueue_assets();
		}

		$values = get_post_meta( get_the_ID(), '', true );
		$values = ! empty( $values ) ? $values : array();

		cherry_re_get_template(
			'location',
			array(
				'key'    => $this->get_meta_key(),
				'values' => $values,
				'nonce'  => wp_create_nonce( plugin_basename( __FILE__ ) ),
			),
			cherry_real_estate()->template_path(),
			CHERRY_REAL_ESTATE_DIR . 'views/metabox/'
		);
	}

	/**
	 * Saves the custom post meta.
	 *
	 * @since  1.1.0
	 * @param  int    $post_id The post ID.
	 * @param  object $post    The post object.
	 */
	public function save_post( $post_id, $post ) {

		/* Verify the nonce. */
		if ( ! isset( $_POST['cherry_re_location_meta_nonce'] )
			|| ! wp_verify_nonce( $_POST['cherry_re_location_meta_nonce'], plugin_basename( __FILE__ ) ) )
		{
			return;
		}

		$post_type_name = cherry_real_estate()->get_post_type_name();

		if ( $post_type_name !== $post->post_type ) {
			return;
		}

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* Check if the current user has permission to edit the post. */
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		/* Don't save if the post is only a revision. */
		if ( 'revision' == $post->post_type ) {
			return;
		}

		$fields = $this->get_fields();

		foreach ( (array) $fields as $meta_key => $sanitize_callback ) {

			if ( ! isset( $_POST[ $meta_key ] ) ) {
				continue;
			}

			if ( ! is_callable( $sanitize_callback ) ) {
				continue;
			}

			$new_meta_value = call_user_func( $sanitize_callback, $_POST[ $meta_key ] );

			/* Get the meta value of the custom field key. */
			$meta_value = get_post_meta( $post_id, $meta_key, true );

			/* If a new meta value was added and there was no previous value, add it. */
			if ( $new_meta_value && '' == $meta_value ) {
				add_post_meta( $post_id, $meta_key, $new_meta_value, true );

			} elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
				/* If the new meta value does not match the old value, update it. */
				update_post_meta( $post_id, $meta_key, $new_meta_value );

			} elseif ( '' == $new_meta_value && $meta_value ) {
				/* If there is no new meta value but an old value exists, delete it. */
				delete_post_meta( $post_id, $meta_key, $meta_value );
			}
		}

		if ( current_theme_supports( 'cherry-real-estate-geodata' ) ) {
			$this->_save_geo_data( $post_id );
		}
	}

	/**
	 * Set WordPress GeoData.
	 *
	 * @link  http://codex.wordpress.org/Geodata
	 * @param $post_id
	 */
	public function _save_geo_data( $post_id ) {
		$meta_key  = $this->get_meta_key();
		$latitude  = get_post_meta( $post_id, $meta_key . 'latitude', true );
		$longitude = get_post_meta( $post_id, $meta_key . 'longitude', true );

		if ( $latitude ) {
			update_post_meta( $post_id, 'geo_latitude', floatval( $latitude ) );
		} else {
			delete_post_meta( $post_id, 'geo_latitude' );
		}

		if ( $longitude ) {
			update_post_meta( $post_id, 'geo_longitude', floatval( $longitude ) );
		} else {
			delete_post_meta( $post_id, 'geo_longitude' );
		}
	}

	/**
	 * Enqueue metabox assets.
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'cherry-re-admin-style' );
		wp_enqueue_script( 'cherry-re-geocomplete-init' );
	}

	/**
	 * Retrieve a meta key.
	 *
	 * @since 1.1.0
	 * @return string
	 */
	public function get_meta_key() {
		return cherry_real_estate()->get_meta_prefix();
	}

	/**
	 * Retrieve a metabox fields data - `meta_key_field` => `sanitize_callback`
	 * with WordPress Geodata recommendation - https://codex.wordpress.org/Geodata
	 *
	 * @since  1.1.0
	 * @return array
	 */
	public function get_fields() {
		$meta_key = $this->get_meta_key();

		return apply_filters( 'cherry_re_get_location_metabox_fields' ,array(
			$meta_key . 'place_id'  => 'sanitize_text_field',
			$meta_key . 'latitude'  => 'sanitize_text_field',
			$meta_key . 'longitude' => 'sanitize_text_field',
			$meta_key . 'location'  => 'sanitize_text_field',
		) );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.1.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
		}

		return $instance;
	}
}

Cherry_RE_Meta_Box_Location::get_instance();
