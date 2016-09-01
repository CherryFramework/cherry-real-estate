<?php
/**
 * Model settings class file.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Models
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Model settings class.
 *
 * @since 1.0.0
 */
class Model_Settings {

	/**
	 * Titles for diferent area unit.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $area_unit_symbols = array(
		'meters' => 'm²',
		'feets'  => 'ft²',
	);

	/**
	 * Get settings from Main section.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function get_main_settings() {
		return get_option( 'cherry-re-options-main' );
	}

	/**
	 * Get settings from Map settings.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function get_map_settings() {
		return get_option( 'cherry-re-options-map' );
	}

	/**
	 * Get settings from Confirmation E-mail settings.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function get_emails_settings() {
		return get_option( 'cherry-re-options-emails' );
	}

	/**
	 * Get settings for Property pages.
	 *
	 * @since 1.0.0
	 * @return mixed
	 */
	public static function get_listing_settings() {
		return get_option( 'cherry-re-options-listing' );
	}

	/**
	 * Get are unit.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_area_unit_setting() {
		$main_settings = self::get_main_settings();

		return ! empty( $main_settings['area-unit'] ) ? $main_settings['area-unit'] : '';
	}

	/**
	 * Get area unit options.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public static function get_area_unit() {
		return apply_filters( 'cherry_re_get_area_unit', array(
			'feets'  => esc_html__( 'feets', 'cherry-real-estate' ),
			'meters' => esc_html__( 'meters', 'cherry-real-estate' ),
		) );
	}

	/**
	 * Get area unit title.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_area_unit_title() {

		if ( ! empty( self::$area_unit_symbols[ self::get_area_unit_setting() ] ) ) {
			return self::$area_unit_symbols[ self::get_area_unit_setting() ];
		}

		return self::$area_unit_symbols['feets'];
	}

	/**
	 * Get currency symbol.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_currency_symbol() {
		$main_settings = self::get_main_settings();

		return ! empty( $main_settings['currency-sign'] ) ? $main_settings['currency-sign'] : '$';
	}

	/**
	 * Get currency position.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_currency_position() {
		$main_settings = self::get_main_settings();
		$value         = false;

		if ( ! empty( $main_settings['currency-position'] ) ) {
			$value = stripslashes( $main_settings['currency-position'] );
		}

		return $value ? $value : 'left';
	}

	/**
	 * Get thousand separator.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_thousand_sep() {
		$main_settings = self::get_main_settings();
		$value         = false;

		if ( isset( $main_settings['thousand-sep'] ) ) {
			$value = stripslashes( $main_settings['thousand-sep'] );
		}

		return false !== $value ? $value : ',';
	}

	/**
	 * Get decimal separator.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_decimal_sep() {
		$main_settings = self::get_main_settings();
		$value         = false;

		if ( isset( $main_settings['decimal-sep'] ) ) {
			$value = stripslashes( $main_settings['decimal-sep'] );
		}

		return false !== $value ? $value : '.';
	}

	/**
	 * Get number of decimals.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_decimal_numb() {
		$main_settings = self::get_main_settings();
		$value         = false;

		if ( isset( $main_settings['decimal-numb'] ) ) {
			$value = absint( $main_settings['decimal-numb'] );
		}

		return false !== $value ? $value : 2;
	}

	/**
	 * Get the price format depending on the currency position.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public static function get_price_format() {
		$currency_pos = self::get_currency_position();

		switch ( $currency_pos ) {
			case 'right' :
				$format = '%2$s%1$s';
				break;

			case 'left-with-space' :
				$format = '%1$s&nbsp;%2$s';
				break;

			case 'right-with-space' :
				$format = '%2$s&nbsp;%1$s';
				break;

			default:
				$format = '%1$s%2$s';
				break;
		}

		return apply_filters( 'cherry_re_price_format', $format, $currency_pos );
	}

	/**
	 * Get map api key.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_map_api_key() {
		$map_settings = self::get_map_settings();

		return ! empty( $map_settings['api_key'] ) ? $map_settings['api_key'] : '';
	}

	/**
	 * Get map style.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_map_style() {
		$map_settings = self::get_map_settings();

		return ! empty( $map_settings['style'] ) ? $map_settings['style'] : '';
	}

	/**
	 * Get map marker.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_map_marker() {
		$map_settings = self::get_map_settings();

		return ! empty( $map_settings['marker'] ) ? $map_settings['marker'] : '';
	}

	/**
	 * Get notification subject.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_notification_subject() {
		$emails = self::get_emails_settings();

		return ! empty( $emails['notification-subject'] ) ? $emails['notification-subject'] : '';
	}

	/**
	 * Get notification message.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_notification_message() {
		$emails = self::get_emails_settings();

		return ! empty( $emails['notification-message'] ) ? $emails['notification-message'] : '';
	}

	/**
	 * Get congratulate subject.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_congratulate_subject() {
		$emails = self::get_emails_settings();

		return ! empty( $emails['congratulate-subject'] ) ? $emails['congratulate-subject'] : '';
	}

	/**
	 * Get congratulate message.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_congratulate_message() {
		$emails = self::get_emails_settings();

		return ! empty( $emails['congratulate-message'] ) ? $emails['congratulate-message'] : '';
	}

	/**
	 * Get listing page.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_listing_page() {
		$listing_options = self::get_listing_settings();

		return ! empty( $listing_options['page'] ) ? $listing_options['page'] : '';
	}

	/**
	 * Get listing layout.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_listing_layout() {
		$listing_options = self::get_listing_settings();
		$layout         = false;

		if ( is_array( $listing_options ) && ! empty( $listing_options['layout'] ) ) {
			$layout = $listing_options['layout'];
		}

		if ( ! in_array( $layout, array( 'grid', 'list' ) ) ) {
			$layout = 'grid';
		}

		return $layout;
	}

	/**
	 * Get listing per page.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_listing_per_page() {
		$listing_options = self::get_listing_settings();

		return ! empty( $listing_options['posts_per_page'] ) ? $listing_options['posts_per_page'] : 10;
	}

	/**
	 * Remove all settings.
	 *
	 * @since 1.0.0
	 */
	public static function remove_all_settings() {
		$options_page = Cherry_RE_Options_Page::get_instance();
		$sections     = $options_page->get_sections();

		if ( empty( $sections ) || ! is_array( $sections ) ) {
			return;
		}

		$options = array_keys( $sections );

		if ( empty( $options ) ) {
			return;
		}

		foreach ( $options as $key ) {
			delete_option( $key );
		}
	}
}
