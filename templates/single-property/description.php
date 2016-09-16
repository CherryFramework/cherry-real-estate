<?php
/**
 * Single Property Description.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/single-property/description.php
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

$heading = esc_html( apply_filters(
	'cherry_re_property_description_heading',
	esc_html__( 'Property Description', 'cherry-real-estate' )
) ); ?>

<div class="tm-property__description">

	<?php if ( $heading ) : ?>
		<h2 class="tm-property__subtitle"><?php echo $heading; ?></h2>
	<?php endif; ?>

	<?php the_content(); ?>

</div>
