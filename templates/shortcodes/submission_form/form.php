<?php
/**
 * Submission Form view.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/shortcodes/submission_form/form.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */
do_action( 'cherry_re_before_submission_form' ); ?>

<form method="post" id="tm-re-submissionform" class="tm-re-submission-form" action="#" enctype="multipart/form-data">
	<?php wp_nonce_field( '_tm-re-submission-form', 'tm-re-submissionform-nonce' ); ?>

	<div class="tm-re-submission-form__group">
		<label for="property_title"><?php esc_html_e( 'Title', 'cherry-real-estate' ); ?></label>
		<input type="text" id="property_title" class="tm-re-submission-form__field" name="property_title" value="" placeholder="<?php esc_html_e( 'Enter your property title', 'cherry-real-estate' ); ?>" required="required">
	</div>

	<div class="tm-re-submission-form__group">
		<label for="property_description"><?php esc_html_e( 'Description', 'cherry-real-estate' ); ?></label>
		<textarea id="property_description" name="property_description" required="required"></textarea>
	</div>

	<div class="tm-re-submission-form__group">
		<label for="property_price"><?php printf( esc_html__( 'Price, %s', 'cherry-real-estate' ), Model_Settings::get_currency_symbol() ); ?></label>
		<input type="text" id="property_price" name="property_price" value="" placeholder="<?php esc_html_e( 'Enter price', 'cherry-real-estate' ); ?>" required="required">
	</div>

	<?php $select_status = Cherry_RE_Tools::select_form( Model_Properties::get_allowed_property_statuses(), array(
			'id'      => 'property_status',
			'name'    => 'property_status',
			'default' => esc_html__( 'Choose', 'cherry-real-estate' ),
			'echo'    => false,
		) ); ?>

	<?php if ( ! empty( $select_status ) ) { ?>

		<div class="tm-re-submission-form__group">
			<label for="property_status"><?php esc_html_e( 'Status', 'cherry-real-estate' ); ?></label>
			<?php echo $select_status; ?>
		</div>

	<?php } ?>

	<?php $select_types = Cherry_RE_Tools::select_form( Model_Properties::get_property_types( 'id' ), array(
			'id'      => 'property_type',
			'name'    => 'property_type',
			'default' => esc_html__( 'Choose', 'cherry-real-estate' ),
			'echo'    => false,
		) ); ?>

	<?php if ( ! empty( $select_types ) ) { ?>

		<div class="tm-re-submission-form__group">
			<label for="property_type"><?php esc_html_e( 'Type', 'cherry-real-estate' ); ?></label>
			<?php echo $select_types; ?>
		</div>

	<?php } ?>

	<div class="tm-re-submission-form__group">
		<label for="property_bedrooms"><?php esc_html_e( 'Bedrooms', 'cherry-real-estate' ); ?></label>
		<input type="text" id="property_bedrooms" name="property_bedrooms" value="" placeholder="<?php esc_html_e( 'Enter number of bedrooms', 'cherry-real-estate' ); ?>">
	</div>

	<div class="tm-re-submission-form__group">
		<label for="property_bathrooms"><?php esc_html_e( 'Bathrooms', 'cherry-real-estate' ); ?></label>
		<input type="text" id="property_bathrooms" name="property_bathrooms" value="" placeholder="<?php esc_html_e( 'Enter number of bathrooms', 'cherry-real-estate' ); ?>">
	</div>

	<div class="tm-re-submission-form__group">
		<label for="property_area"><?php printf( esc_html__( 'Area Size, %s', 'cherry-real-estate' ), Model_Settings::get_area_unit_title() ); ?></label>
		<input type="text" id="property_area" name="property_area" value="" placeholder="<?php esc_html_e( 'Enter property area size', 'cherry-real-estate' ); ?>">
	</div>

	<div class="tm-re-submission-form__group">
		<label for="property_parking_places"><?php esc_html_e( 'Parking places', 'cherry-real-estate' ); ?></label>
		<input type="text" id="property_parking_places" name="property_parking_places" value="" placeholder="<?php esc_html_e( 'Enter number of parking places', 'cherry-real-estate' ); ?>">
	</div>

	<div class="tm-re-submission-form__group">
		<label for="property_address"><?php esc_html_e( 'Address', 'cherry-real-estate' ) ?></label>
		<input type="text" id="property_address" name="property_address" value="" required="required" placeholder="<?php esc_html_e( 'Enter your property address', 'cherry-real-estate' ); ?>">
	</div>

	<?php $file_field = cherry_re_get_template_html( 'form-fields/file-field', array(
		'field' => array(
			'label'              => esc_html__( 'Gallery', 'cherry-real-estate' ),
			'name'               => 'property_gallery',
			'multiple'           => true,
			'allowed_mime_types' => Model_Submit_Form::allowed_image_types(),
			'value'              => '',
		),
	) );

	if ( ! empty( $file_field ) ) { ?>
		<div class="tm-re-submission-form__group">
			<?php echo $file_field; ?>
		</div>
	<?php } ?>

	<?php do_action( 'cherry_re_before_submission_form_btn' ); ?>

	<div class="tm-re-submission-form__group">
		<button type="submit" class="tm-re-submission-form__btn" <?php disabled( is_user_logged_in(), false, true ); ?>><?php esc_html_e( 'Submit', 'cherry-real-estate' ); ?></button>
	</div>

	<?php do_action( 'cherry_re_after_submission_form_btn' ); ?>

	<div class="tm-re-submission-form__messages tm-re-status">
		<div class="tm-re-submission-form__process tm-re-status__item tm-re-status--process tm-re-hidden">
			<svg class="circular" viewBox="25 25 50 50">
				<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10"/>
			</svg>
		</div>

		<div class="tm-re-submission-form__success tm-re-hidden"><?php esc_html_e( 'Success', 'cherry-real-estate' ) ?></div>
		<div class="tm-re-submission-form__error tm-re-hidden"></div>
	</div>

</form>

<?php do_action( 'cherry_re_after_submission_form' ); ?>

<?php // Enqueue a scripts.
cherry_re_enqueue_script( array( 'jquery-validate', 'cherry-re-script' ) ); ?>
