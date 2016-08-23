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
	<div class="tm-re-uploaded-image__progress tm-re-hidden"></div>
</div>
