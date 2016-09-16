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

$values = $passed_vars['values']; ?>

<form role="search" method="get" class="widget-tm-re-search__form" action="<?php echo home_url( '/' ); ?>">

	<?php if ( ! empty( $passed_vars['title'] ) ) {
		echo $passed_vars['title'];
	} ?>

	<div class="tm-re-search-form__group">
		<label>
			<span class="tm-re-search-form__label"><?php esc_html_e( 'Keyword', 'cherry-real-estate' ); ?></span>
			<input type="text" name="s" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Any', 'cherry-real-estate' ); ?>" value="<?php echo $values['s']; ?>">
		</label>
	</div>

	<?php $select_status = Cherry_RE_Tools::select_form( Model_Properties::get_allowed_property_statuses(), array(
			'name'    => 'property_status',
			'default' => esc_html__( 'Any', 'cherry-real-estate' ),
			'value'   => $values['property_status'],
			'echo'    => false,
		) ); ?>

	<?php if ( ! empty( $select_status ) ) { ?>

		<div class="tm-re-search-form__group">
			<label>
				<span class="tm-re-search-form__label"><?php esc_html_e( 'Property status', 'cherry-real-estate' ); ?></span>
				<?php echo $select_status; ?>
			</label>
		</div>

	<?php } ?>

	<?php $select_types = Cherry_RE_Tools::select_form( Model_Properties::get_property_types( 'slug' ), array(
			'name'    => 'property_type',
			'default' => esc_html__( 'Any', 'cherry-real-estate' ),
			'value'   => $values['property_type'],
			'echo'    => false,
		) ); ?>

	<?php if ( ! empty( $select_types ) ) { ?>

		<div class="tm-re-search-form__group">
			<label>
				<span class="tm-re-search-form__label"><?php esc_html_e( 'Property type', 'cherry-real-estate' ); ?></span>
				<?php echo $select_types; ?>
			</label>
		</div>

	<?php } ?>

	<div class="tm-re-search-form__group">
		<label>
			<span class="tm-re-search-form__label"><?php esc_html_e( 'Location', 'cherry-real-estate' ); ?></span>
			<input type="text" name="property_location" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Any', 'cherry-real-estate' ); ?>" value="<?php echo $values['property_location']; ?>">
		</label>
	</div>

	<div class="tm-re-search-form__group-more">

		<input type="checkbox" id="tm-re-more-property-fields" name="tm-re-more-property-fields" class="tm-re-search-form__more-field">

		<div class="tm-re-search-form__more-fields">
			<div class="tm-re-search-form__group">
				<span class="tm-re-search-form__label"><?php esc_html_e( 'Price', 'cherry-real-estate' ); ?></span>
				<span class="tm-re-search-form__range">
					<input type="number" min="0" step="0.01" name="min_price" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Min', 'cherry-real-estate' ); ?>" value="<?php echo $values['min_price']; ?>">
					<input type="number" min="0" step="0.01" name="max_price" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Max', 'cherry-real-estate' ); ?>" value="<?php echo $values['max_price']; ?>">
				</span>
			</div>

			<div class="tm-re-search-form__group">
				<span class="tm-re-search-form__label"><?php esc_html_e( 'Bedrooms', 'cherry-real-estate' ); ?></span>
				<span class="tm-re-search-form__range">
					<input type="number" min="0" name="min_bedrooms" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Min', 'cherry-real-estate' ); ?>" value="<?php echo $values['min_bedrooms']; ?>">
					<input type="number" min="0" name="max_bedrooms" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Max', 'cherry-real-estate' ); ?>" value="<?php echo $values['max_bedrooms']; ?>">
				</span>
			</div>

			<div class="tm-re-search-form__group">
				<span class="tm-re-search-form__label"><?php esc_html_e( 'Bathrooms', 'cherry-real-estate' ); ?></span>
				<span class="tm-re-search-form__range">
					<input type="number" min="0" name="min_bathrooms" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Min', 'cherry-real-estate' ); ?>" value="<?php echo $values['min_bathrooms']; ?>">
					<input type="number" min="0" name="max_bathrooms" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Max', 'cherry-real-estate' ); ?>" value="<?php echo $values['max_bathrooms']; ?>">
				</span>
			</div>

			<div class="tm-re-search-form__group">
				<span class="tm-re-search-form__label"><?php esc_html_e( 'Area', 'cherry-real-estate' ); ?></span>
				<span class="tm-re-search-form__range">
					<input type="number" min="0" step="0.01" name="min_area" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Min', 'cherry-real-estate' ); ?>" value="<?php echo $values['min_area']; ?>">
					<input type="number" min="0" step="0.01" name="max_area" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Max', 'cherry-real-estate' ); ?>" value="<?php echo $values['max_area']; ?>">
				</span>
			</div>

			<div class="tm-re-search-form__group">
				<span class="tm-re-search-form__label"><?php esc_html_e( 'Parking spots', 'cherry-real-estate' ); ?></span>
				<span class="tm-re-search-form__range">
					<input type="number" min="0" name="min_parking_place" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Min', 'cherry-real-estate' ); ?>" value="<?php echo $values['min_parking_place']; ?>">
					<input type="number" min="0" name="max_parking_place" class="tm-re-search-form__field" placeholder="<?php esc_html_e( 'Max', 'cherry-real-estate' ); ?>" value="<?php echo $values['max_parking_place']; ?>">
				</span>
			</div>

		</div>

		<label for="tm-re-more-property-fields" class="tm-re-search-form__more-toggle"><?php esc_html_e( 'More', 'cherry-real-estate' ); ?></label>
	</div>

	<input type="hidden" name="post_type" value="<?php echo cherry_real_estate()->get_post_type_name(); ?>">

	<button type="submit" class="tm-re-search-form__submit"><?php esc_html_e( 'Search', 'cherry-real-estate' ); ?></button>
</form>
