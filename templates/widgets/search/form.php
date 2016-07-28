<?php
/**
 * Search Form view.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/widgets/search/form.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */
?>
<form role="search" method="get" class="widget-tm-re-search__form" action="<?php echo home_url( '/' ); ?>">

	<?php if ( ! empty( $title ) ) {
		echo $title;
	} ?>

	<input type="hidden" name="post_type" value="<?php echo cherry_real_estate()->get_post_type_name(); ?>">
	<input type="hidden" name="orderby" value="<?php echo $values['orderby']; ?>">
	<input type="hidden" name="order" value="<?php echo $values['order']; ?>">

	<div class="tm-re-search-form__group">
		<label for="s"><?php esc_html_e( 'Keyword', 'cherry-real-estate' ); ?></label>
		<input type="text" name="s" id="s" placeholder="<?php esc_html_e( 'Any', 'cherry-real-estate' ); ?>" value="<?php echo $values['s']; ?>">
	</div>

	<?php $select_status = Cherry_RE_Tools::select_form( Model_Properties::get_allowed_property_statuses(), array(
			'name'    => 'property_status',
			'default' => esc_html__( 'Any', 'cherry-real-estate' ),
			'value'   => $values['property_status'],
			'echo'    => false,
		) ); ?>

	<?php if ( ! empty( $select_status ) ) { ?>

		<div class="tm-re-search-form__group">
			<label for="property_status"><?php esc_html_e( 'Property status', 'cherry-real-estate' ); ?></label>
			<?php echo $select_status; ?>
		</div>

	<?php } ?>

	<?php $select_types = Cherry_RE_Tools::select_form( Model_Properties::get_property_types( 'slug' ), array(
			'name'    => 'property_type',
			'default' => esc_html__( 'Any', 'cherry-real-estate' ),
			'value'   => $values['property_status'],
			'echo'    => false,
		) ); ?>

	<?php if ( ! empty( $select_types ) ) { ?>

		<div class="tm-re-search-form__group">
			<label for="property_type"><?php esc_html_e( 'Property type', 'cherry-real-estate' ); ?></label>
			<?php echo $select_types; ?>
		</div>

	<?php } ?>

	<div class="tm-re-search-form__group">
		<label for="property_location" class="tm-re-search-form__label"><?php esc_html_e( 'Location', 'cherry-real-estate' ); ?></label>
		<input type="text" name="property_location" id="property_location" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Any', 'cherry-real-estate' ); ?>" value="<?php echo $values['property_location']; ?>">
	</div>

	<div class="tm-re-search-form__group">
		<label for="min_price" class="tm-re-search-form__label"><?php esc_html_e( 'Price', 'cherry-real-estate' ); ?></label>
		<div class="tm-re-search-form__range">
			<input type="number" min="0" name="min_price" id="min_price" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Min', 'cherry-real-estate' ); ?>" value="<?php echo $values['min_price']; ?>">
			<input type="number" min="0" name="max_price" id="max_price" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Max', 'cherry-real-estate' ); ?>" value="<?php echo $values['max_price']; ?>">
		</div>
	</div>

	<div class="tm-re-search-form__group">
		<label for="min_bedrooms" class="tm-re-search-form__label"><?php esc_html_e( 'Bedrooms', 'cherry-real-estate' ); ?></label>
		<div class="tm-re-search-form__range">
			<input type="number" min="0" name="min_bedrooms" id="min_bedrooms" placeholder="<?php esc_html_e( 'Min', 'cherry-real-estate' ); ?>" value="<?php echo $values['min_bedrooms']; ?>">
			<input type="number" min="0" name="max_bedrooms" id="max_bedrooms" placeholder="<?php esc_html_e( 'Max', 'cherry-real-estate' ); ?>" value="<?php echo $values['max_bedrooms']; ?>">
		</div>
	</div>

	<div class="tm-re-search-form__group">
		<label for="min_bathrooms" class="tm-re-search-form__label"><?php esc_html_e( 'Bathrooms', 'cherry-real-estate' ); ?></label>
		<div class="tm-re-search-form__range">
			<input type="number" min="0" name="min_bathrooms" id="min_bathrooms" placeholder="<?php esc_html_e( 'Min', 'cherry-real-estate' ); ?>" value="<?php echo $values['min_bathrooms']; ?>">
			<input type="number" min="0" name="max_bathrooms" id="max_bathrooms" placeholder="<?php esc_html_e( 'Max', 'cherry-real-estate' ); ?>" value="<?php echo $values['max_bathrooms']; ?>">
		</div>
	</div>

	<div class="tm-re-search-form__group">
		<label for="min_area" class="tm-re-search-form__label"><?php esc_html_e( 'Area', 'cherry-real-estate' ); ?></label>
		<div class="tm-re-search-form__range">
			<input type="number" min="0" name="min_area" id="min_area" placeholder="<?php esc_html_e( 'Min', 'cherry-real-estate' ); ?>" value="<?php echo $values['min_area']; ?>">
			<input type="number" min="0" name="max_area" id="max_area" placeholder="<?php esc_html_e( 'Max', 'cherry-real-estate' ); ?>" value="<?php echo $values['max_area']; ?>">
		</div>
	</div>

	<div class="tm-re-search-form__group">
		<label for="min_parking_place" class="tm-re-search-form__label"><?php esc_html_e( 'Parking spots', 'cherry-real-estate' ); ?></label>
		<div class="tm-re-search-form__range">
			<input type="number" min="0" name="min_parking_place" id="min_parking_place" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Min', 'cherry-real-estate' ); ?>" value="<?php echo $values['min_parking_place']; ?>">
			<input type="number" min="0" name="max_parking_place" id="max_parking_place" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Max', 'cherry-real-estate' ); ?>" value="<?php echo $values['max_parking_place']; ?>">
		</div>
	</div>

	<button type="submit" class="tm-re-search-form__submit"><?php esc_html_e( 'Search', 'cherry-real-estate' ); ?></button>
</form>
