<?php
/**
 * New post type and taxonomy registration.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Class for register post types.
 *
 * @since 1.0.0
 */
class Cherry_RE_Registration {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
	}

	/**
	 * Register the new post type.
	 *
	 * @since 1.0.0
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public static function register_post_type() {
		$post_type = cherry_real_estate()->get_post_type_name();

		// Labels used when displaying the posts.
		$labels = array(
			'name'                  => esc_html__( 'Properties',                   'cherry-real-estate' ),
			'singular_name'         => esc_html__( 'Property',                     'cherry-real-estate' ),
			'menu_name'             => esc_html__( 'Properties',                   'cherry-real-estate' ),
			'name_admin_bar'        => esc_html__( 'Property',                     'cherry-real-estate' ),
			'add_new'               => esc_html__( 'Add New',                      'cherry-real-estate' ),
			'add_new_item'          => esc_html__( 'Add New Property',             'cherry-real-estate' ),
			'edit_item'             => esc_html__( 'Edit Property',                'cherry-real-estate' ),
			'new_item'              => esc_html__( 'New Property',                 'cherry-real-estate' ),
			'view_item'             => esc_html__( 'View Property',                'cherry-real-estate' ),
			'search_items'          => esc_html__( 'Search Properties',            'cherry-real-estate' ),
			'not_found'             => esc_html__( 'No properties found',          'cherry-real-estate' ),
			'not_found_in_trash'    => esc_html__( 'No properties found in trash', 'cherry-real-estate' ),
			'all_items'             => esc_html__( 'Properties',                   'cherry-real-estate' ),
			'filter_items_list'     => esc_html__( 'Filter properties list',       'cherry-real-estate' ),
			'items_list_navigation' => esc_html__( 'Properties list navigation',   'cherry-real-estate' ),
			'items_list'            => esc_html__( 'Properties list',              'cherry-real-estate' ),
		);

		// What features the post type supports.
		$supports = array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
		);

		// The rewrite handles the URL structure.
		$rewrite = array(
			'slug'       => 'properties',
			'with_front' => false,
			'pages'      => true,
			'feeds'      => true,
			'ep_mask'    => EP_PERMALINK,
		);

		/* Capabilities. */
		$capabilities = array(
			'create_posts'           => 'create_properties',
			'edit_posts'             => 'edit_properties',
			'publish_posts'          => 'manage_properties',
			'read_private_posts'     => 'read',
			'read'                   => 'read',
			'delete_posts'           => 'manage_properties',
			'delete_private_posts'   => 'manage_properties',
			'delete_published_posts' => 'manage_properties',
			'delete_others_posts'    => 'manage_properties',
			'edit_private_posts'     => 'edit_properties',
			'edit_published_posts'   => 'edit_properties',
			// meta caps (don't assign these to roles)
			'edit_post'              => 'edit_property',
			'read_post'              => 'read_property',
			'delete_post'            => 'delete_property',
		);

		if ( current_user_can( 'administrator' ) ) {
			// Show all properties for `administrator`.
			$capabilities = wp_parse_args( $capabilities, array( 'edit_others_posts' => 'manage_properties' ) );
		}

		$args = array(
			'labels'              => $labels,
			'supports'            => $supports,
			'description'         => '',
			'public'              => true,
			'publicly_queryable'  => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-building',
			'can_export'          => true,
			'delete_with_user'    => false,
			'hierarchical'        => false,
			'has_archive'         => 'properties',
			'query_var'           => $post_type,
			'capability_type'     => $post_type,
			'map_meta_cap'        => true,
			'capabilities'        => $capabilities,
			'rewrite'             => $rewrite,
		);

		$args = apply_filters( 'cherry_re_post_type_args', $args );

		register_post_type( $post_type, $args );
	}

	/**
	 * Register the Property taxonomies.
	 *
	 * @since 1.0.0
	 * @link  https://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	public static function register_taxonomies() {
		$post_type = cherry_real_estate()->get_post_type_name();

		/* Register the Property Type taxonomy. */
		register_taxonomy(
			$post_type . '_type',
			array( $post_type ),
			apply_filters( 'cherry_re_taxonomy_type_args', array(
				'public'            => true,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'show_tagcloud'     => true,
				'show_admin_column' => true,
				'hierarchical'      => true,
				'query_var'         => $post_type . '_type',
				/* Capabilities. */
				'capabilities' => array(
					'manage_terms' => 'manage_properties',
					'edit_terms'   => 'manage_properties',
					'delete_terms' => 'manage_properties',
					'assign_terms' => 'edit_properties',
				),
				/* The rewrite handles the URL structure. */
				'rewrite' => array(
					'slug'         => $post_type . '/type',
					'with_front'   => false,
					'hierarchical' => true,
					'ep_mask'      => EP_NONE
				),
				/* Labels used when displaying taxonomy and terms. */
				'labels' => array(
					'name'                       => __( 'Property Types',                 'cherry-real-estate' ),
					'singular_name'              => __( 'Property Type',                  'cherry-real-estate' ),
					'menu_name'                  => __( 'Types',                          'cherry-real-estate' ),
					'name_admin_bar'             => __( 'Type',                           'cherry-real-estate' ),
					'search_items'               => __( 'Search Types',                   'cherry-real-estate' ),
					'popular_items'              => __( 'Popular Types',                  'cherry-real-estate' ),
					'all_items'                  => __( 'All Types',                      'cherry-real-estate' ),
					'edit_item'                  => __( 'Edit Type',                      'cherry-real-estate' ),
					'view_item'                  => __( 'View Type',                      'cherry-real-estate' ),
					'update_item'                => __( 'Update Type',                    'cherry-real-estate' ),
					'add_new_item'               => __( 'Add New Type',                   'cherry-real-estate' ),
					'new_item_name'              => __( 'New Type Name',                  'cherry-real-estate' ),
					'separate_items_with_commas' => __( 'Separate tags with commas',      'cherry-real-estate' ),
					'add_or_remove_items'        => __( 'Add or remove tags',             'cherry-real-estate' ),
					'choose_from_most_used'      => __( 'Choose from the most used tags', 'cherry-real-estate' ),
					'not_found'                  => __( 'No tags found',                  'cherry-real-estate' ),
					'parent_item'                => null,
					'parent_item_colon'          => null,
				)
			) )
		);

		/* Register the Property Tag taxonomy. */
		register_taxonomy(
			$post_type . '_tag',
			array( $post_type ),
			apply_filters( 'cherry_re_taxonomy_tag_args', array(
				'public'            => true,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'show_tagcloud'     => true,
				'show_admin_column' => true,
				'hierarchical'      => false,
				'query_var'         => $post_type . '_tag',
				/* Capabilities. */
				'capabilities' => array(
					'manage_terms' => 'manage_properties',
					'edit_terms'   => 'manage_properties',
					'delete_terms' => 'manage_properties',
					'assign_terms' => 'edit_properties',
				),
				/* The rewrite handles the URL structure. */
				'rewrite' => array(
					'slug'         => $post_type . '/tag',
					'with_front'   => false,
					'hierarchical' => false,
					'ep_mask'      => EP_NONE
				),
				/* Labels used when displaying taxonomy and terms. */
				'labels' => array(
					'name'                       => __( 'Property Tags',                  'cherry-real-estate' ),
					'singular_name'              => __( 'Property Tag',                   'cherry-real-estate' ),
					'menu_name'                  => __( 'Tags',                           'cherry-real-estate' ),
					'name_admin_bar'             => __( 'Tag',                            'cherry-real-estate' ),
					'search_items'               => __( 'Search Tags',                    'cherry-real-estate' ),
					'popular_items'              => __( 'Popular Tags',                   'cherry-real-estate' ),
					'all_items'                  => __( 'All Tags',                       'cherry-real-estate' ),
					'edit_item'                  => __( 'Edit Tag',                       'cherry-real-estate' ),
					'view_item'                  => __( 'View Tag',                       'cherry-real-estate' ),
					'update_item'                => __( 'Update Tag',                     'cherry-real-estate' ),
					'add_new_item'               => __( 'Add New Tag',                    'cherry-real-estate' ),
					'new_item_name'              => __( 'New Tag Name',                   'cherry-real-estate' ),
					'separate_items_with_commas' => __( 'Separate tags with commas',      'cherry-real-estate' ),
					'add_or_remove_items'        => __( 'Add or remove tags',             'cherry-real-estate' ),
					'choose_from_most_used'      => __( 'Choose from the most used tags', 'cherry-real-estate' ),
					'not_found'                  => __( 'No tags found',                  'cherry-real-estate' ),
					'parent_item'                => null,
					'parent_item_colon'          => null,
				)
			) )
		);

		/* Register the Property Features taxonomy. */
		register_taxonomy(
			$post_type . '_feature',
			array( $post_type ),
			apply_filters( 'cherry_re_taxonomy_feature_args', array(
				'public'            => true,
				'show_ui'           => true,
				'show_in_nav_menus' => true,
				'show_tagcloud'     => true,
				'show_admin_column' => true,
				'hierarchical'      => false,
				'query_var'         => $post_type . '_feature',
				/* Capabilities. */
				'capabilities' => array(
					'manage_terms' => 'manage_properties',
					'edit_terms'   => 'manage_properties',
					'delete_terms' => 'manage_properties',
					'assign_terms' => 'edit_properties',
				),
				/* The rewrite handles the URL structure. */
				'rewrite' => array(
					'slug'         => $post_type . '/feature',
					'with_front'   => false,
					'hierarchical' => false,
					'ep_mask'      => EP_NONE
				),
				/* Labels used when displaying taxonomy and terms. */
				'labels' => array(
					'name'                       => __( 'Property Features',                  'cherry-real-estate' ),
					'singular_name'              => __( 'Property Feature',                   'cherry-real-estate' ),
					'menu_name'                  => __( 'Features',                           'cherry-real-estate' ),
					'name_admin_bar'             => __( 'Feature',                            'cherry-real-estate' ),
					'search_items'               => __( 'Search Features',                    'cherry-real-estate' ),
					'popular_items'              => __( 'Popular Features',                   'cherry-real-estate' ),
					'all_items'                  => __( 'All Features',                       'cherry-real-estate' ),
					'edit_item'                  => __( 'Edit Feature',                       'cherry-real-estate' ),
					'view_item'                  => __( 'View Feature',                       'cherry-real-estate' ),
					'update_item'                => __( 'Update Feature',                     'cherry-real-estate' ),
					'add_new_item'               => __( 'Add New Feature',                    'cherry-real-estate' ),
					'new_item_name'              => __( 'New Feature Name',                   'cherry-real-estate' ),
					'separate_items_with_commas' => __( 'Separate features with commas',      'cherry-real-estate' ),
					'add_or_remove_items'        => __( 'Add or remove features',             'cherry-real-estate' ),
					'choose_from_most_used'      => __( 'Choose from the most used features', 'cherry-real-estate' ),
					'not_found'                  => __( 'No features found',                  'cherry-real-estate' ),
					'parent_item'                => null,
					'parent_item_colon'          => null,
				)
			) )
		);
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

Cherry_RE_Registration::get_instance();
