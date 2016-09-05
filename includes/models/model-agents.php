<?php
/**
 * Agents
 *
 * @package    Cherry_Real_Estate
 * @subpackage Models
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Model agents.
 *
 * @since 1.0.0
 */
class Model_Agents {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'show_user_profile', array( $this, 'add_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'add_profile_fields' ) );

		add_action( 'personal_options_update',  array( $this, 'save_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_profile_fields' ) );

		// Add Properties to Author Archives Page.
		add_action( 'pre_get_posts', array( $this, 'author_archive' ) );

		add_action( 'load-post.php',     array( $this, 'load' ) );
		add_action( 'load-post-new.php', array( $this, 'load' ) );
	}

	/**
	 * Add a custom profile fields to the profile page.
	 *
	 * @since 1.0.0
	 * @param WP_User $user The current WP_User object.
	 */
	public function add_profile_fields( $user ) {
		$this->_add_trusted( $user );

		$post_type = cherry_real_estate()->get_post_type_name();
		$obj       = get_post_type_object( $post_type );
		$caps      = $obj->cap->edit_published_posts;

		// Output only for users with RE-capabilities.
		if ( ! user_can( $user, $caps ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			return false;
		}

		$this->_add_photo( $user );
		$this->_add_contacts( $user );
	}

	/**
	 * Save values from custom fields in profile page.
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID.
	 */
	public function save_profile_fields( $user_id ) {
		$this->_save_trusted( $user_id );

		$post_type = cherry_real_estate()->get_post_type_name();
		$obj       = get_post_type_object( $post_type );
		$caps      = $obj->cap->edit_published_posts;

		// Output only for users with RE-capabilities.
		if ( ! user_can( $user_id, $caps ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$this->_save_contacts( $user_id );
		$this->_save_photo( $user_id );
	}

	/**
	 * Add a contact profile fields.
	 *
	 * @since 1.0.0
	 * @param WP_User $user The current WP_User object.
	 */
	public function _add_contacts( $user ) {
		$prefix = cherry_real_estate()->get_meta_prefix();

		// Prerape arguments for custom fields.
		$args = array(
			array(
				'id'          => $prefix . 'agent_contacts',
				'name'        => $prefix . 'agent_contacts',
				'value'       => get_the_author_meta( $prefix . 'agent_contacts', $user->ID ),
				'label'       => esc_html__( 'Contacts', 'cherry-real-estate' ),
				'add_label'   => esc_html__( 'Add contact', 'cherry-real-estate' ),
				'title_field' => 'contact_name',
				'fields'      => array(
					'icon' => array(
						'type'      => 'iconpicker',
						'id'        => 'icon',
						'name'      => 'icon',
						'label'     => esc_html__( 'Choose icon', 'cherry-real-estate' ),
						'icon_data' => array(
							'icon_set'    => 'cherryREFontAwesome',
							'icon_css'    => CHERRY_REAL_ESTATE_URI . 'assets/css/font-awesome.min.css',
							'icon_base'   => 'fa',
							'icon_prefix' => 'fa-',
							'icons'       => Cherry_RE_Tools::get_icons_set(),
						),
					),
					'label' => array(
						'type'  => 'text',
						'id'    => 'label',
						'name'  => 'label',
						'label' => esc_html__( 'Label', 'cherry-real-estate' ),
					),
					'value' => array(
						'type'  => 'text',
						'id'    => 'value',
						'name'  => 'value',
						'label' => esc_html__( 'Value', 'cherry-real-estate' ),
					),
				),
			),
			array(
				'id'          => $prefix . 'agent_socials',
				'name'        => $prefix . 'agent_socials',
				'value'       => get_the_author_meta( $prefix . 'agent_socials', $user->ID ),
				'label'       => esc_html__( 'Social profiles', 'cherry-real-estate' ),
				'add_label'   => esc_html__( 'Add Social Network', 'cherry-real-estate' ),
				'title_field' => 'label',
				'fields'      => array(
					'icon' => array(
						'type'        => 'iconpicker',
						'id'          => 'icon',
						'name'        => 'icon',
						'label'       => esc_html__( 'Choose icon', 'cherry-real-estate' ),
						'icon_data'   => array(
							'icon_set'    => 'cherryREFontAwesome',
							'icon_css'    => CHERRY_REAL_ESTATE_URI . 'assets/css/font-awesome.min.css',
							'icon_base'   => 'fa',
							'icon_prefix' => 'fa-',
							'icons'       => Cherry_RE_Tools::get_icons_set(),
						),
					),
					'label' => array(
						'type'        => 'text',
						'id'          => 'label',
						'name'        => 'label',
						'placeholder' => esc_html__( 'Label', 'cherry-real-estate' ),
						'label'       => esc_html__( 'Label', 'cherry-real-estate' ),
					),
					'value' => array(
						'type'        => 'text',
						'id'          => 'value',
						'name'        => 'value',
						'placeholder' => esc_html__( 'URL', 'cherry-real-estate' ),
						'label'       => esc_html__( 'URL', 'cherry-real-estate' ),
					),
				),
			),
		);

		$html = '';

		foreach ( $args as $arg ) {
			$control = new UI_Repeater( $arg );
			$html .= $control->render();
		}

		cherry_re_get_template(
			'contacts-field',
			array(
				'control_html' => $html,
			),
			cherry_real_estate()->template_path(),
			CHERRY_REAL_ESTATE_DIR . 'views/profile/'
		);
	}

	/**
	 * Add a photo profile field.
	 *
	 * @since  1.0.0
	 * @param  WP_User $user The current WP_User object.
	 * @return string
	 */
	public function _add_photo( $user ) {

		if ( ! current_user_can( 'upload_files', $user->ID ) ) {
			return false;
		}

		$prefix   = cherry_real_estate()->get_meta_prefix();
		$photo_id = self::get_agent_photo_id( $user->ID );

		if ( ! $photo_id ) {
			$btn_text = esc_html__( 'Upload', 'cherry-real-estate' );
		} else {
			$btn_text = esc_html__( 'Change', 'cherry-real-estate' );
		}

		$control = new UI_Media( array(
			'id'                 => $prefix . 'agent_photo',
			'name'               => $prefix . 'agent_photo',
			'value'              => $photo_id,
			'multi_upload'       => false,
			'library_type'       => 'image',
			'upload_button_text' => $btn_text,
		) );

		cherry_re_get_template(
			'photo-field',
			array(
				'control_name' => $prefix . 'agent_photo',
				'control_html' => $control->render(),
			),
			cherry_real_estate()->template_path(),
			CHERRY_REAL_ESTATE_DIR . 'views/profile/'
		);
	}

	/**
	 * Add a photo profile field.
	 *
	 * @since  1.0.0
	 * @param  WP_User $user The current WP_User object.
	 * @return string
	 */
	public function _add_trusted( $user ) {
		$post_type = cherry_real_estate()->get_post_type_name();
		$type      = get_post_type_object( $post_type );
		$caps      = $type->cap->delete_published_posts;

		// Output only for RE agents.
		if ( ! user_can( $user, $caps ) ) {
			return false;
		}

		// Visibility only for admin.
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$prefix = cherry_real_estate()->get_meta_prefix();
		$value  = self::get_agent_trust( $user->ID );
		$value  = ! empty( $value ) ? $value : 'false';

		$control = new UI_Switcher( array(
			'id'     => $prefix . 'agent_trust',
			'name'   => $prefix . 'agent_trust',
			'value'  => $value,
			'style'  => ( wp_is_mobile() ) ? 'normal' : 'small',
			'toggle' => array(
				'true_toggle'  => esc_html__( 'Yes', 'cherry-real-estate' ),
				'false_toggle' => esc_html__( 'No', 'cherry-real-estate' ),
			),
		) );

		cherry_re_get_template(
			'trust-field',
			array(
				'control_name' => $prefix . 'agent_trust',
				'control_html' => $control->render(),
			),
			cherry_real_estate()->template_path(),
			CHERRY_REAL_ESTATE_DIR . 'views/profile/'
		);
	}

	/**
	 * Save agent contacts.
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID.
	 */
	public function _save_contacts( $user_id ) {
		$prefix   = cherry_real_estate()->get_meta_prefix();
		$contacts = isset( $_POST[ $prefix . 'agent_contacts' ] ) ? $_POST[ $prefix . 'agent_contacts' ]: '';
		$socials  = isset( $_POST[ $prefix . 'agent_socials' ] ) ? $_POST[ $prefix . 'agent_socials' ]: '';

		update_user_meta(
			absint( $user_id ),
			$prefix . 'agent_contacts',
			$contacts
		);

		update_user_meta(
			absint( $user_id ),
			$prefix . 'agent_socials',
			$socials
		);
	}

	/**
	 * Save agent photo.
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID.
	 */
	public function _save_photo( $user_id ) {

		if ( ! current_user_can( 'upload_files', $user_id ) ) {
			return false;
		}

		$prefix = cherry_real_estate()->get_meta_prefix();

		if ( isset( $_POST[ $prefix . 'agent_photo' ] ) ) {
			update_user_meta(
				absint( $user_id ),
				$prefix . 'agent_photo',
				sanitize_text_field( $_POST[ $prefix . 'agent_photo' ] )
			);
		}
	}

	/**
	 * Save agent option for trust.
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID.
	 */
	public function _save_trusted( $user_id ) {
		$post_type = cherry_real_estate()->get_post_type_name();
		$type      = get_post_type_object( $post_type );
		$caps      = $type->cap->edit_posts;

		if ( ! user_can( $user_id, $caps ) || ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$prefix = cherry_real_estate()->get_meta_prefix();

		if ( isset( $_POST[ $prefix . 'agent_trust' ] ) ) {
			update_user_meta(
				absint( $user_id ),
				$prefix . 'agent_trust',
				sanitize_text_field( $_POST[ $prefix . 'agent_trust' ] )
			);
		}
	}

	/**
	 * Add Properties to Author Archives Page.
	 *
	 * @since 1.0.0
	 * @param object $query Main query.
	 */
	public function author_archive( $query ) {

		if ( ! is_admin() && $query->is_main_query() && $query->is_author ) {
			$query->set( 'post_type', array( cherry_real_estate()->get_post_type_name(), 'post' ) );

			add_filter( 'body_class', array( $this, 'body_class' ) );
		}

		remove_action( 'pre_get_posts', array( $this, 'author_archive' ) );
	}

	/**
	 * Add a custom CSS-class in Author Archives Page.
	 *
	 * @since  1.0.0
	 * @param  array $classes CSS-classes.
	 * @return array
	 */
	public function body_class( $classes ) {
		$classes[] = 'tm-agent';

		return $classes;
	}

	/**
	 * Fires on the page load hook to add actions specifically for the post and
	 * new post screens.
	 *
	 * @author Justin Tadlock <justin@justintadlock.com>
	 * @author Template Monster
	 * @since  1.0.0
	 */
	public function load() {
		$current_screen = get_current_screen();
		$post_type_name = cherry_real_estate()->get_post_type_name();

		if ( empty( $current_screen->post_type ) || $post_type_name !== $current_screen->post_type ) {
			return;
		}

		// Filtering the author drop-down.
		add_filter( 'wp_dropdown_users_args', array( $this, 'dropdown_users_args' ), 10, 2 );

		// Changed position to the athor metabox.
		add_action( 'do_meta_boxes' , array( $this, 'relocate_author_metabox' ) );
	}

	/**
	 * Filtering the author drop-down.
	 *
	 * @author Justin Tadlock <justin@justintadlock.com>
	 * @author Template Monster
	 * @since  1.0.0
	 * @param  array $args The query arguments for wp_dropdown_users().
	 * @param  array $r    The default arguments for wp_dropdown_users().
	 * @return array
	 */
	public function dropdown_users_args( $args, $r ) {
		global $post;

		$post_type_name = cherry_real_estate()->get_post_type_name();

		// Check that this is the correct drop-down.
		if ( 'post_author_override' === $r['name'] && $post_type_name === $post->post_type ) {

			$roles = $this->get_roles_for_post_type( $post->post_type );

			// If we have roles, change the args to only get users of those roles.
			if ( $roles ) {
				$args['who']      = '';
				$args['include']  = false;
				$args['role__in'] = $roles;
			}
		}

		return $args;
	}

	/**
	 * Getting roles with permission.
	 *
	 * @author Justin Tadlock <justin@justintadlock.com>
	 * @author Template Monster
	 * @since  1.0.0
	 * @param  array $post_type Post type name.
	 * @return array
	 */
	public function get_roles_for_post_type( $post_type ) {
		global $wp_roles;

		$roles = array();
		$type  = get_post_type_object( $post_type );

		// Get the post type object caps.
		$caps = array( $type->cap->delete_published_posts );
		$caps = apply_filters( 'cherry_re_get_roles_for_author_meta_box', $caps, $post_type );
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
	 * Changed position to the athor metabox.
	 *
	 * @since 1.0.0
	 */
	public function relocate_author_metabox() {
		global $post;

		$post_type_name = cherry_real_estate()->get_post_type_name();

		remove_meta_box( 'authordiv', array( $post_type_name ), 'normal' );

		// Only for administrator.
		if ( current_user_can( 'manage_options' ) ) {
			add_meta_box( 'authordiv', esc_html__( 'Agent', 'cherry-real-estate' ), 'post_author_meta_box', $post_type_name, 'side', 'default' );
		}
	}

	/**
	 * Retrieve a photo url.
	 *
	 * @since  1.0.0
	 * @param  int    $agent_id Agent ID.
	 * @param  string $size     Valid image size.
	 * @return bool|string
	 */
	public static function get_agent_photo_url( $agent_id, $size = 'thumbnail' ) {

		// Skip unregistered user.
		if ( 0 == $agent_id ) {
			return false;
		}

		$photo_id = self::get_agent_photo_id( $agent_id );

		if ( ! empty( $photo_id ) ) {
			$image     = wp_get_attachment_image_src( $photo_id, $size );
			$photo_url = $image[0];
		} else {
			$avatar_size = Cherry_RE_Tools::get_image_size( $size );

			if ( ! $avatar_size ) {
				return false;
			}

			if ( ! isset( $avatar_size['width'] ) ) {
				return false;
			}

			$photo_url = get_avatar_url( $agent_id, array(
				'size' => $avatar_size['width'],
			) );
		}

		return $photo_url;
	}

	/**
	 * Retrieve a photo ID.
	 *
	 * @since  1.0.0
	 * @param  int $agent_id Agent ID.
	 * @return int
	 */
	public static function get_agent_photo_id( $agent_id ) {
		$prefix = cherry_real_estate()->get_meta_prefix();

		return (int) get_the_author_meta( $prefix . 'agent_photo', $agent_id );
	}

	/**
	 * Retrieve a custom agent contacts.
	 *
	 * @since  1.0.0
	 * @param  bool $agent_id Agent ID.
	 * @return mixed
	 */
	public static function get_agent_contacts( $agent_id ) {
		$prefix = cherry_real_estate()->get_meta_prefix();

		return get_the_author_meta( $prefix . 'agent_contacts', $agent_id );
	}

	/**
	 * Retrieve agent socials contacts.
	 *
	 * @since  1.0.0
	 * @param  bool $agent_id Agent ID.
	 * @return mixed
	 */
	public static function get_agent_socials( $agent_id ) {
		$prefix = cherry_real_estate()->get_meta_prefix();

		return get_the_author_meta( $prefix . 'agent_socials', $agent_id );
	}

	/**
	 * Retrieve agent trust value.
	 *
	 * @since  1.0.0
	 * @param  bool $agent_id Agent ID.
	 * @return mixed
	 */
	public static function get_agent_trust( $agent_id ) {
		$prefix = cherry_real_estate()->get_meta_prefix();

		return get_the_author_meta( $prefix . 'agent_trust', $agent_id );
	}

	/**
	 * Retrieve a CSS-id for properties list wrapper in agent archive template.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_property_wrap_id() {
		return apply_filters( 'cherry_re_agent_archive_property_wrap_id', 'tm-agent-property-instance' );
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

Model_Agents::get_instance();
