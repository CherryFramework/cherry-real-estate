<?php
/**
 * Uploaded Image (preview).
 *
 * This template can be overridden by copying it to yourtheme/real-estate/form-fields/uploaded-file-html.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */
?>

<div class="tm-re-uploaded-images__item">
	<div class="tm-re-uploaded-image__preview">
		<a class="tm-re-uploaded-image__remove" href="#"></a>
	</div>
	<div class="tm-re-uploaded-image__name"></div>
	<button type="button" class="tm-re-uploaded-image__btn btn btn-upload" disabled="disabled"><?php echo esc_html__( 'Upload', 'cherry-real-estate' ); ?></button>

	<div class="tm-re-status">
		<div class="tm-re-status__item tm-re-status--process tm-re-hidden">
			<svg class="circular" viewBox="25 25 50 50">
				<circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="5" stroke-miterlimit="10"/>
			</svg>
		</div>

		<div class="tm-re-status__item tm-re-status--success tm-re-hidden"></div>
		<div class="tm-re-status__item tm-re-status--error tm-re-hidden"></div>
	</div>

</div>
