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

	private function __construct() {
		add_action( 'show_user_profile', array( $this, 'add_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'add_profile_fields' ) );

		add_action( 'personal_options_update', array( $this, 'save_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_profile_fields' ) );

		// Add Properties to Author Archives Page.
		add_action( 'pre_get_posts', array( $this, 'author_archive' ) );

		add_filter( 'option_show_avatars', array( $this, 'hide_avatar_option' ), 10, 2 );
	}

	/**
	 * Add a custom profile fields to the profile page.
	 *
	 * @since 1.0.0
	 * @param WP_User $user The current WP_User object.
	 */
	public function add_profile_fields( $user ) {
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
						'label'       => esc_html__( 'Label', 'cherry-real-estate'  ),
					),
					'value' => array(
						'type'        => 'text',
						'id'          => 'value',
						'name'        => 'value',
						'placeholder' => esc_html__( 'URL', 'cherry-real-estate' ),
						'label'       => esc_html__( 'URL', 'cherry-real-estate'  ),
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
	 * @param  object $user
	 * @return string
	 */
	public function _add_photo( $user ) {

		if ( ! current_user_can( 'upload_files' ) ) {
			return false;
		}

		$prefix   = cherry_real_estate()->get_meta_prefix();
		$photo_id = self::get_agent_photo_id( $user->ID );

		if ( ! $photo_id ) {
			$btn_text = esc_html__( 'Upload Photo', 'cherry-real-estate' );
		} else {
			$btn_text = esc_html__( 'Change Photo', 'cherry-real-estate' );
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
	 * Save agent contacts.
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID.
	 */
	public function _save_contacts( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$prefix = cherry_real_estate()->get_meta_prefix();

		update_user_meta(
			$user_id,
			$prefix . 'agent_contacts',
			$_POST[ $prefix . 'agent_contacts' ]
		);

		update_user_meta(
			$user_id,
			$prefix . 'agent_socials',
			$_POST[ $prefix . 'agent_socials' ]
		);
	}

	/**
	 * Save agent photo.
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID.
	 */
	public static function _save_photo( $user_id ) {

		if ( ! current_user_can( 'upload_files', $user_id ) ) {
			return false;
		}

		$prefix = cherry_real_estate()->get_meta_prefix();

		update_user_meta(
			$user_id,
			$prefix . 'agent_photo',
			sanitize_text_field( $_POST[ $prefix . 'agent_photo' ] )
		);
	}

	/**
	 * Hide a avatar option in profile page.
	 *
	 * @since  1.0.0
	 * @param  mixed  $value  Value of the option.
	 * @param  string $option Option name.
	 * @return mixed
	 */
	public function hide_avatar_option( $value, $option ) {
		$pre = apply_filters( 'cherry_re_hide_avatar_option', true, $value, $option );

		if ( true !== $pre ) {
			return $value;
		}

		if ( ! defined( 'IS_PROFILE_PAGE' ) ) {
			return $value;
		}

		if ( IS_PROFILE_PAGE ) {
			return false;
		}

		return $value;
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
	 * @param  array $classes CSS-classes
	 * @return array
	 */
	public function body_class( $classes ) {
		$classes[] = 'tm-agent';

		return $classes;
	}

	/**
	 * Retrieve a photo url.
	 *
	 * @since  1.0.0
	 * @param  int    Agent ID.
	 * @return string
	 */
	public static function get_agent_photo_url( $agent_id, $size = 'thumbnail' ) {
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
	 * @param  int Agent ID.
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
	 * @param  bool  $agent_id Agent ID.
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
	 * @param  bool  $agent_id Agent ID.
	 * @return mixed
	 */
	public static function get_agent_socials( $agent_id ) {
		$prefix = cherry_real_estate()->get_meta_prefix();

		return get_the_author_meta( $prefix . 'agent_socials', $agent_id );
	}

	/**
	 * Get all agents.
	 *
	 * @since  1.0.0
	 * @return array all agents.
	 */
	public static function get_list() {
		$agents = get_users( array(
			'role__in' => apply_filters( 'cherry_re_get_agent_list_args', array( 'administrator', 're_agent' ) ),
		) );

		$result = array();

		if ( is_array( $agents ) ) {
			foreach ( $agents as $agent ) {
				$result[ $agent->data->ID ] = $agent->data->display_name;
			}
		}

		return $result;
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
