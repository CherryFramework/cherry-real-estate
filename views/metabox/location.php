<?php
/**
 * Authors metabox view.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Views
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

$meta_key    = $passed_vars['key'];
$meta_values = $passed_vars['values'];

$location      = ! empty( $meta_values[ $meta_key . 'location'] ) ? $meta_values[ $meta_key . 'location'][0] : '';
$latitude      = ! empty( $meta_values[ 'geo_latitude'] )  ? $meta_values[ 'geo_latitude'][0] : '';
$longitude     = ! empty( $meta_values[ 'geo_longitude'] ) ? $meta_values[ 'geo_longitude'][0] : '';
$search_status = ! empty( $location ) ? ' ' : ' cherry-re-location-place--hidden';

if ( $latitude && $longitude ) {
	$search  = '';
	$checked = false;

} else {
	$search  = $location;
	$checked = true;
} ?>

<div class="cherry-re-location-place">

	<input class="cherry-re-location-place__toggle-control" type="checkbox" id="cherry-re-location-search-toggle" <?php checked( $checked, true, true ); ?>>

	<div class="cherry-re-location-place__search">
		<input  id="cherry-re-geocode-input"
				class="cherry-re-location-place__input cherry-ui-text"
				type="text"
				name="<?php echo esc_attr( $meta_key ); ?>search_location"
				placeholder="<?php esc_html_e( 'Search', 'cherry-real-estate' ); ?>"
				value="<?php echo esc_attr( $search ); ?>">
		<button type="button" id="cherry-re-geocode-trigger" class="cherry-re-location-place__btn dashicons dashicons-search" aria-label="<?php esc_html_e( 'Search', 'cherry-real-estate' ); ?>"></button>
	</div>

	<div class="cherry-re-location-place__map">
		<div id="cherry-re-location-place-map" class="cherry-re-location-place__map-wrap"></div>
		<label for="cherry-re-location-search-toggle" class="cherry-re-location-place__toggle-text<?php echo esc_attr( $search_status ); ?>"><?php esc_html_e( 'Search', 'cherry-real-estate' ); ?></label>
	</div>

	<p class="cherry-re-location-place__desc description"><?php esc_html_e( 'Drag marker to reposition.', 'cherry-real-estate' ); ?></p>

	<div id="cherry-re-location-place-details" class="cherry-re-location-place__fieldset">

		<div class="cherry-re-location-place__field cherry-re-location-place__field--formatted-address">
			<label>
				<span class="cherry-re-location-place__field-label"><?php esc_html_e( 'Address', 'cherry-real-estate' ); ?></span>
				<input  type="text"
						data-geo="formatted_address"
						name="<?php echo esc_attr( $meta_key ); ?>location"
						class="cherry-ui-text"
						value="<?php echo esc_attr( $location ); ?>">
			</label>
			<p class="cherry-re-location-place__desc description"><?php esc_html_e( 'You may format and specify address.', 'cherry-real-estate' ); ?></p>
		</div>

		<div class="cherry-re-location-place__field cherry-re-location-place__field--lat">
			<label>
				<span class="cherry-re-location-place__field-label"><?php esc_html_e( 'Latitude', 'cherry-real-estate' ); ?></span>
				<input  type="text"
						data-geo="lat"
						name="geo_latitude"
						class="cherry-ui-text"
						value="<?php echo esc_attr( $latitude ); ?>"
						readonly>
			</label>
		</div>

		<div class="cherry-re-location-place__field cherry-re-location-place__field--lng">
			<label>
				<span class="cherry-re-location-place__field-label"><?php esc_html_e( 'Longitude', 'cherry-real-estate' ); ?></span>
				<input type="text"
						data-geo="lng"
						name="geo_longitude"
						class="cherry-ui-text"
						value="<?php echo esc_attr( $longitude ); ?>"
						readonly>
			</label>
		</div>
	</div>

</div>