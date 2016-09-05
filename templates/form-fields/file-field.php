<?php
/**
 * Input File (upload).
 *
 * This template can be overridden by copying it to yourtheme/real-estate/form-fields/file-field.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

if ( ! is_user_logged_in() ) {
	return;
}

$field              = $passed_vars['field'];
$field_name         = $field['name'];
$field_name         .= ! empty( $field['multiple'] ) ? '[]' : '';
$allowed_mime_types = ! empty( $field['allowed_mime_types'] ) ? $field['allowed_mime_types'] : get_allowed_mime_types();
$allowed_mime_types = array_keys( $allowed_mime_types );
$multiple           = ! empty( $field['multiple'] ) ? 'multiple' : ''; ?>

<label for="property_gallery"><?php echo $field['label']; ?></label>
<div class="tm-re-submission-form__upload tm-re-uploaded">
	<div class="tm-re-uploaded-images"></div>
	<input type="hidden" class="tm-re-uploaded-ids" data-ids="[]" value="">

	<span class="tm-re-uploaded-btn">
		<span><?php echo esc_html__( 'Browse file', 'cherry-rea-estate' ); ?></span>
		<input type="file" class="tm-re-uploaded-btn__field" data-file_types="<?php echo esc_attr( implode( '|', $allowed_mime_types ) ); ?>" <?php echo $multiple; ?> name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $field['name'] ); ?>">
	</span>

	<small class="tm-re-submission-form__group-desc">
		<?php if ( ! empty( $field['description'] ) ) : ?>
			<?php echo $field['description']; ?>
		<?php else : ?>
			<?php printf( esc_html__( 'Maximum file size: %s.', 'cherry-real-estate' ), size_format( wp_max_upload_size() ) ); ?>
		<?php endif; ?>
	</small>
</div>

<?php // Enqueue a script.
cherry_re_enqueue_script( array( 'jquery-fileupload' ) ); ?>
