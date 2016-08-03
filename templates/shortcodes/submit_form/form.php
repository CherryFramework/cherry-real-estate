<?php
/**
 * Submission Form view.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/shortcodes/submit_form/form.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

$post_type = cherry_real_estate()->get_post_type_name();
$post_meta = cherry_real_estate()->get_meta_prefix();
?>

<form method="post" id="tm-re-submitform" class="tm-re-submit-form" action="#">
	<?php wp_nonce_field( '_tm-re-submit-form', 'tm-re-submitform-nonce' ); ?>

	<div class="tm-re-submit-form__group">
		<label for="tm-re-submitform__title"><?php esc_html_e( 'Title', 'cherry-real-estate' ); ?></label>
		<input type="text" id="tm-re-submitform__title" name="property_title" value="" required="required">
	</div>

	<div class="tm-re-submit-form__group">
		<label for="tm-re-submitform__desc"><?php esc_html_e( 'Description', 'cherry-real-estate' ); ?></label>
		<textarea id="tm-re-submitform__desc" name="property_description" required="required"></textarea>
	</div>

	<div class="tm-re-submit-form__group">
		<label for="ptm-re-submitform__price"><?php esc_html_e( 'Price', 'cherry-real-estate' ); ?></label>
		<input type="text" id="tm-re-submitform__price" name="property_price" value="">
	</div>

	<?php $select_status = Cherry_RE_Tools::select_form( Model_Properties::get_allowed_property_statuses(), array(
			'id'      => 'tm-re-submitform__status',
			'name'    => 'property_status',
			'default' => esc_html__( 'Choose', 'cherry-real-estate' ),
			'echo'    => false,
		) ); ?>

	<?php if ( ! empty( $select_status ) ) { ?>

		<div class="tm-re-search-form__group">
			<label for="tm-re-submitform__status"><?php esc_html_e( 'Status', 'cherry-real-estate' ); ?></label>
			<?php echo $select_status; ?>
		</div>

	<?php } ?>

	<?php $select_types = Cherry_RE_Tools::select_form( Model_Properties::get_property_types( 'id' ), array(
			'id'      => 'tm-re-submitform__type',
			'name'    => 'property_type',
			'default' => esc_html__( 'Choose', 'cherry-real-estate' ),
			'echo'    => false,
		) ); ?>

	<?php if ( ! empty( $select_types ) ) { ?>

		<div class="tm-re-search-form__group">
			<label for="tm-re-submitform__type"><?php esc_html_e( 'Type', 'cherry-real-estate' ); ?></label>
			<?php echo $select_types; ?>
		</div>

	<?php } ?>

	<div class="tm-re-submit-form__group">
		<label for="tm-re-submitform__bedrooms"><?php esc_html_e( 'Bedrooms', 'cherry-real-estate' ); ?></label>
		<input type="text" id="tm-re-submitform__bedrooms" name="property_bedrooms" value="">
	</div>

	<div class="tm-re-submit-form__group">
		<label for="tm-re-submitform__bathrooms"><?php esc_html_e( 'Bathrooms', 'cherry-real-estate' ); ?></label>
		<input type="text" id="tm-re-submitform__bathrooms" name="property_bathrooms" value="">
	</div>

	<div class="tm-re-submit-form__group">
		<label for="tm-re-submitform__area"><?php esc_html_e( 'Area', 'cherry-real-estate' ); ?></label>
		<input type="text" id="tm-re-submitform__area" name="property_area" value="">
	</div>

	<div class="tm-re-submit-form__group">
		<label for="tm-re-submitform__parking"><?php esc_html_e( 'Parking places', 'cherry-real-estate' ); ?></label>
		<input type="text" id="tm-re-submitform__parking" name="property_parking_places" value="">
	</div>

	<div class="tm-re-submit-form__group">
		<label for="tm-re-submitform__address"><?php esc_html_e( 'Address', 'cherry-real-estate' ) ?></label>
		<input type="text" id="tm-re-submitform__address" name="property_address" value="">
	</div>

	<?php if ( ! current_user_can( 'manage_properties' ) ) : ?>
		<div class="tm-re-submit-form__group">
			<label for="tm-re-submitform__email"><?php esc_html_e( 'Your e-mail', 'cherry-real-estate' ) ?></label>
			<input type="email" id="tm-re-submitform__email" name="agent_email" value="" required="required">
		</div>

		<div class="tm-re-submit-form__group">
			<label for="tm-re-submitform__phone"><?php esc_html_e( 'Your phone', 'cherry-real-estate' ) ?></label>
			<input type="text" id="tm-re-submitform__phone" name="agent_phone" value="" required="required">
		</div>

	<?php endif; ?>

	<div class="tm-re-submit-form__group">
		<button type="submit" class="tm-re-submit-form__btn"><?php esc_html_e( 'Submit', 'cherry-real-estate' ); ?></button>
	</div>

	<div class="tm-re-submit-form__messages">
		<div class="tm-re-submit-form__success tm-re-hidden">Success</div>
		<div class="tm-re-submit-form__error tm-re-hidden"></div>
	</div>

</form>

<?php // Enqueue a script.
cherry_re_enqueue_script( array( 'cherry-re-script' ) ); ?>
