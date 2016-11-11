<?php
/**
 * Related Properties.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/single-property/related.php
 *
 * @package    Cherry_Real_Estate
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

$heading = esc_html( apply_filters(
	'cherry_re_property_related_heading',
	esc_html__( 'Related Properties', 'cherry-real-estate' )
) );

$property_id    = get_the_ID();
$related_by     = Model_Settings::get_related_by();
$related_amount = absint( Model_Settings::get_related_amount() );

if ( ! in_array( $related_by, array( 'price', 'author' ) ) ) {
	$related_by = 'author';
}

$query_args = array(
	'post__not_in'        => array( $property_id ),
	'ignore_sticky_posts' => true,
	'number'              => $related_amount,
	'echo'                => false,
	'item_class'          => 'tm-property-related__item',
	'css_class'           => 'tm-property-items tm-property-related__wrap tm-property__wrap--related',
	'template'            => 'related.tmpl',
);

if ( 'price' == $related_by ) {

	$price_range = (int) Model_Settings::get_related_price_range();
	$min_range   = ( 100 - $price_range ) / 100; // -$price_range (%)
	$max_range   = ( 100 + $price_range ) / 100; // +$price_range (%)
	$price_key   = cherry_real_estate()->get_meta_prefix() . 'price';

	$meta_query = array( array(
		'relation' => 'OR',
		array(
			'key'   => $price_key,
			'value' => array(
				(float) get_post_meta( $property_id, $price_key, true ) * $min_range,
				(float) get_post_meta( $property_id, $price_key, true ) * $max_range,
			),
			'compare' => 'BETWEEN',
			'type'    => 'DECIMAL',
		),
	) );

	$query_args = wp_parse_args( $query_args, array(
		'meta_query' => $meta_query,
	) );

} else {
	$query_args = wp_parse_args( $query_args, array(
		'author' => get_the_author_meta( 'ID' ),
	) );
}

$data       = Cherry_RE_Property_Data::get_instance();
$query_args = apply_filters( 'cherry_re_property_related_args', $query_args );
$related    = $data->the_property( $query_args );

if ( ! $related ) {
	return;
} ?>

<div class="tm-property-related">

	<?php if ( $heading ) : ?>
		<h2 class="tm-property-related__title tm-property__subtitle"><?php echo $heading; ?></h2>
	<?php endif; ?>

	<?php echo $related; ?>

</div>
