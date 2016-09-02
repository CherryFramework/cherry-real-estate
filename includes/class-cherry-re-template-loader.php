<?php
/**
 * Templater.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Templater class.
 *
 * @since 1.0.0
 */
class Cherry_RE_Templater {

	/**
	 * Templater macros regular expression.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $macros_regex = '/%%.+?%%/';

	/**
	 * Templates data to replace.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $replace_data = array();

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Specific CSS-classes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	public static $classes = array();

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'template_loader' ) );
		add_filter( 'body_class',       array( __CLASS__, 'add_body_class' ) );

		// Set `posts_per_page` for archive index page.
		add_action( 'pre_get_posts', array( $this, 'set_posts_per_archive_page' ) );
	}

	/**
	 * Load a template.
	 *
	 * @param string $template The path of the template to include.
	 * @return string
	 */
	public function template_loader( $template ) {
		$post_type = cherry_real_estate()->get_post_type_name();
		$find      = array();
		$file      = '';

		if ( is_singular( $post_type ) ) {

			$file   = 'single-property.php';
			$find[] = $file;
			$find[] = cherry_real_estate()->template_path() . $file;

			// CSS class.
			self::$classes[] = 'tm-property--single';

		} elseif ( cherry_re_is_property_search() ) {

			$file   = 'search-property.php';
			$find[] = $file;
			$find[] = cherry_real_estate()->template_path() . $file;
			$find[] = 'archive-property.php';
			$find[] = cherry_real_estate()->template_path() . 'archive-property.php';

			// CSS class.
			self::$classes[] = 'tm-property--search';

		} elseif ( cherry_re_is_property_taxonomy() ) {

			$term = get_queried_object();
			$file = 'archive-property.php';

			$find[] = 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';
			$find[] = cherry_real_estate()->template_path() . 'taxonomy-' . $term->taxonomy . '-' . $term->slug . '.php';

			$find[] = 'taxonomy-' . $term->taxonomy . '.php';
			$find[] = cherry_real_estate()->template_path() . 'taxonomy-' . $term->taxonomy . '.php';

			$find[] = 'taxonomy-property.php';
			$find[] = cherry_real_estate()->template_path() . 'taxonomy-property.php';

			$find[] = $file;
			$find[] = cherry_real_estate()->template_path() . $file;

			// CSS class.
			self::$classes[] = 'tm-property--taxonomy';

		} elseif ( cherry_re_is_property_listing() ) {

			$file   = 'archive-property.php';
			$find[] = $file;
			$find[] = cherry_real_estate()->template_path() . $file;

			// CSS class.
			self::$classes[] = 'tm-property--archive';

		} elseif ( cherry_re_is_agent() ) {

			$file = 're-agent.php';

			$find[] = $file;
			$find[] = cherry_real_estate()->template_path() . $file;

			$find[] = 'archive-property.php';
			$find[] = cherry_real_estate()->template_path() . 'archive-property.php';

			// CSS class.
			self::$classes[] = 'tm-property--agent';
		}

		if ( $file ) {
			$template = locate_template( array_unique( $find ) );

			if ( ! $template ) {
				$template = CHERRY_REAL_ESTATE_DIR . 'templates/' . $file;
			}
		}

		return $template;
	}

	/**
	 * Set `posts_per_page` for archive index page.
	 *
	 * @since 1.0.0
	 * @param object $query Main query.
	 */
	public function set_posts_per_archive_page( $query ) {
		$post_type = cherry_real_estate()->get_post_type_name();

		if ( ! is_admin()
			&& $query->is_main_query()
			&& ( $query->is_post_type_archive( $post_type ) || $query->is_tax( get_object_taxonomies( $post_type ) ) )
			) {

			$query->set( 'posts_per_page', Model_Settings::get_listing_per_page() );
		}
	}

	/**
	 * Returns macros regular expression.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function macros_regex() {
		return $this->macros_regex;
	}

	/**
	 * Prepare template data to replace.
	 *
	 * @since 1.0.0
	 * @param array $atts Output attributes.
	 */
	public function setup_template_data( $atts = array() ) {
		$callbacks = new Cherry_RE_Template_Callbacks( $atts );

		$data = array(
			'agent_photo'        => array( $callbacks, 'get_agent_photo' ),
			'agent_name'         => array( $callbacks, 'get_agent_name' ),
			'agent_desc'         => array( $callbacks, 'get_agent_description' ),
			'agent_contacts'     => array( $callbacks, 'get_agent_contacts' ),
			'agent_socials'      => array( $callbacks, 'get_agent_socials' ),
			'agent_more'         => array( $callbacks, 'get_agent_more' ),
			'property_title'     => array( $callbacks, 'get_property_title' ),
			'property_image'     => array( $callbacks, 'get_property_image' ),
			'property_status'    => array( $callbacks, 'get_property_status' ),
			'property_area'      => array( $callbacks, 'get_property_area' ),
			'property_bedrooms'  => array( $callbacks, 'get_property_bedrooms' ),
			'property_bathrooms' => array( $callbacks, 'get_property_bathrooms' ),
			'property_price'     => array( $callbacks, 'get_property_price' ),
			'property_excerpt'   => array( $callbacks, 'get_property_excerpt' ),
			'property_content'   => array( $callbacks, 'get_property_content' ),
			'property_location'  => array( $callbacks, 'get_property_location' ),
			'property_more'      => array( $callbacks, 'get_property_more' ),
		);

		/**
		 * Filters item data.
		 *
		 * @since 1.0.0
		 * @param array $data Item data.
		 * @param array $atts Attributes.
		 */
		$this->replace_data = apply_filters( 'cherry_re_data_callbacks', $data, $atts );

		return $callbacks;
	}

	/**
	 * Retrieve a replace data.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_replace_data() {
		return $this->replace_data;
	}

	/**
	 * Read template (static).
	 *
	 * @since 1.0.0
	 * @return bool|WP_Error|string - false on failure, stored text on success.
	 */
	public static function get_contents( $template ) {

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			include_once( ABSPATH . '/wp-admin/includes/file.php' );
		}

		WP_Filesystem();
		global $wp_filesystem;

		// Check for existence.
		if ( ! $wp_filesystem->exists( $template ) ) {
			return false;
		}

		// Read the file.
		$content = $wp_filesystem->get_contents( $template );

		if ( ! $content ) {
			// Return error object.
			return new WP_Error( 'reading_error', 'Error when reading file' );
		}

		return $content;
	}

	/**
	 * Retrieve a *.tmpl file content.
	 *
	 * @since  1.0.0
	 * @param  string $template  File name.
	 * @param  string $shortcode Shortcode name.
	 * @return string
	 */
	public function get_template_by_name( $template, $shortcode ) {
		$stylesheet_dir = trailingslashit( get_stylesheet_directory() );
		$default        = 'default.tmpl';
		$content        = '';

		$find   = array();
		$find[] = $stylesheet_dir . cherry_real_estate()->template_path( 'shortcodes/' . $shortcode ) . $template;
		$find[] = cherry_real_estate()->plugin_path( 'templates/shortcodes/' . $shortcode ) . $template;
		$find[] = $stylesheet_dir . cherry_real_estate()->template_path( 'shortcodes/' . $shortcode ) . $default;
		$find[] = cherry_real_estate()->plugin_path( 'templates/shortcodes/' . $shortcode ) . $default;

		foreach ( $find as $path ) {
			if ( file_exists( $path ) ) {
				$template = $path;
				break;
			}
		}

		if ( ! empty( $template ) ) {
			$content = self::get_contents( $template );
		}

		return $content;
	}

	/**
	 * Parse template content and replace macros with real data.
	 *
	 * @since  1.0.0
	 * @param  string $content Content to parse.
	 * @return string
	 */
	public function parse_template( $content ) {
		return preg_replace_callback( $this->macros_regex(), array( $this, 'replace_callback' ), $content );
	}

	/**
	 * Callback to replace macros with data.
	 *
	 * @since 1.0.0
	 * @param array $matches Founded macros.
	 */
	public function replace_callback( $matches ) {

		if ( ! is_array( $matches ) ) {
			return;
		}

		if ( empty( $matches ) ) {
			return;
		}

		$item   = trim( $matches[0], '%%' );
		$arr    = explode( ' ', $item, 2 );
		$macros = strtolower( $arr[0] );
		$attr   = isset( $arr[1] ) ? shortcode_parse_atts( $arr[1] ) : array();

		if ( ! isset( $this->replace_data[ $macros ] ) ) {
			return;
		}

		$callback = $this->replace_data[ $macros ];

		if ( ! is_callable( $callback ) || ! isset( $this->replace_data[ $macros ] ) ) {
			return;
		}

		if ( ! empty( $attr ) ) {

			// Call a WordPress function.
			return call_user_func( $callback, $attr );
		}

		return call_user_func( $callback );
	}

	/**
	 * Returns available agent templates list.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_agent_templates_list() {
		return apply_filters( 'cherry_re_agent_templates_list', array(
			'default.tmpl' => 'default.tmpl',
		) );
	}

	/**
	 * Returns available property templates list.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_property_templates_list() {
		return apply_filters( 'cherry_re_property_templates_list', array(
			'default.tmpl' => 'default.tmpl',
		) );
	}

	/**
	 * Get CSS-class name for shortcode by template name.
	 *
	 * @since  1.0.0
	 * @param  string $template template name.
	 * @return string|bool
	 */
	public function get_template_class( $template ) {

		if ( ! $template ) {
			return false;
		}

		$prefix = apply_filters( 'cherry_re_template_class_prefix', 'template' );
		$class  = sprintf( '%s-%s', esc_attr( $prefix ), esc_attr( str_replace( '.tmpl', '', $template ) ) );

		return $class;
	}

	/**
	 * Added a custom control classes.
	 *
	 * @since 1.0.0
	 * @param array $classes CSS-classes.
	 */
	public static function add_body_class( $classes ) {

		if ( ! empty( self::$classes ) ) {
			$classes = array_merge( self::$classes, $classes );
		}

		return $classes;
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
 * Returns instance of templater class.
 *
 * @since 1.0.0
 * @return Cherry_RE_Templater
 */
function cherry_re_templater() {
	return Cherry_RE_Templater::get_instance();
}

cherry_re_templater();
