<?php
/**
 * The Template for displaying controls for listing.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/misc/listing-controls.php.
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

if ( empty( $passed_vars ) ) {
	return;
}

$sort_control   = $passed_vars['sort_control'];
$layout_control = $passed_vars['layout_control']; ?>

<div class="tm-re_listing-controls">
	<?php echo $sort_control; ?>
	<?php echo $layout_control; ?>
</div>
