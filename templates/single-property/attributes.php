<?php
/**
 * Single Property Attributes.
 *
 * This template can be overridden by copying it to yourtheme/real-estate/single-property/attributes.php
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

$callbacks = $passed_vars['callbacks'];
$heading   = esc_html( apply_filters(
	'cherry_re_property_attributes_heading',
	esc_html__( 'Quick Summary', 'cherry-real-estate' )
) ); ?>

<div class="tm-property__attributes">

	<?php if ( $heading ) : ?>
		<h2 class="tm-property__subtitle"><?php echo $heading; ?></h2>
	<?php endif;

	// Prepare attributes.
	$atts = array();
	$args = array( 'wrap' => 'dd' );

	$property_id    = get_the_ID();
	$status         = $callbacks->get_property_status( $args );
	$location       = $callbacks->get_property_location( $args );
	$bedrooms       = $callbacks->get_property_bedrooms( $args );
	$bathrooms      = $callbacks->get_property_bathrooms( $args );
	$area           = $callbacks->get_property_area( $args );
	$parking_places = $callbacks->get_property_parking_places( $args );

	$types = Model_Properties::get_property_term_list(
		'type',
		$property_id,
		apply_filters( 'cherry_re_single_property_types_list_args', array() )
	);

	$features = Model_Properties::get_property_term_list(
		'feature',
		$property_id,
		apply_filters( 'cherry_re_single_property_features_list_args', array(
			'before' => '<p>',
			'after'  => '</p>',
			'sep'    => '</p><p>',
		) )
	);

	$atts['id'] = array(
		'label' => esc_html__( 'Property ID:', 'cherry-real-estate' ),
		'value' => '<dd>' . $property_id . '</dd>',
	);

	if ( $status ) {
		$atts['status'] = array(
			'label' => esc_html__( 'Property status:', 'cherry-real-estate' ),
			'value' => $status,
		);
	}

	if ( $types ) {
		$atts['types'] = array(
			'label' => esc_html__( 'Property type:', 'cherry-real-estate' ),
			'value' => '<dd>' . $types . '</dd>',
		);
	}

	if ( $location ) {
		$atts['location'] = array(
			'label' => esc_html__( 'Location:', 'cherry-real-estate' ),
			'value' => $location,
		);
	}

	if ( $bedrooms ) {
		$atts['bedrooms'] = array(
			'label' => esc_html__( 'Bedrooms:', 'cherry-real-estate' ),
			'value' => $bedrooms,
		);
	}

	if ( $bathrooms ) {
		$atts['bathrooms'] = array(
			'label' => esc_html__( 'Bathrooms:', 'cherry-real-estate' ),
			'value' => $bathrooms,
		);
	}

	if ( $area ) {
		$atts['area'] = array(
			'label' => esc_html__( 'Area:', 'cherry-real-estate' ),
			'value' => $area,
		);
	}

	if ( $parking_places ) {
		$atts['parking_places'] = array(
			'label' => esc_html__( 'Parking place:', 'cherry-real-estate' ),
			'value' => $parking_places,
		);
	}

	if ( $features ) {
		$atts['features'] = array(
			'label' => esc_html__( 'Property Features:', 'cherry-real-estate' ),
			'value' => '<dd>' . $features . '</dd>',
		);
	}

	$atts = apply_filters( 'cherry_re_single_property_attributes', $atts, $args ); ?>

	<dl class="tm-property__attributes-list">

		<?php foreach ( (array) $atts as $name => $attr ) { ?>
			<dt class="tm-property__attribute-item tm-property__attribute-<?php echo sanitize_html_class( $name ); ?>"><?php echo $attr['label']; ?></dt>
			<?php echo $attr['value']; ?>
		<?php } ?>

	</dl>

</div>
