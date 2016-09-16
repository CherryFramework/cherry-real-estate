<?php
/**
 * Managing shortcodes data.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Class for managing shortcodes data.
 *
 * @since 1.0.0
 */
class Cherry_RE_Shortcodes_Data {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Storage for data object.
	 *
	 * @since 1.0.0
	 * @var   null|object
	 */
	public $data = null;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {}

	/**
	 * Retrieve a shortcodes.
	 *
	 * @since  1.0.0
	 * @param  bool|string $shortcode Shortcode tag. False - returned all shortcodes.
	 * @return array                  Shortcode settings.
	 */
	public static function shortcodes( $shortcode = false ) {
		$image_sizes = get_intermediate_image_sizes();

		/**
		 * Filter a shortcode settings.
		 *
		 * @since 1.0.0
		 * @param array $shortcodes All shortcode settings.
		 */
		$shortcodes = apply_filters( 'cherry_re_shortcodes_data', array(

				// [agents_list]
				'agents_list' => array(
					'title' => esc_html__( 'Agents List', 'cherry-real-estate' ),
					'icon'  => '',
					'body'  => array(
						array(
							'type'  => 'textbox',
							'name'  => 'number',
							'value' => '10',
							'label' => esc_html__( 'How Many?', 'cherry-real-estate' ),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'orderby',
							'value'  => 'display_name',
							'label'  => esc_html__( 'Order By', 'cherry-real-estate' ),
							'values' => array(
								'id'           => esc_html__( 'ID', 'cherry-real-estate' ),
								'display_name' => esc_html__( 'Display name', 'cherry-real-estate' ),
								'name'         => esc_html__( 'Name', 'cherry-real-estate' ),
								'login'        => esc_html__( 'Login', 'cherry-real-estate' ),
								'registered'   => esc_html__( 'Registered date', 'cherry-real-estate' ),
								'post_count'   => esc_html__( 'Post count', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'order',
							'value'  => 'desc',
							'label'  => esc_html__( 'Order', 'cherry-real-estate' ),
							'values' => array(
								'asc'  => esc_html__( 'Ascending', 'cherry-real-estate' ),
								'desc' => esc_html__( 'Descending', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_photo',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Photo', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'photo_size',
							'value'  => 'thumbnail',
							'label'  => esc_html__( 'Photo Size', 'cherry-real-estate' ),
							'values' => array_combine( $image_sizes, $image_sizes ),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_name',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Name', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_desc',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Description', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'  => 'textbox',
							'name'  => 'desc_length',
							'value' => '10',
							'label' => esc_html__( 'Description Length (in words)', 'cherry-real-estate' ),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_contacts',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Contacts', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_socials',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Socials', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_more_button',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show More Button', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'  => 'textbox',
							'name'  => 'more_button_text',
							'value' => esc_html__( 'read more', 'cherry-real-estate' ),
							'label' => esc_html__( 'More Button Text', 'cherry-real-estate' ),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_pagination',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Pagination', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'template',
							'value'  => 'default.tmpl',
							'label'  => esc_html__( 'Template', 'cherry-real-estate' ),
							'values' => cherry_re_templater()->get_agent_templates_list(),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'color_scheme',
							'value'  => 'regular',
							'label'  => esc_html__( 'Color Scheme', 'cherry-real-estate' ),
							'values' => array(
								'regular' => esc_html__( 'Regular', 'cherry-real-estate' ),
								'invert'  => esc_html__( 'Invert', 'cherry-real-estate' ),
							),
						),
						array(
							'type'  => 'textbox',
							'name'  => 'css_class',
							'value' => '',
							'label' => esc_html__( 'Extra CSS classes', 'cherry-real-estate' ),
						),
					),
				),

				// [property_list]
				'property_list' => array(
					'title' => esc_html__( 'Property List', 'cherry-real-estate' ),
					'icon'  => '',
					'body'  => array(
						array(
							'type'  => 'textbox',
							'name'  => 'number',
							'value' => '5',
							'label' => esc_html__( 'How Many?', 'cherry-real-estate' ),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'orderby',
							'value'  => 'date',
							'label'  => esc_html__( 'Order By', 'cherry-real-estate' ),
							'values' => array(
								'none'       => esc_html__( 'None', 'cherry-real-estate' ),
								'id'         => esc_html__( 'Property ID', 'cherry-real-estate' ),
								'author'     => esc_html__( 'Property author', 'cherry-real-estate' ),
								'title'      => esc_html__( 'Property title', 'cherry-real-estate' ),
								'name'       => esc_html__( 'Property slug', 'cherry-real-estate' ),
								'date'       => esc_html__( 'Date', 'cherry-real-estate' ),
								'modified'   => esc_html__( 'Last modified date', 'cherry-real-estate' ),
								'parent'     => esc_html__( 'Property parent', 'cherry-real-estate' ),
								'rand'       => esc_html__( 'Random', 'cherry-real-estate' ),
								'menu_order' => esc_html__( 'Menu order', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'order',
							'value'  => 'asc',
							'label'  => esc_html__( 'Order', 'cherry-real-estate' ),
							'values' => array(
								'asc'  => esc_html__( 'Ascending', 'cherry-real-estate' ),
								'desc' => esc_html__( 'Descending', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_title',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Title', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_image',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Image', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'image_size',
							'value'  => 'thumbnail',
							'label'  => esc_html__( 'Image Size', 'cherry-real-estate' ),
							'values' => array_combine( $image_sizes, $image_sizes ),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_status',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Status', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_area',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Area', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_bedrooms',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Bedrooms', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_bathrooms',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Bathrooms', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_price',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Price', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_location',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Location', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_excerpt',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show Excerpt', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'  => 'textbox',
							'name'  => 'excerpt_length',
							'value' => '15',
							'label' => esc_html__( 'Excerpt Length (in words)', 'cherry-real-estate' ),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_more_button',
							'value'  => 'yes',
							'label'  => esc_html__( 'Show More Button', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'  => 'textbox',
							'name'  => 'more_button_text',
							'value' => esc_html__( 'read more', 'cherry-real-estate' ),
							'label' => esc_html__( 'More Button Text', 'cherry-real-estate' ),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'show_pagination',
							'value'  => 'no',
							'label'  => esc_html__( 'Show Pagination', 'cherry-real-estate' ),
							'values' => array(
								'no'  => esc_html__( 'no', 'cherry-real-estate' ),
								'yes' => esc_html__( 'yes', 'cherry-real-estate' ),
							),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'template',
							'value'  => 'default.tmpl',
							'label'  => esc_html__( 'Template', 'cherry-real-estate' ),
							'values' => cherry_re_templater()->get_property_templates_list(),
						),
						array(
							'type'   => 'listbox',
							'name'   => 'color_scheme',
							'value'  => 'regular',
							'label'  => esc_html__( 'Color Scheme', 'cherry-real-estate' ),
							'values' => array(
								'regular' => esc_html__( 'Regular', 'cherry-real-estate' ),
								'invert'  => esc_html__( 'Invert', 'cherry-real-estate' ),
							),
						),
						array(
							'type'  => 'textbox',
							'name'  => 'css_class',
							'value' => '',
							'label' => esc_html__( 'Extra CSS classes', 'cherry-real-estate' ),
						),
					),
				),

				// [submission_form]
				'submission_form' => array(
					'title' => esc_html__( 'Submission Form', 'cherry-real-estate' ),
					'icon'  => '',
				),
			)
		);

		// Return result.
		return ( is_string( $shortcode ) ) ? $shortcodes[ sanitize_text_field( $shortcode ) ] : $shortcodes;
	}

	/**
	 * Builds the Agents List shortcode output.
	 *
	 * @since  1.0.0
	 * @param  array  $atts    Shortcode attributes.
	 * @param  string $content Shortcode content.
	 * @return string
	 */
	public function agents_list( $atts = null, $content = null ) {
		$defaults = apply_filters( 'cherry_re_agents_list_shortcode_defaults', array(
			'number'           => 10,
			'orderby'          => 'display_name',
			'order'            => 'desc',
			'show_name'        => 'yes',
			'show_photo'       => 'yes',
			'photo_size'       => 'thumbnail',
			'show_desc'        => 'yes',
			'desc_length'      => 10,
			'show_contacts'    => 'yes',
			'show_socials'     => 'yes',
			'show_more_button' => 'yes',
			'more_button_text' => esc_html__( 'read more', 'cherry-real-estate' ),
			'show_pagination'  => 'yes',
			'template'         => 'default.tmpl',
			'color_scheme'     => 'regular',
			'css_class'        => '',
		), $atts );

		$shortcode = 'agents_list';
		$atts      = shortcode_atts( $defaults, $atts, $shortcode );

		$bool_to_fix = array(
			'show_name',
			'show_photo',
			'show_desc',
			'show_contacts',
			'show_socials',
			'show_more_button',
			'show_pagination',
		);

		// Fix booleans.
		foreach ( $bool_to_fix as $v ) {
			$atts[ $v ] = filter_var( $atts[ $v ], FILTER_VALIDATE_BOOLEAN );
		}

		if ( $atts['show_photo'] ) {
			$image_sizes        = get_intermediate_image_sizes();
			$atts['photo_size'] = ! in_array( $atts['photo_size'], $image_sizes ) ? $defaults['photo_size'] : $atts['photo_size'];
		}

		$atts['number']      = intval( $atts['number'] );
		$atts['desc_length'] = intval( $atts['desc_length'] );
		$atts['css_class']   = esc_attr( $atts['css_class'] );

		// Make sure we return and don't echo.
		$atts['echo'] = false;

		$data   = Cherry_RE_Agent_Data::get_instance();
		$output = $data->the_agents( $atts );

		return apply_filters( 'cherry_re_shortcodes_output', $output, $atts, $shortcode );
	}

	/**
	 * Builds the Property List shortcode output.
	 *
	 * @since  1.0.0
	 * @param  array  $atts    Shortcode attributes.
	 * @param  string $content Shortcode content.
	 * @return string
	 */
	public function property_list( $atts = null, $content = null ) {
		$defaults = apply_filters( 'cherry_re_property_list_shortcode_defaults', array(
			'number'           => 5,
			'orderby'          => 'date',
			'order'            => 'desc',
			'show_title'       => 'yes',
			'show_image'       => 'yes',
			'image_size'       => 'thumbnail',
			'show_status'      => 'yes',
			'show_area'        => 'yes',
			'show_bedrooms'    => 'yes',
			'show_bathrooms'   => 'yes',
			'show_price'       => 'yes',
			'show_location'    => 'yes',
			'show_excerpt'     => 'yes',
			'excerpt_length'   => 15,
			'show_more_button' => 'yes',
			'more_button_text' => esc_html__( 'read more', 'cherry-real-estate' ),
			'show_pagination'  => 'no',
			'template'         => 'default.tmpl',
			'color_scheme'     => 'regular',
			'css_class'        => '',
		), $atts );

		$shortcode = 'property_list';
		$atts      = shortcode_atts( $defaults, $atts, $shortcode );

		$bool_to_fix = array(
			'show_title',
			'show_image',
			'show_status',
			'show_area',
			'show_bedrooms',
			'show_bathrooms',
			'show_price',
			'show_location',
			'show_excerpt',
			'show_more_button',
			'show_pagination',
		);

		// Fix booleans.
		foreach ( $bool_to_fix as $v ) {
			$atts[ $v ] = filter_var( $atts[ $v ], FILTER_VALIDATE_BOOLEAN );
		}

		if ( $atts['show_image'] ) {
			$image_sizes        = get_intermediate_image_sizes();
			$atts['image_size'] = ! in_array( $atts['image_size'], $image_sizes ) ? $defaults['image_size'] : $atts['image_size'];
		}

		$atts['number']         = intval( $atts['number'] );
		$atts['excerpt_length'] = intval( $atts['excerpt_length'] );
		$atts['css_class']      = esc_attr( $atts['css_class'] );

		// Make sure we return and don't echo.
		$atts['echo'] = false;

		$data   = Cherry_RE_Property_Data::get_instance();
		$output = $data->the_property( $atts );

		return apply_filters( 'cherry_re_shortcodes_output', $output, $atts, $shortcode );
	}

	/**
	 * Builds the Submit Form shortcode output.
	 *
	 * @since  1.0.0
	 * @param  array  $atts    Shortcode attributes.
	 * @param  string $content Shortcode content.
	 * @return string
	 */
	public function submission_form( $atts = null, $content = null ) {
		$defaults  = apply_filters( 'cherry_re_submission_form_shortcode_defaults', array(), $atts );
		$shortcode = 'submission_form';
		$atts      = shortcode_atts( $defaults, $atts, $shortcode );

		$output = $this->get_popup();
		$output .= cherry_re_get_template_html( 'shortcodes/' . $shortcode . '/form' );

		return apply_filters( 'cherry_re_shortcodes_output', $output, $atts, $shortcode );
	}

	/**
	 * Retrieve a popup with login & register forms.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_popup() {
		$output = '';

		if ( ! is_user_logged_in() ) {

			$register_form = '';

			if ( get_option( 'users_can_register' ) ) {
				$register_form = cherry_re_get_template_html( 'auth/register' );
			} else {
				$register_form = esc_html__( 'User registration is currently not allowed.', 'cherry-real-estate' );
			}

			$output .= cherry_re_get_template_html( 'auth/popup', array(
				'popup_id'      => Model_Submit_Form::get_popup_id(),
				'login_form'    => cherry_re_get_template_html( 'auth/login' ),
				'register_form' => $register_form,
			) );
		}

		return $output;
	}

	/**
	 * Add buttons.
	 *
	 * @since 1.0.0
	 */
	public static function add_buttons() {

		// Check user permissions.
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Check if WYSIWYG is enabled.
		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( __CLASS__, 'mce_external_plugins' ) );
			add_filter( 'mce_buttons', array( __CLASS__, 'mce_buttons' ) );
		}

		foreach ( array( 'post.php', 'post-new.php' ) as $hook ) {
			add_action( "admin_head-$hook", array( __CLASS__, 'localize_script' ) );
		}
	}

	/**
	 * Localize Script.
	 *
	 * @since 1.0.0
	 */
	public static function localize_script() {
		$title      = esc_html__( 'Insert RE shortcodes', 'cherry-real-estate' );
		$prefix     = cherry_real_estate()->get_shortcode_prefix();
		$button     = self::get_mce_button();
		$shortcodes = self::_prepare_localize_shortcodes();
?>
<script type='text/javascript'>
var CherryRETinyMCE = {
	'title': '<?php echo esc_js( $title ); ?>',
	'prefix': '<?php echo esc_js( $prefix ); ?>',
	'button': '<?php echo esc_js( $button ); ?>',
	'shortcodes': '<?php echo $shortcodes; ?>'
};
</script>
<?php
	}

	/**
	 * Add js file to plugins array.
	 *
	 * @since  1.0.0
	 * @param  array $plugin_array Array of plugins.
	 * @return array $plugin_array.
	 */
	public static function mce_external_plugins( $plugin_array ) {
		$button = self::get_mce_button();

		$plugin_array[ $button ] = CHERRY_REAL_ESTATE_URI . 'assets/js/tinymce-button.js';

		return $plugin_array;
	}

	/**
	 * Add buttons to buttons array.
	 *
	 * @since  1.0.0
	 * @param  array $buttons Array of buttons.
	 * @return array $buttons.
	 */
	public static function mce_buttons( $buttons ) {
		array_push( $buttons, self::get_mce_button() );

		return $buttons;
	}

	/**
	 * Retrieve a plugin MCE button.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public static function get_mce_button() {
		$prefix = cherry_real_estate()->get_shortcode_prefix();
		$button = $prefix . 'shortcodes';
		$button = str_replace( '-', '_', $button );

		return $button;
	}

	/**
	 * [_prepare_localize_shortcodes description]
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public static function _prepare_localize_shortcodes() {
		$shortcodes = self::shortcodes();

		if ( empty( $shortcodes ) ) {
			return '';
		}

		foreach ( (array) $shortcodes as $key => $shortcode ) {

			if ( empty( $shortcode['body'] ) ) {
				continue;
			}

			foreach ( $shortcode['body'] as $k => $attr ) {

				if ( empty( $attr['type'] ) || empty( $attr['values'] ) ) {
					continue;
				}

				if ( 'listbox' !== $attr['type'] ) {
					continue;
				}

				$shortcodes[ $key ]['body'][ $k ]['values'] = self::prepare_options( $attr['values'] );
			}
		}

		return wp_json_encode( $shortcodes );
	}

	/**
	 * Prepare option for js modal window.
	 *
	 * @since  1.0.0
	 * @param  array   $options     Array of options.
	 * @param  boolean $first_empty Add empty option if true.
	 * @return array
	 */
	public static function prepare_options( $options, $first_empty = true ) {
		$js_options = array();

		if ( is_array( $options ) ) {

			foreach ( $options as $key => $value ) {
				$js_options[] = array(
					'text'  => $value,
					'value' => $key,
				);
			}
		}

		return $js_options;
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

Cherry_RE_Shortcodes_Data::get_instance();
