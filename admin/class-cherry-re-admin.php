<?php
/**
 * Real Estate.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Admin
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * @since 1.1.0
 */
class Cherry_Real_Estate_Admin {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.1.0
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function __construct() {
		$this->meta_prefix = cherry_real_estate()->get_meta_prefix();
		$this->includes();

		// Adds `Property Data` metabox.
		add_action( 'admin_init', array( $this, 'property_data_metabox' ) );
	}

	/**
	 * Include required files used in admin.
	 *
	 * @since 1.1.0
	 */
	public function includes() {
		include_once( CHERRY_REAL_ESTATE_DIR . 'admin/class-cherry-re-options-page.php' );
		include_once( CHERRY_REAL_ESTATE_DIR . 'admin/class-cherry-re-meta-box-location.php' );
		include_once( CHERRY_REAL_ESTATE_DIR . 'admin/page-builder/class-cherry-re-page-builder.php' );

		if ( 'agency' == Model_Settings::get_mode() ) {
			include_once( CHERRY_REAL_ESTATE_DIR . 'admin/class-cherry-re-meta-box-authors.php' );
		}
	}

	/**
	 * Adds `Property Data` metabox.
	 *
	 * @since 1.1.0
	 */
	public function property_data_metabox() {
		cherry_real_estate()->get_core()->init_module( 'cherry-post-meta', array(
			'id'     => 'cherry-re-property-data',
			'title'  => esc_html__( 'Property Data', 'cherry-real-estate' ),
			'page'   => array( cherry_real_estate()->get_post_type_name() ),
			'fields' => apply_filters( 'cherry_re_prorerty_data_metabox_fields', array(
				$this->meta_prefix . 'state' => array(
					'type'    => 'select',
					'id'      => $this->meta_prefix . 'state',
					'name'    => $this->meta_prefix . 'state',
					'title'   => esc_html__( 'State of progress', 'cherry-real-estate' ),
					'options' => Model_Properties::get_allowed_property_states(),
				),
				$this->meta_prefix . 'price' => array(
					'type'       => 'stepper',
					'id'         => $this->meta_prefix . 'price',
					'name'       => $this->meta_prefix . 'price',
					'max_value'  => 9999999999,
					'min_value'  => 0,
					'step_value' => 0.01,
					'title'      => esc_html__( 'Price', 'cherry-real-estate' ),
				),
				$this->meta_prefix . 'status' => array(
					'type'    => 'select',
					'id'      => $this->meta_prefix . 'status',
					'name'    => $this->meta_prefix . 'status',
					'title'   => esc_html__( 'Property status', 'cherry-real-estate' ),
					'options' => Model_Properties::get_allowed_property_statuses(),
				),
				$this->meta_prefix . 'bedrooms' => array(
					'type'       => 'stepper',
					'id'         => $this->meta_prefix . 'bedrooms',
					'name'       => $this->meta_prefix . 'bedrooms',
					'max_value'  => 99999,
					'min_value'  => 0,
					'step_value' => 1,
					'title'      => esc_html__( 'Bedrooms', 'cherry-real-estate' ),
				),
				$this->meta_prefix . 'bathrooms' => array(
					'type'       => 'stepper',
					'id'         => $this->meta_prefix . 'bathrooms',
					'name'       => $this->meta_prefix . 'bathrooms',
					'max_value'  => 99999,
					'min_value'  => 0,
					'step_value' => 1,
					'title'      => esc_html__( 'Bathrooms', 'cherry-real-estate' ),
				),
				$this->meta_prefix . 'area' => array(
					'type'       => 'stepper',
					'id'         => $this->meta_prefix . 'area',
					'name'       => $this->meta_prefix . 'area',
					'max_value'  => 999999,
					'min_value'  => 0,
					'step_value' => 0.01,
					'title'      => esc_html__( 'Area', 'cherry-real-estate' ),
				),
				$this->meta_prefix . 'parking_places' => array(
					'type'      => 'stepper',
					'id'        => $this->meta_prefix . 'parking_places',
					'name'      => $this->meta_prefix . 'parking_places',
					'max_value' => 99999,
					'min_value' => 0,
					'title'     => esc_html__( 'Parking places', 'cherry-real-estate' ),
				),
				$this->meta_prefix . 'gallery' => array(
					'type'         => 'media',
					'id'           => $this->meta_prefix . 'gallery',
					'name'         => $this->meta_prefix . 'gallery',
					'multi_upload' => true,
					'title'        => esc_html__( 'Gallery', 'cherry-real-estate' ),
				),
			) ),
			'admin_columns' => array(
				$this->meta_prefix . 'state' => array(
					'label'    => esc_html__( 'State', 'cherry-real-estate' ),
					'callback' => array( $this, '_show_state' ),
					'position' => 2,
				),
				$this->meta_prefix . 'price' => array(
					'label'    => sprintf( esc_html__( 'Price, %s', 'cherry-real-estate' ), Model_Settings::get_currency_symbol() ),
					'callback' => array( $this, '_show_price' ),
					'position' => 3,
				),
				$this->meta_prefix . 'status' => array(
					'label'    => esc_html__( 'Status', 'cherry-real-estate' ),
					'callback' => array( $this, '_show_status' ),
					'position' => 4,
				),
				'thumbnail' => array(
					'label'    => esc_html__( 'Featured Image', 'cherry-real-estate' ),
					'callback' => array( $this, '_show_thumb' ),
				),
			),
		) );
	}

	/**
	 * Show thumbnail in admin columns.
	 *
	 * @since 1.1.0
	 * @param string $column  The name of the column to display.
	 * @param int    $post_id The ID of the current post.
	 */
	public function _show_thumb( $column, $post_id ) {

		if ( has_post_thumbnail( $post_id ) ) {
			the_post_thumbnail( array( 50, 50 ) );
		}
	}

	/**
	 * Show price in admin columns.
	 *
	 * @since 1.1.0
	 * @param string $column  The name of the column to display.
	 * @param int    $post_id The ID of the current post.
	 */
	public function _show_price( $column, $post_id ) {
		$decimals           = Model_Settings::get_decimal_numb();
		$decimal_separator  = Model_Settings::get_decimal_sep();
		$thousand_separator = Model_Settings::get_thousand_sep();

		$price = floatval( get_post_meta( $post_id, $this->meta_prefix . 'price', true ) );

		echo apply_filters( 'cherry_re_formatted_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );
	}

	/**
	 * Show status in admin columns.
	 *
	 * @since 1.1.0
	 * @param string $column  The name of the column to display.
	 * @param int    $post_id The ID of the current post.
	 */
	public function _show_status( $column, $post_id ) {
		$status  = get_post_meta( $post_id, $this->meta_prefix . 'status', true );
		$allowed = Model_Properties::get_allowed_property_statuses();

		if ( ! array_key_exists( $status, $allowed ) ) {
			$value = end( $allowed );
		} else {
			$value = $allowed[ $status ];
		}

		echo $value;
	}

	/**
	 * Show state in admin columns.
	 *
	 * @since 1.1.0
	 * @param string $column  The name of the column to display.
	 * @param int    $post_id The ID of the current post.
	 */
	public function _show_state( $column, $post_id ) {
		$state   = get_post_meta( $post_id, $this->meta_prefix . 'state', true );
		$allowed = Model_Properties::get_allowed_property_states();

		if ( ! array_key_exists( $state, $allowed ) ) {
			$value = end( $allowed );
		} else {
			$value = $allowed[ $state ];
		}

		echo $value;
	}

	/**
	 * Returns the instance.
	 *
	 * @since 1.1.0
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

Cherry_Real_Estate_Admin::get_instance();
