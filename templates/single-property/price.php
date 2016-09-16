<?php
/**
 * Single Prorepty price.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/single-property/price.php
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

if ( empty( $passed_vars['callbacks'] ) ) {
	return;
}

$callbacks = $passed_vars['callbacks']; ?>

<div class="tm-property__price">
	<h2 class="tm-property__subtitle">
		<?php $price = $callbacks->get_property_price( apply_filters( 'cherry_re_single_property_price_args', array(
				'wrap'  => 'span',
				'class' => 'tm-property__price',
			) ) );

			if ( '' != $price ) {
				printf( esc_html__( 'Price: %s', 'cherry-real-estate' ), $price );
			}
		?>
	</h2>
</div>
