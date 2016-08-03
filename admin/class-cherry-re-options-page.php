<?php
/**
 * Real Estate options page.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Admin
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

class Cherry_RE_Options_Page {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Plugin options.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $options = array();

	/**
	 * Options page slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $page_slug = 'cherry-re-options';

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __construct() {
		add_action( 'cherry_re_modules_ready', array( $this, 'create_page' ) );
		add_action( 'cherry_re_plugin_activation', array( $this, 'create_defaults' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Add admin menu.
	 *
	 * @since 1.0.0
	 */
	public function create_page() {
		cherry_real_estate()->get_core()->modules['cherry-page-builder']->make(
			$this->get_page_slug(),
			esc_html__( 'Settings', 'cherry-real-estate' ),
			'edit.php?post_type=' . cherry_real_estate()->get_post_type_name()
		)->set(
			array(
				'capability' => 'manage_options',
				'sections'   => $this->get_sections(),
				'settings'   => $this->get_options(),
			)
		);
	}

	/**
	 * Retrieve a sections.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_sections() {
		return apply_filters( 'cherry_re_plugin_sections', array(
			'cherry-re-options-main' => array(
				'slug' => 'cherry-re-options-main',
				'name' => esc_html__( 'Main', 'cherry-real-estate' ),
			),
			'cherry-re-options-map' => array(
				'slug' => 'cherry-re-options-map',
				'name' => esc_html__( 'Map', 'cherry-real-estate' ),
			),
			'cherry-re-options-confirn-email' => array(
				'slug' => 'cherry-re-options-confirn-email',
				'name' => esc_html__( 'Confirmation E-mail', 'cherry-real-estate' ),
			),
		) );
	}

	/**
	 * Retrieve options.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_options() {
		return apply_filters( 'cherry_re_plugin_options', array(
			'cherry-re-options-main' => array(
				'area-unit' => array(
					'slug'  => 'area-unit',
					'type'  => 'select',
					'title' => esc_html__( 'Area unit', 'cherry-real-estate' ),
					'field' => array(
						'id'      => 'area-unit',
						'size'    => 1,
						'value'   => 'feets',
						'options' => Model_Settings::get_area_unit(),
					),
				),
				'сurrency-sign' => array(
					'slug'  => 'сurrency-sign',
					'type'  => 'text',
					'title' => esc_html__( 'Currency', 'cherry-real-estate' ),
					'field' => array(
						'id'    => 'сurrency-sign',
						'value' => '$',
					),
				),
				'сurrency-position' => array(
					'slug'  => 'сurrency-position',
					'type'  => 'select',
					'title' => esc_html__( 'Currency Position', 'cherry-real-estate' ),
					'field' => array(
						'id'      => 'сurrency-position',
						'size'    => 1,
						'value'   => 'left',
						'options' => array(
							'left'             => esc_html__( 'Left', 'cherry-real-estate' ),
							'right'            => esc_html__( 'Right', 'cherry-real-estate' ),
							'left-with-space'  => esc_html__( 'Left with space', 'cherry-real-estate' ),
							'right-with-space' => esc_html__( 'Right with space', 'cherry-real-estate' ),
						),
					),
				),
				'thousand-sep' => array(
					'slug'  => 'thousand-sep',
					'type'  => 'text',
					'title' => esc_html__( 'Thousand Separator', 'cherry-real-estate' ),
					'field' => array(
						'id'    => 'thousand-sep',
						'value' => ',',
					),
				),
				'decimal-sep' => array(
					'slug'  => 'decimal-sep',
					'type'  => 'text',
					'title' => esc_html__( 'Decimal Separator', 'cherry-real-estate' ),
					'field' => array(
						'id'    => 'decimal-sep',
						'value' => '.',
					),
				),
				'decimal-numb' => array(
					'slug'  => 'decimal-numb',
					'type'  => 'text',
					'title' => esc_html__( 'Number of Decimals', 'cherry-real-estate' ),
					'field' => array(
						'id'    => 'decimal-numb',
						'value' => '2',
					),
				),
			),
			'cherry-re-options-map' => array(
				'api_key' => array(
					'slug'  => 'api_key',
					'title' => esc_html__( 'Api Key', 'cherry-real-estate' ),
					'type'  => 'text',
					'field' => array(
						'id'    => 'api_key',
						'value' => '',
					),
				),
				'style' => array(
					'slug'  => 'style',
					'title' => esc_html__( 'Style', 'cherry-real-estate' ),
					'type'  => 'textarea',
					'field' => array(
						'id'    => 'style',
						'value' => '',
					),
				),
				'marker' => array(
					'slug'  => 'marker',
					'title' => esc_html__( 'Marker', 'cherry-real-estate' ),
					'type'  => 'media',
					'field' => array(
						'id'                 => 'marker',
						'value'              => '',
						'multi_upload'       => false,
						'upload_button_text' => esc_html__( 'Upload', 'cherry-real-estate' ),
					),
				),
			),
			'cherry-re-options-confirn-email' => array(
				'subject' => array(
					'slug'  => 'subject',
					'title' => esc_html__( 'Subject', 'cherry-real-estate' ),
					'type'  => 'text',
					'field' => array(
						'id'    => 'subject',
						'value' => esc_html__( 'Confirmation email', 'cherry-real-estate' ),
					),
				),
				'message' => array(
					'slug'  => 'message',
					'title' => esc_html__( 'Message', 'cherry-real-estate' ),
					'type'  => 'textarea',
					'field' => array(
						'id'    => 'message',
						'value' => esc_html__( 'Hello. You submit new property. Please, to confirm your ads go to the link ', 'cherry-real-estate' ),
					),
				),
			),
		) );
	}

	/**
	 * Store default options into database on plugin activation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function create_defaults() {
		$options = $this->get_options();

		if ( empty( $options ) ) {
			return;
		}

		foreach ( (array) $options as $key => $option ) {

			if ( get_option( $key, false ) ) {
				continue;
			}

			$values = array();

			foreach ( $option as $k => $v ) {
				if ( ! isset( $v['field']['value'] ) ) {
					$values[ $k ] = '';
				} else {
					$values[ $k ] = $v['field']['value'];
				}
			}

			update_option( $key, $values );
		}
	}

	/**
	 * Enqueue settings page style.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_styles( $hook_suffix ) {

		if ( false === strpos( $hook_suffix, $this->get_page_slug() ) ) {
			return null;
		}

		wp_enqueue_style( 'cherry-re-settings-page' );
	}

	/**
	 * Check if is options page.
	 *
	 * @since 1.0.0
	 * @return boolean
	 */
	public function is_options_page() {
		return ( ! empty( $_GET['page'] ) && $this->get_page_slug() === $_GET['page'] );
	}

	/**
	 * Retrieve a page options slug.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_page_slug() {
		return $this->page_slug;
	}

	/**
	 * Returns the instance.
	 *
	 * @since 1.0.0
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

Cherry_RE_Options_Page::get_instance();
