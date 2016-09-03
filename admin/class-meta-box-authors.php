<?php
/**
 * Handles the author avatars meta box.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Admin
 * @version    1.0.0
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/avatars-meta-box
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Meta box class.
 *
 * @since  1.0.0
 * @access public
 */
class Cherry_RE_Meta_Box_Authors {

	/**
	 * Sets up the appropriate actions.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return void
	 */
	protected function __construct() {
		add_action( 'load-post.php',     array( $this, 'load' ) );
		add_action( 'load-post-new.php', array( $this, 'load' ) );
	}

	/**
	 * Fires on the page load hook to add actions specifically for the post and
	 * new post screens.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
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
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function add_meta_boxes( $post_type ) {

		// If the post type doesn't support `author`, bail.
		if ( ! post_type_supports( $post_type, 'author' ) ) {
			return;
		}

		// Add our custom meta box.
		add_meta_box(
			'cherry-re-custom-authors',
			esc_html__( 'Author', 'cherry-real-estate' ),
			array( $this, 'meta_box' ),
			$post_type,
			'side',
			'default'
		);
	}

	/**
	 * Outputs the meta box HTML.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object $post Current post object.
	 * @return void
	 */
	public function meta_box( $post ) {
		$value = get_post_meta( $post->ID, $this->get_meta_key(), true );
		$value = ! empty( $value ) ? $value : $post->post_author;

		// Set up the main arguments for `get_users()`.
		$args = array( 'who' => 'authors' );

		// WP version 4.4.0 check. User `role__in` if we can.
		if ( method_exists( 'WP_User_Query', 'fill_query_vars' ) ) {
			$args = array( 'role__in' => $this->get_roles( $post->post_type ) );
		}

		// Get the users allowed to be post author.
		$users = get_users( apply_filters( 'cherry_re_authors_metabox_get_users_args', $args ) );

		cherry_re_get_template(
			'authors',
			array(
				'key'   => $this->get_meta_key(),
				'value' => $value,
				'users' => $users,
				'nonce' => wp_create_nonce( plugin_basename( __FILE__ ) ),
			),
			cherry_real_estate()->template_path(),
			CHERRY_REAL_ESTATE_DIR . 'views/metabox/'
		);
	}

	/**
	 * Returns an array of user roles that are allowed to edit, publish, or create
	 * posts of the given post type.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string $post_type Post type aname.
	 * @global WP_Roles $wp_roles  WP_Roles global instance.
	 * @return array
	 */
	public function get_roles( $post_type ) {
		global $wp_roles;

		$roles = array();
		$type  = get_post_type_object( $post_type );

		// Get the post type object caps.
		$caps = array( $type->cap->edit_posts, $type->cap->publish_posts, $type->cap->create_posts );
		$caps = apply_filters( 'cherry_re_authors_metabox_get_roles_caps', $caps );
		$caps = array_unique( $caps );

		// Loop through the available roles.
		foreach ( $wp_roles->roles as $name => $role ) {

			foreach ( $caps as $cap ) {

				// If the role is granted the cap, add it.
				if ( isset( $role['capabilities'][ $cap ] ) && true === $role['capabilities'][ $cap ] ) {
					$roles[] = $name;
					break;
				}
			}
		}

		return $roles;
	}

	/**
	 * Saves the custom post meta for the menu item.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  int    $post_id The post ID.
	 * @param  object $post    The post object.
	 * @return void
	 */
	public function save_post( $post_id, $post ) {

		/* Verify the nonce. */
		if ( ! isset( $_POST['cherry_re_custom_authors_meta_nonce'] ) || ! wp_verify_nonce( $_POST['cherry_re_custom_authors_meta_nonce'], plugin_basename( __FILE__ ) ) ) {
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

		$prefix = cherry_real_estate()->get_meta_prefix();

		$meta = array(
			$this->get_meta_key() => intval( strip_tags( $_POST[ $this->get_meta_key() ] ) ),
		);

		foreach ( $meta as $meta_key => $new_meta_value ) {

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
	}

	/**
	 * Retrieve a meta key.
	 *
	 * @since 1.0.0
	 * @author Template Monster
	 * @return string
	 */
	public function get_meta_key() {
		return cherry_real_estate()->get_meta_prefix() . 'author';
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
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

Cherry_RE_Meta_Box_Authors::get_instance();
