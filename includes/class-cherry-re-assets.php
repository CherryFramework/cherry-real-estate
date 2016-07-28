<?php
/**
 * Managing assets.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Class for managing plugin assets.
 *
 * @since 1.0.0
 */
class Cherry_RE_Assets {

	/**
	 * Set of queried assets.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var array
	 */
	public static $js_handles = array();

	/**
	 * Handle for Google Map API javascript.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public static $googleapis_handle = 'google-maps-js-api';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// JS.
		add_action( 'wp_enqueue_scripts',    array( __CLASS__, 'register_public_scripts' ), 1 );
		add_action( 'wp_footer',             array( __CLASS__, 'enqueue_public_scripts' ) );
		// add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_scripts' ) );

		// CSS.
		add_action( 'wp_enqueue_scripts',    array( __CLASS__, 'register_public_styles' ), 1 );
		add_action( 'wp_enqueue_scripts',    array( __CLASS__, 'enqueue_public_styles' ), 9 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_styles' ) );

		// Google Map API - fix conflict.
		add_action( 'wp_footer', array( __CLASS__, 'googleapis_conflict' ), 11 );
	}

	/**
	 * Register the javascripts for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public static function register_public_scripts() {
		wp_register_script(
			self::get_googleapis_handle(),
			esc_url( Cherry_RE_Tools::get_google_map_url() ),
			array(),
			false,
			true
		);

		wp_register_script(
			'cherry-re-locations',
			plugins_url( 'assets/js/locations.min.js', CHERRY_REAL_ESTATE_MAIN_FILE ),
			array( 'jquery' ),
			CHERRY_REAL_ESTATE_VERSION,
			true
		);

		wp_register_script(
			'jquery-swiper',
			plugins_url( 'assets/js/swiper/swiper.jquery.min.js', CHERRY_REAL_ESTATE_MAIN_FILE ),
			array( 'jquery' ),
			'3.3.1',
			true
		);

		wp_register_script(
			'cherry-re-script',
			plugins_url( 'assets/js/real-estate.min.js', CHERRY_REAL_ESTATE_MAIN_FILE ),
			array( 'cherry-js-core' ),
			CHERRY_REAL_ESTATE_VERSION,
			true
		);

		/**
		 * Hook to deregister the javascripts or add custom.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cherry_re_register_public_scripts' );
	}

	/**
	 * Enqueue the javascripts for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public static function enqueue_public_scripts() {
		$assets = self::get_js_handles();

		// Enqueue the javascript.
		foreach ( $assets as $script ) {
			wp_enqueue_script( $script );
		}

		/**
		 * Hook to dequeue the javascripts or add custom.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cherry_re_enqueue_public_scripts' );
	}

	/**
	 * Enqueue the javascripts for the admin-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public static function enqueue_admin_scripts( $hook_suffix ) {
		/**
		 * Hook to dequeue the javascripts or add custom.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cherry_re_enqueue_admin_scripts' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public static function register_public_styles() {
		wp_register_style(
			'jquery-swiper',
			plugins_url( 'assets/css/swiper.css', CHERRY_REAL_ESTATE_MAIN_FILE ),
			array(),
			'1.1.5',
			'all'
		);

		wp_register_style(
			'cherry-re-style',
			plugins_url( 'assets/css/public.css', CHERRY_REAL_ESTATE_MAIN_FILE ),
			array(),
			CHERRY_REAL_ESTATE_VERSION,
			'all'
		);

		/**
		 * Hook to deregister stylesheets or add custom.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cherry_re_register_public_styles' );
	}

	/**
	 * Enqueue the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public static function enqueue_public_styles() {
		wp_enqueue_style( 'jquery-swiper' );
		wp_enqueue_style( 'cherry-re-style' );

		/**
		 * Hook to dequeue the stylesheets or add custom.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cherry_re_enqueue_public_styles' );
	}

	/**
	 * Enqueue the stylesheets for the admin-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public static function enqueue_admin_styles( $hook_suffix ) {

		if ( in_array( $hook_suffix, array( 'user-edit.php', 'profile.php' ) ) ) {
			wp_enqueue_style(
				'cherry-re-agent-styles',
				plugins_url( 'admin/assets/css/admin-style.css', CHERRY_REAL_ESTATE_MAIN_FILE ),
				array(),
				CHERRY_REAL_ESTATE_VERSION,
				'all'
			);
		}

		wp_register_style(
			'cherry-re-settings-page',
			plugins_url( 'admin/assets/css/settings-page.css', CHERRY_REAL_ESTATE_MAIN_FILE ),
			array(),
			CHERRY_REAL_ESTATE_VERSION,
			'all'
		);

		/**
		 * Hook to dequeue the stylesheets or add custom.
		 *
		 * @since 1.0.0
		 */
		do_action( 'cherry_re_enqueue_admin_styles' );
	}

	/**
	 * Add asset to the query.
	 *
	 * @since 1.0.0
	 * @param mixed $handle Asset handle or array with handles.
	 */
	public static function add( $handle ) {

		if ( is_array( $handle ) ) {
			foreach ( $handle as $h ) {
				self::$js_handles[ $h ] = $h;
			}
		} else {
			self::$js_handles[ $handle ] = $handle;
		}
	}

	/**
	 * Get the queried javascripts.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_js_handles() {
		$handles = self::$js_handles;

		return array_unique( (array) apply_filters( 'cherry_re_get_js_handles', (array) array_unique( $handles ) ) );
	}

	/**
	 * Solution for including a Google Map API javascript.
	 *
	 * @since 1.0.0
	 */
	public static function googleapis_conflict() {
		$remove_fix = apply_filters( 'cherry_re_remove_googleapis_conflict', false );

		if ( false !== $remove_fix ) {
			return;
		}

		global $wp_scripts;

		foreach( $wp_scripts->registered as $r ) {

			if ( $r->handle == self::get_googleapis_handle() ) {
				continue;
			}

			if ( preg_match( '/maps.google.com/i', $r->src ) || preg_match( '/maps.googleapis.com/i', $r->src ) ) {

				if ( in_array( $r->handle, $wp_scripts->done ) ) {
					wp_dequeue_script( self::get_googleapis_handle() );
				}

				if ( in_array( $r->handle, $wp_scripts->queue ) ) {
					wp_dequeue_script( $r->handle );
					wp_enqueue_script( self::get_googleapis_handle() );
				}
			}
		}
	}

	/**
	 * Retrieve a Google Map API handle.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_googleapis_handle() {
		return apply_filters( 'cherry_re_get_googleapis_handle', self::$googleapis_handle );
	}
}

new Cherry_RE_Assets;

/**
 * Helper function to add javascript to the query.
 *
 * @since 1.0.0
 * @param mixed $handle JavaScript handle or array with handles.
 */
function cherry_re_enqueue_script( $handle ) {
	Cherry_RE_Assets::add( $handle );
}
