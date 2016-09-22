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

		$args = array(
			'labels'             => $labels,
			'supports'           => $supports,
			'description'        => '',
			'public'             => true,
			'publicly_queryable' => true,
			'show_in_nav_menus'  => false,
			'show_in_admin_bar'  => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_position'      => null,
			'menu_icon'          => 'dashicons-building',
			'can_export'         => true,
			'delete_with_user'   => false,
			'hierarchical'       => false,
			'has_archive'        => 'properties',
			'query_var'          => $post_type,
			'capability_type'    => $post_type,
			'map_meta_cap'       => true,
			'rewrite'            => $rewrite,
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
				/* Capabilities */
				'capabilities'      => array(
					'manage_terms' => sprintf( 'edit_%s_type', $post_type ),
					'edit_terms'   => sprintf( 'edit_%s_type', $post_type ),
					'delete_terms' => sprintf( 'edit_%s_type', $post_type ),
					'assign_terms' => sprintf( 'read_%s_type', $post_type ),
				),
				/* The rewrite handles the URL structure. */
				'rewrite' => array(
					'slug'         => $post_type . '/type',
					'with_front'   => false,
					'hierarchical' => true,
					'ep_mask'      => EP_NONE,
				),
				/* Labels used when displaying taxonomy and terms. */
				'labels' => array(
					'name'                       => esc_html__( 'Property Types',                 'cherry-real-estate' ),
					'singular_name'              => esc_html__( 'Property Type',                  'cherry-real-estate' ),
					'menu_name'                  => esc_html__( 'Types',                          'cherry-real-estate' ),
					'name_admin_bar'             => esc_html__( 'Type',                           'cherry-real-estate' ),
					'search_items'               => esc_html__( 'Search Types',                   'cherry-real-estate' ),
					'popular_items'              => esc_html__( 'Popular Types',                  'cherry-real-estate' ),
					'all_items'                  => esc_html__( 'All Types',                      'cherry-real-estate' ),
					'edit_item'                  => esc_html__( 'Edit Type',                      'cherry-real-estate' ),
					'view_item'                  => esc_html__( 'View Type',                      'cherry-real-estate' ),
					'update_item'                => esc_html__( 'Update Type',                    'cherry-real-estate' ),
					'add_new_item'               => esc_html__( 'Add New Type',                   'cherry-real-estate' ),
					'new_item_name'              => esc_html__( 'New Type Name',                  'cherry-real-estate' ),
					'separate_items_with_commas' => esc_html__( 'Separate tags with commas',      'cherry-real-estate' ),
					'add_or_remove_items'        => esc_html__( 'Add or remove tags',             'cherry-real-estate' ),
					'choose_from_most_used'      => esc_html__( 'Choose from the most used tags', 'cherry-real-estate' ),
					'not_found'                  => esc_html__( 'No tags found',                  'cherry-real-estate' ),
					'parent_item'                => null,
					'parent_item_colon'          => null,
				),
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
				/* Capabilities */
				'capabilities'      => array(
					'manage_terms' => sprintf( 'edit_%s_tag', $post_type ),
					'edit_terms'   => sprintf( 'edit_%s_tag', $post_type ),
					'delete_terms' => sprintf( 'edit_%s_tag', $post_type ),
					'assign_terms' => sprintf( 'read_%s_tag', $post_type ),
				),
				/* The rewrite handles the URL structure. */
				'rewrite' => array(
					'slug'         => $post_type . '/tag',
					'with_front'   => false,
					'hierarchical' => false,
					'ep_mask'      => EP_NONE,
				),
				/* Labels used when displaying taxonomy and terms. */
				'labels' => array(
					'name'                       => esc_html__( 'Property Tags',                  'cherry-real-estate' ),
					'singular_name'              => esc_html__( 'Property Tag',                   'cherry-real-estate' ),
					'menu_name'                  => esc_html__( 'Tags',                           'cherry-real-estate' ),
					'name_admin_bar'             => esc_html__( 'Tag',                            'cherry-real-estate' ),
					'search_items'               => esc_html__( 'Search Tags',                    'cherry-real-estate' ),
					'popular_items'              => esc_html__( 'Popular Tags',                   'cherry-real-estate' ),
					'all_items'                  => esc_html__( 'All Tags',                       'cherry-real-estate' ),
					'edit_item'                  => esc_html__( 'Edit Tag',                       'cherry-real-estate' ),
					'view_item'                  => esc_html__( 'View Tag',                       'cherry-real-estate' ),
					'update_item'                => esc_html__( 'Update Tag',                     'cherry-real-estate' ),
					'add_new_item'               => esc_html__( 'Add New Tag',                    'cherry-real-estate' ),
					'new_item_name'              => esc_html__( 'New Tag Name',                   'cherry-real-estate' ),
					'separate_items_with_commas' => esc_html__( 'Separate tags with commas',      'cherry-real-estate' ),
					'add_or_remove_items'        => esc_html__( 'Add or remove tags',             'cherry-real-estate' ),
					'choose_from_most_used'      => esc_html__( 'Choose from the most used tags', 'cherry-real-estate' ),
					'not_found'                  => esc_html__( 'No tags found',                  'cherry-real-estate' ),
					'parent_item'                => null,
					'parent_item_colon'          => null,
				),
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
				/* Capabilities */
				'capabilities'      => array(
					'manage_terms' => sprintf( 'edit_%s_feature', $post_type ),
					'edit_terms'   => sprintf( 'edit_%s_feature', $post_type ),
					'delete_terms' => sprintf( 'edit_%s_feature', $post_type ),
					'assign_terms' => sprintf( 'read_%s_feature', $post_type ),
				),
				/* The rewrite handles the URL structure. */
				'rewrite' => array(
					'slug'         => $post_type . '/feature',
					'with_front'   => false,
					'hierarchical' => false,
					'ep_mask'      => EP_NONE,
				),
				/* Labels used when displaying taxonomy and terms. */
				'labels' => array(
					'name'                       => esc_html__( 'Property Features',                  'cherry-real-estate' ),
					'singular_name'              => esc_html__( 'Property Feature',                   'cherry-real-estate' ),
					'menu_name'                  => esc_html__( 'Features',                           'cherry-real-estate' ),
					'name_admin_bar'             => esc_html__( 'Feature',                            'cherry-real-estate' ),
					'search_items'               => esc_html__( 'Search Features',                    'cherry-real-estate' ),
					'popular_items'              => esc_html__( 'Popular Features',                   'cherry-real-estate' ),
					'all_items'                  => esc_html__( 'All Features',                       'cherry-real-estate' ),
					'edit_item'                  => esc_html__( 'Edit Feature',                       'cherry-real-estate' ),
					'view_item'                  => esc_html__( 'View Feature',                       'cherry-real-estate' ),
					'update_item'                => esc_html__( 'Update Feature',                     'cherry-real-estate' ),
					'add_new_item'               => esc_html__( 'Add New Feature',                    'cherry-real-estate' ),
					'new_item_name'              => esc_html__( 'New Feature Name',                   'cherry-real-estate' ),
					'separate_items_with_commas' => esc_html__( 'Separate features with commas',      'cherry-real-estate' ),
					'add_or_remove_items'        => esc_html__( 'Add or remove features',             'cherry-real-estate' ),
					'choose_from_most_used'      => esc_html__( 'Choose from the most used features', 'cherry-real-estate' ),
					'not_found'                  => esc_html__( 'No features found',                  'cherry-real-estate' ),
					'parent_item'                => null,
					'parent_item_colon'          => null,
				),
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
