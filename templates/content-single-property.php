<?php
/**
 * The template for displaying property content in the single-property.php template.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/content-single-propery.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}

$property_callbacks = cherry_re_templater()->setup_template_data(); ?>

<article id="tm-property-<?php the_ID(); ?>" <?php post_class( 'tm-property-item' ); ?>>

	<?php
		/**
		 * Fires before single property summary.
		 *
		 * @since 1.0.0
		 *
		 * @hooked Cherry_RE_Template_Callbacks::the_property_data - 0
		 * @hooked cherry_real_estate_template_single_title - 10
		 * @hooked cherry_real_estate_show_property_gallery - 15
		 */
		do_action( 'cherry_re_before_single_property_summary', $property_callbacks );
	?>

	<div class="tm-property__summary entry-summary">

		<?php
			/**
			 * Fires before single property summary are printed.
			 *
			 * @since 1.0.0
			 *
			 * @hooked cherry_real_estate_template_single_price - 5
			 * @hooked cherry_real_estate_property_description  - 10
			 * @hooked cherry_real_estate_property_attributes   - 15
			 */
			do_action( 'cherry_re_single_property_summary', $property_callbacks );
		?>

	</div><!-- .tm-property__summary -->

	<?php
		/**
		 * Fires after single property summary.
		 *
		 * @since 1.0.0
		 *
		 * @hooked cherry_real_estate_property_map - 5
		 * @hooked cherry_real_estate_property_agent - 10
		 * @hooked Cherry_RE_Template_Callbacks::clear_data - 999999
		 */
		do_action( 'cherry_re_after_single_property_summary', $property_callbacks );
	?>

</article><!-- #tm-property-<?php the_ID(); ?> -->
