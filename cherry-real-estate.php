<?php
/**
 * Plugin Name: Cherry Real Estate
 * Plugin URI:  http://www.templatemonster.com/
 * Description: Plugin for adding real estate functionality to the site.
 * Version:     1.0.0
 * Author:      Template Monster
 * Author URI:  http://www.templatemonster.com/
 * Text Domain: cherry-real-estate
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 *
 * @package   Cherry_Real_Estate
 * @author    Template Monster
 * @license   GPL-3.0+
 * @copyright 2002-2016, Template Monster
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// If class `Cherry_Real_Estate` not exists.
if ( ! class_exists( 'Cherry_Real_Estate' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	class Cherry_Real_Estate {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * A reference to an instance of cherry framework core class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private $core = null;

		/**
		 * Default options.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private static $default_options = array();

		/**
		 * The post type name.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		private $post_type_name = 'tm-property';

		/**
		 * The prefix for metadata.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		private $meta_prefix = '_tm_property_';

		/**
		 * The prefix for shortcodes.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		private $shortcode_prefix = 'tm_re_';

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {

			// Set the constants needed by the plugin.
			$this->constants();

			// Load all required files.
			$this->includes();

			// Set up a Cherry core.
			add_action( 'after_setup_theme', require( trailingslashit( __DIR__ ) . 'cherry-framework/setup.php' ), 0 );
			add_action( 'after_setup_theme', array( $this, 'get_core' ), 1 );
			add_action( 'after_setup_theme', array( 'Cherry_Core', 'load_all_modules' ), 2 );

			// Initialization of Cherry's modules.
			add_action( 'after_setup_theme', array( $this, 'launch' ), 10 );

			// Pluggable functions by plugins and themes.
			add_action( 'after_setup_theme', array( $this, 'template_functions' ), 11 );

			// Shortcodes.
			add_action( 'init', array( $this, 'register_shortcodes' ) );

			// TinyMCE.
			add_action( 'admin_init', array( 'Cherry_RE_Shortcodes_Data', 'add_buttons' ) );

			// Title on search properties page.
			add_filter( 'document_title_parts', array( $this, 'search_title' ), 11 );

			add_filter( 'cherry_breadcrumbs_items', array( $this, 'search_breadcrumbs' ), 11, 2 );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * Defines constants for the plugin.
		 *
		 * @since 1.0.0
		 */
		public function constants() {

			/**
			 * Set constant path to the plugin directory.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_REAL_ESTATE_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			/**
			 * Set constant path to the main file.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_REAL_ESTATE_MAIN_FILE', __FILE__ );

			/**
			 * Set constant path to the plugin URI.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_REAL_ESTATE_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

			/**
			 * Set the version number of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_REAL_ESTATE_VERSION', '1.0.0' );
		}

		/**
		 * Loads the core functions. These files are needed before loading anything else in the
		 * theme because they have required functions for use.
		 *
		 * @since 1.0.0
		 */
		public function get_core() {
			/**
			 * Fires before loads the core.
			 *
			 * @since 1.0.0
			 */
			do_action( 'cherry_re_core_before' );

			global $chery_core_version;

			if ( null !== $this->core ) {
				return $this->core;
			}

			if ( 0 < sizeof( $chery_core_version ) ) {
				$core_paths = array_values( $chery_core_version );

				require_once( $core_paths[0] );
			} else {
				die( 'Class Cherry_Core not found' );
			}

			$this->core = new Cherry_Core( array(
				'base_dir' => CHERRY_REAL_ESTATE_DIR . 'cherry-framework',
				'base_url' => CHERRY_REAL_ESTATE_URI . 'cherry-framework',
				'modules'  => array(
					'cherry-js-core' => array(
						'autoload' => false,
					),
					'cherry-utility' => array(
						'autoload' => false,
					),
					'cherry-page-builder' => array(
						'autoload' => false,
					),
					'cherry-ui-elements' => array(
						'autoload' => false,
					),
					'cherry-post-meta' => array(
						'autoload' => false,
					),
					'cherry-widget-factory' => array(
						'autoload' => false,
					),
				),
			) );

			return $this->core;
		}

		/**
		 * Run initialization of modules.
		 *
		 * @since 1.0.0
		 */
		public function launch() {
			$prefix = $this->get_meta_prefix();

			$this->get_core()->init_module( 'cherry-js-core' );
			$this->get_core()->init_module( 'cherry-utility' );
			$this->get_core()->init_module( 'cherry-page-builder' );
			$this->get_core()->init_module( 'cherry-ui-elements', array(
				'ui_elements' => array(
					'text',
					'textarea',
					'select',
					'media',
					'stepper',
					'checkbox',
					'switcher',
					'repeater',
					'iconpicker',
				),
			) );
			$this->get_core()->init_module( 'cherry-post-meta', array(
				'title'  => esc_html__( 'Property Data', 'cherry-real-estate' ),
				'page'   => array( $this->get_post_type_name() ),
				'fields' => array(
					$prefix . 'state' => array(
						'type'       => 'select',
						'id'         => $prefix . 'state',
						'name'       => $prefix . 'state',
						'value'      => 'active',
						'left_label' => esc_html__( 'State of progress', 'cherry-real-estate' ),
						'options'    => Model_Properties::get_allowed_property_states(),
					),
					$prefix . 'price' => array(
						'type'       => 'stepper',
						'id'         => $prefix . 'price',
						'name'       => $prefix . 'price',
						'max_value'  => 9999999999,
						'min_value'  => 0,
						'step_value' => 0.01,
						'value'      => 0,
						'left_label' => esc_html__( 'Price', 'cherry-real-estate' ),
					),
					$prefix . 'status' => array(
						'type'       => 'select',
						'id'         => $prefix . 'status',
						'name'       => $prefix . 'status',
						'value'      => 'rent',
						'left_label' => esc_html__( 'Property status', 'cherry-real-estate' ),
						'options'    => Model_Properties::get_allowed_property_statuses(),
					),
					$prefix . 'location' => array(
						'type'       => 'text',
						'id'         => $prefix . 'location',
						'name'       => $prefix . 'location',
						'value'      => '',
						'left_label' => esc_html__( 'Location', 'cherry-real-estate' ),
					),
					$prefix . 'bedrooms' => array(
						'type'       => 'stepper',
						'id'         => $prefix . 'bedrooms',
						'name'       => $prefix . 'bedrooms',
						'max_value'  => 99999,
						'min_value'  => 0,
						'step_value' => 1,
						'value'      => 0,
						'left_label' => esc_html__( 'Bedrooms', 'cherry-real-estate' ),
					),
					$prefix . 'bathrooms' => array(
						'type'       => 'stepper',
						'id'         => $prefix . 'bathrooms',
						'name'       => $prefix . 'bathrooms',
						'max_value'  => 99999,
						'min_value'  => 0,
						'step_value' => 1,
						'value'      => 0,
						'left_label' => esc_html__( 'Bathrooms', 'cherry-real-estate' ),
					),
					$prefix . 'area' => array(
						'type'       => 'stepper',
						'id'         => $prefix . 'area',
						'name'       => $prefix . 'area',
						'max_value'  => 999999,
						'min_value'  => 0,
						'step_value' => 0.01,
						'value'      => 0,
						'left_label' => esc_html__( 'Area', 'cherry-real-estate' ),
					),
					$prefix . 'parking_places' => array(
						'type'       => 'stepper',
						'id'         => $prefix . 'parking_places',
						'name'       => $prefix . 'parking_places',
						'max_value'  => 99999,
						'min_value'  => 0,
						'value'      => 0,
						'left_label' => esc_html__( 'Parking places', 'cherry-real-estate' ),
					),
					$prefix . 'gallery' => array(
						'type'         => 'media',
						'id'           => $prefix . 'gallery',
						'name'         => $prefix . 'gallery',
						'multi_upload' => true,
						'left_label'   => esc_html__( 'Gallery', 'cherry-real-estate' ),
					),
				),
			) );
			$this->get_core()->init_module( 'cherry-widget-factory' );

			/**
			 * Fire when all modules already loaded and ready for to use.
			 *
			 * @since 1.0.0
			 */
			do_action( 'cherry_re_modules_ready' );

			// Load widgets.
			$this->add_widgets();
		}

		/**
		 * Include required files used in admin and on the frontend.
		 *
		 * @since 1.0.0
		 */
		public function includes() {
			// Models.
			require_once( CHERRY_REAL_ESTATE_DIR . 'includes/models/model-properties.php' );
			require_once( CHERRY_REAL_ESTATE_DIR . 'includes/models/model-agents.php' );
			require_once( CHERRY_REAL_ESTATE_DIR . 'includes/models/model-settings.php' );

			// Classes.
			require_once( CHERRY_REAL_ESTATE_DIR . 'includes/class-cherry-re-registration.php' );
			include_once( CHERRY_REAL_ESTATE_DIR . 'includes/class-cherry-re-assets.php' );
			include_once( CHERRY_REAL_ESTATE_DIR . 'includes/class-cherry-re-agent-data.php' );
			include_once( CHERRY_REAL_ESTATE_DIR . 'includes/class-cherry-re-property-data.php' );
			include_once( CHERRY_REAL_ESTATE_DIR . 'includes/class-cherry-re-shortcodes-data.php' );
			include_once( CHERRY_REAL_ESTATE_DIR . 'includes/class-cherry-re-template-loader.php' );
			require_once( CHERRY_REAL_ESTATE_DIR . 'includes/class-cherry-re-tools.php' );

			// Functions.
			include_once( CHERRY_REAL_ESTATE_DIR . 'includes/core-functions.php' );

			// Frontend.
			if ( ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) ) {
				include_once( CHERRY_REAL_ESTATE_DIR . 'includes/template-hooks.php' );
				require_once( CHERRY_REAL_ESTATE_DIR . 'includes/class-cherry-re-template-callbacks.php' );
			}

			// Admin.
			if ( is_admin() ) {
				include_once( CHERRY_REAL_ESTATE_DIR . 'admin/class-cherry-re-options-page.php' );
			}
		}

		/**
		 * Function used to Init Template Functions.
		 *
		 * @since 1.0.0
		 */
		public function template_functions() {
			include_once( 'includes/template-functions.php' );
		}

		/**
		 * Register shortcodes.
		 *
		 * @since 1.0.0
		 */
		public function register_shortcodes() {
			$shortcoder = Cherry_RE_Shortcodes_Data::get_instance();

			foreach ( (array) Cherry_RE_Shortcodes_Data::shortcodes() as $id => $data ) {

				if ( isset( $data['function'] ) && is_callable( $data['function'] ) ) {
					$func = $data['function'];
				} elseif ( is_callable( array( $shortcoder, $id ) ) ) {
					$func = array( $shortcoder, $id );
				} else {
					continue;
				}

				// Register shortcode.
				add_shortcode( $this->get_shortcode_prefix() . $id, $func );
			}
		}

		/**
		 * Add `Search Form` widget.
		 *
		 * @since 1.0.0
		 */
		public function add_widgets() {

			if ( ! class_exists( 'Cherry_RE_Search_Widget' ) ) {
				require_once( CHERRY_REAL_ESTATE_DIR . '/widgets/class-cherry-re-search-widget.php' );

				add_action( 'widgets_init', create_function( '', 'register_widget("Cherry_RE_Search_Widget");' ) );
			}
		}

		/**
		 * Add `RE Agent` role.
		 *
		 * @since 1.0.0
		 */
		public function add_user_role() {

			// Define property capabilities.
			$capabilities = apply_filters( 'cherry_re_agent_capabilities', array(
				'create_properties' => true,
				'edit_properties'   => true,
				'manage_properties' => true,
				'upload_files'      => true,
				'edit_posts'        => true, // `save_post` action callback check `edit_posts` capability.
				'read'              => true,
			) );

			// Create `RE Agent` role and assign the capabilities to it.
			add_role(
				're_agent',
				esc_html__( 'RE Agent', 'cherry-real-estate' ),
				$capabilities
			);

			// Add property capabilities to Admin and Editor Roles.
			$roles = apply_filters( 'cherry_re_update_roles_list', array( 'administrator' ) );

			foreach ( (array) $roles as $name ) {
				$role = get_role( $name );

				if ( is_null( $role ) ) {
					continue;
				}

				foreach ( $capabilities as $capability => $enabled ) {
					if ( $enabled ) {
						$role->add_cap( $capability );
					}
				}
			}
		}

		/**
		 * Fired when the plugin is activated.
		 *
		 * @since 1.0.0
		 */
		public function activation() {
			/**
			 * Call CPT registration function.
			 *
			 * @link https://codex.wordpress.org/Function_Reference/flush_rewrite_rules#Examples
			 */
			Cherry_RE_Registration::register_post_type();
			Cherry_RE_Registration::register_taxonomies();

			$this->add_user_role();

			do_action( 'cherry_re_plugin_activation' );
		}

		/**
		 * Fired when the plugin is deactivated.
		 *
		 * @since 1.0.0
		 */
		public function deactivation() {
			do_action( 'cherry_re_plugin_deactivation' );
		}

		/**
		 * Uninstall
		 *
		 * @since 1.0.0
		 */
		public static function uninstall() {
			// Model_Settings::remove_all_settings();
		}

		/**
		 * Customize the title on search properties page.
		 *
		 * @since  1.0.0
		 * @param  string $title
		 * @return string
		 */
		public function search_title( $title ) {

			if ( cherry_re_is_property_search() ) {
				$title['title'] = esc_html__( 'Properties search', 'cherry-real-estate' );
			}

			return $title;
		}

		public function search_breadcrumbs( $items, $args ) {

			if ( cherry_re_is_property_search() ) {
				return array( $items[0], __( 'Properties search results', 'cherry-real-estate' ) );
			}

			return $items;
		}

		/**
		 * Get the template path.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function template_path( $dir = '' ) {
			$path = 'real-estate';

			if ( $dir && is_string( $dir ) ) {
				$path = trailingslashit( $path );
				$path .= ltrim( $dir, '/' );
			}

			return apply_filters( 'cherry_re_template_path', trailingslashit( $path ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_path( $dir = '' ) {
			$path = plugin_dir_path( __FILE__ );

			if ( $dir && is_string( $dir ) ) {
				$path = trailingslashit( $path );
				$path .= ltrim( $dir, '/' );
			}

			return apply_filters( 'cherry_re_plugin_path', trailingslashit( $path ) );
		}

		/**
		 * Retrieve a post type name.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_post_type_name() {
			return apply_filters( 'cherry_re_get_post_type_name', $this->post_type_name );
		}

		/**
		 * Retrieve a prefix for metadata.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_meta_prefix() {
			return apply_filters( 'cherry_re_get_meta_prefix', $this->meta_prefix );
		}

		/**
		 * Retrieve a prefix for shortcodes.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_shortcode_prefix() {
			return apply_filters( 'cherry_re_get_shortcode_prefix', $this->shortcode_prefix );
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
}

/**
 * Returns instance of main class.
 *
 * @since  1.0.0
 * @return Cherry_Real_Estate
 */
function cherry_real_estate() {
	return Cherry_Real_Estate::get_instance();
}

cherry_real_estate();
