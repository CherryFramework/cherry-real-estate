<?php
/**
 * Plugin Name: Cherry Real Estate
 * Plugin URI:  http://www.templatemonster.com/
 * Description: Plugin for adding real estate functionality to the site.
 * Version:     1.0.1
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
		 * @var object
		 */
		private static $instance = null;

		/**
		 * A reference to an instance of cherry framework core class.
		 *
		 * @since 1.0.0
		 * @var object
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
		 * @var string
		 */
		private $post_type_name = 'tm-property';

		/**
		 * The prefix for metadata.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $meta_prefix = '_tm_property_';

		/**
		 * The prefix for shortcodes.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $shortcode_prefix = 'tm_re_';

		/**
		 * Constructor method.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {}

		/**
		 * Sets up initial actions.
		 *
		 * @since 1.0.0
		 */
		private function actions() {

			// Internationalize the text strings used.
			add_action( 'plugins_loaded', array( $this, 'i18n' ) );

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

			// Breacrumbs on search properties page.
			add_filter( 'cherry_breadcrumbs_items', array( $this, 'search_breadcrumbs' ), 11, 2 );

			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'settings_link' ), 10, 4 );

			// Enable use shortcodes in text widget.
			add_filter( 'widget_text', 'do_shortcode', 11 );

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
			 * Set constant path to the main file.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_REAL_ESTATE_MAIN_FILE', __FILE__ );

			/**
			 * Set constant path to the plugin directory.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_REAL_ESTATE_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			/**
			 * Set constant path to the plugin URI.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_REAL_ESTATE_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

			/**
			 * Set the slug of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_REAL_ESTATE_SLUG', basename( dirname( __FILE__ ) ) );

			/**
			 * Set the version number of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'CHERRY_REAL_ESTATE_VERSION', '1.0.1' );
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
			require_once( CHERRY_REAL_ESTATE_DIR . 'includes/models/model-submit-form.php' );
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
				include_once( CHERRY_REAL_ESTATE_DIR . 'admin/class-meta-box-authors.php' );
			}
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
			$this->get_core()->init_module( 'cherry-post-meta', array(
				'title'  => esc_html__( 'Property Data', 'cherry-real-estate' ),
				'page'   => array( $this->get_post_type_name() ),
				'fields' => array(
					$prefix . 'state' => array(
						'type'       => 'select',
						'id'         => $prefix . 'state',
						'name'       => $prefix . 'state',
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
						'left_label' => esc_html__( 'Price', 'cherry-real-estate' ),
					),
					$prefix . 'status' => array(
						'type'       => 'select',
						'id'         => $prefix . 'status',
						'name'       => $prefix . 'status',
						'left_label' => esc_html__( 'Property status', 'cherry-real-estate' ),
						'options'    => Model_Properties::get_allowed_property_statuses(),
					),
					$prefix . 'location' => array(
						'type'       => 'text',
						'id'         => $prefix . 'location',
						'name'       => $prefix . 'location',
						'left_label' => esc_html__( 'Location', 'cherry-real-estate' ),
					),
					$prefix . 'bedrooms' => array(
						'type'       => 'stepper',
						'id'         => $prefix . 'bedrooms',
						'name'       => $prefix . 'bedrooms',
						'max_value'  => 99999,
						'min_value'  => 0,
						'step_value' => 1,
						'left_label' => esc_html__( 'Bedrooms', 'cherry-real-estate' ),
					),
					$prefix . 'bathrooms' => array(
						'type'       => 'stepper',
						'id'         => $prefix . 'bathrooms',
						'name'       => $prefix . 'bathrooms',
						'max_value'  => 99999,
						'min_value'  => 0,
						'step_value' => 1,
						'left_label' => esc_html__( 'Bathrooms', 'cherry-real-estate' ),
					),
					$prefix . 'area' => array(
						'type'       => 'stepper',
						'id'         => $prefix . 'area',
						'name'       => $prefix . 'area',
						'max_value'  => 999999,
						'min_value'  => 0,
						'step_value' => 0.01,
						'left_label' => esc_html__( 'Area', 'cherry-real-estate' ),
					),
					$prefix . 'parking_places' => array(
						'type'       => 'stepper',
						'id'         => $prefix . 'parking_places',
						'name'       => $prefix . 'parking_places',
						'max_value'  => 99999,
						'min_value'  => 0,
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

			if ( is_admin() ) {
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
			}

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
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 */
		public function i18n() {
			load_plugin_textdomain( 'cherry-real-estate', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
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
		 * Add a plugin widgets.
		 *
		 * @since 1.0.0
		 */
		public function add_widgets() {
			require_once( CHERRY_REAL_ESTATE_DIR . '/widgets/class-cherry-re-search-widget.php' );
			require_once( CHERRY_REAL_ESTATE_DIR . '/widgets/class-cherry-re-properties-widget.php' );
		}

		/**
		 * Add `RE Agent` and `RE Contributor` roles.
		 *
		 * @since 1.0.0
		 */
		public function add_user_roles() {
			$capability_type = $this->get_post_type_name();

			// Define `RE Agent` capabilities.
			$cap_agent = $this->get_agent_caps();

			// Define `RE Contributor` capabilities.
			$cap_contributor = $this->get_contributor_caps();

			// Create `RE Agent` and `RE Contributor` roles.
			add_role( 're_agent', esc_html__( 'RE Agent', 'cherry-real-estate' ), $cap_agent );
			add_role( 're_contributor', esc_html__( 'RE Contributor', 'cherry-real-estate' ), $cap_contributor );
		}

		/**
		 * Remove `RE Agent` and `RE Contributor` roles.
		 *
		 * @since 1.0.0
		 */
		public function remove_user_roles() {
			remove_role( 're_agent' );
			remove_role( 're_contributor' );
		}

		/**
		 * Added a custom capabilities.
		 *
		 * @since 1.0.0
		 */
		public function update_caps() {
			$capability_type = $this->get_post_type_name();
			$cap_agent       = $this->get_agent_caps();

			// Update property capabilities to Admin Role.
			$roles        = apply_filters( 'cherry_re_update_roles_list', array( 'administrator' ) );
			$capabilities = wp_parse_args( $cap_agent, array(
				"delete_others_{$capability_type}s" => true,
				"edit_others_{$capability_type}s"   => true,
				"publish_{$capability_type}s"       => true,
				"read_private_{$capability_type}s"  => true,
				"read_{$capability_type}_type"      => true,
				"edit_{$capability_type}_type"      => true,
				"read_{$capability_type}_tag"       => true,
				"edit_{$capability_type}_tag"       => true,
				"read_{$capability_type}_feature"   => true,
				"edit_{$capability_type}_feature"   => true,
			) );
			$capabilities = apply_filters( 'cherry_re_custom_property_capabilities', $capabilities );

			foreach ( (array) $roles as $name ) {
				$role = get_role( $name );

				if ( is_null( $role ) ) {
					continue;
				}

				foreach ( $capabilities as $capability => $enabled ) {
					if ( ! $enabled ) {
						continue;
					}

					$role->add_cap( $capability );
				}
			}
		}

		/**
		 * Retrieve a `RE Agent` capabilities.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_agent_caps() {
			$capability_type = $this->get_post_type_name();

			return apply_filters( 'cherry_re_agent_capabilities', array(
				"delete_private_{$capability_type}s"   => true,
				"delete_published_{$capability_type}s" => true,
				"delete_{$capability_type}s"           => true,
				"edit_private_{$capability_type}s"     => true,
				"edit_published_{$capability_type}s"   => true,
				"edit_{$capability_type}s"             => true,
				"read_{$capability_type}_type"        => true,
				"edit_{$capability_type}_type"        => true,
				"read_{$capability_type}_tag"         => true,
				"edit_{$capability_type}_tag"         => true,
				"read_{$capability_type}_feature"     => true,
				"edit_{$capability_type}_feature"     => true,
				'edit_posts'                           => true, // `save_post` action-callback check `edit_posts` capability.
				'read'                                 => true,
				'upload_files'                         => true,
			) );
		}

		/**
		 * Retrieve a `RE Contributor` capabilities.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		public function get_contributor_caps() {
			$capability_type = $this->get_post_type_name();

			return apply_filters( 'cherry_re_contributor_capabilities', array(
				"edit_published_{$capability_type}s" => true,
				"edit_{$capability_type}s"           => true,
				'edit_posts'                         => true, // `save_post` action-callback check `edit_posts` capability.
				'read'                               => true,
				'upload_files'                       => true,
				"read_{$capability_type}_type"        => true,
				"read_{$capability_type}_tag"         => true,
				"read_{$capability_type}_feature"     => true,
			) );
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

			$this->add_user_roles();
			$this->update_caps();

			do_action( 'cherry_re_plugin_activation' );
		}

		/**
		 * Fired when the plugin is deactivated.
		 *
		 * @since 1.0.0
		 */
		public function deactivation() {
			$this->remove_user_roles();

			do_action( 'cherry_re_plugin_deactivation' );
		}

		/**
		 * Fired when the plugin is uninstall.
		 *
		 * @since 1.0.0
		 */
		public static function uninstall() {
			Model_Settings::remove_all_settings();
		}

		/**
		 * Customize the title on search properties page.
		 *
		 * @since  1.0.0
		 * @param  array $title The document title parts.
		 * @return string
		 */
		public function search_title( $title ) {

			if ( cherry_re_is_property_search() ) {
				$title['title'] = esc_html__( 'Properties search', 'cherry-real-estate' );
			}

			return $title;
		}

		/**
		 * Customize the breadcrumbs on search properties page.
		 *
		 * @since  1.0.0
		 * @param  array $items Indexed array of breadcrumb trail items.
		 * @param  array $args  Breadcrumb arguments.
		 * @return array
		 */
		public function search_breadcrumbs( $items, $args ) {

			if ( cherry_re_is_property_search() ) {

				$defaults = array(
					'css_namespace' => array(
						'item'   => 'breadcrumbs__item',
						'target' => 'breadcrumbs__item-target',
					),
				);

				$args = wp_parse_args( $args, $defaults );

				return array(
					$items[0],
					sprintf(
						'<div class="%s"><span class="%s">%s</span></div>',
						esc_attr( $args['css_namespace']['item'] ),
						esc_attr( $args['css_namespace']['target'] ),
						esc_html__( 'Properties search results', 'cherry-real-estate' )
					),
				);
			}

			return $items;
		}

		/**
		 * Added a link to the plugin settings page.
		 *
		 * @since  1.0.0
		 * @param  array  $actions     An array of plugin action links.
		 * @param  string $plugin_file Path to the plugin file relative to the plugins directory.
		 * @param  array  $plugin_data An array of plugin data.
		 * @param  string $context     The plugin context.
		 * @return array
		 */
		public function settings_link( $actions, $plugin_file, $plugin_data, $context ) {
			$options_page = Cherry_RE_Options_Page::get_instance();

			$path = sprintf(
				'edit.php?post_type=%s&page=%s">',
				$this->get_post_type_name(),
				$options_page->get_page_slug()
			);

			$actions[] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( get_admin_url( null, $path ) ),
				esc_html__( 'Settings', 'cherry-real-esatate' )
			);

			return $actions;
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
		 * @since  1.0.0
		 * @param  string $dir Path inside plugin dir.
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
				self::$instance->constants();
				self::$instance->includes();
				self::$instance->actions();
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
